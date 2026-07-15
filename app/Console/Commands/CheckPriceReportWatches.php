<?php

namespace App\Console\Commands;

use App\Models\PriceReport;
use App\Models\Watch;
use App\Support\LineMessaging;
use Illuminate\Console\Command;

class CheckPriceReportWatches extends Command
{
    protected $signature = 'reports:check-watches';

    protected $description = 'ウォッチ登録された引越し業者に新しい相見積もり額の口コミが投稿されていないか確認し、LINEで通知する';

    public function handle(): int
    {
        $watches = Watch::with('lineUser')->get();

        foreach ($watches as $watch) {
            if (! $watch->lineUser) {
                continue;
            }

            $since = $watch->last_checked_report_id ?? 0;
            $newReports = PriceReport::where('company_name', $watch->company_name)
                ->where('id', '>', $since)
                ->get();

            if ($newReports->isEmpty()) {
                continue;
            }

            $latest = $newReports->sortByDesc('id')->first();
            LineMessaging::push(
                $watch->lineUser->line_user_id,
                "「{$watch->company_name}」の新しい相見積もり額の口コミが投稿されました: " . number_format($latest->total_price) . '円'
            );

            // last_checked_report_idは検知カーソル。idは常に厳密単調増加のため、
            // created_at(秒精度)を使った場合に起こりうる同一秒内の複数投稿の取りこぼしが起きない。
            $watch->update(['last_checked_report_id' => $newReports->max('id')]);
        }

        $this->info("チェック完了: {$watches->count()}件のウォッチを確認しました。");

        return self::SUCCESS;
    }
}
