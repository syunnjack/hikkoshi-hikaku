<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class MovingCompanySeeder extends Seeder
{
    /**
     * 引越安心マークの認定事業者を取り込む。
     *
     * 元データは scripts/build-company-data.py が database/data/moving-companies.json に
     * 書き出す。認定は数年ごとに入れ替わるため、作り直して流し直せば内容が揃う。
     */
    private const CHUNK = 30; // SQLiteのプレースホルダ上限（既定999）に収まる大きさ

    public function run(): void
    {
        $path = database_path('data/moving-companies.json');

        if (! File::exists($path)) {
            throw new RuntimeException('database/data/moving-companies.json が見つかりません。');
        }

        $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        $companies = $payload['companies'] ?? [];
        $confirmedOn = $payload['confirmedOn'] ?? null;

        if ($companies === []) {
            throw new RuntimeException('業者データが空です。');
        }

        $now = now();
        $written = 0;

        foreach (array_chunk($companies, self::CHUNK) as $chunk) {
            $rows = [];

            foreach ($chunk as $company) {
                $rows[] = [
                    'name' => $company['name'],
                    'kana_column' => $company['kana_column'],
                    'certificate_url' => $company['certificate_url'],
                    'source_url' => $company['source_url'],
                    'confirmed_on' => $confirmedOn,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('moving_companies')->upsert(
                $rows,
                ['name'],
                ['kana_column', 'certificate_url', 'source_url', 'confirmed_on', 'updated_at']
            );

            $written += count($rows);
        }

        // 認定から外れた事業者は掲載からも下げる（今回の確認日が付かなかった行）。
        $removed = DB::table('moving_companies')
            ->where(function ($query) use ($confirmedOn) {
                $query->whereNull('confirmed_on')->orWhere('confirmed_on', '!=', $confirmedOn);
            })
            ->delete();

        $this->command?->info(number_format($written).'社を取り込みました（'.$confirmedOn.'時点の認定事業者）。'
            .($removed > 0 ? number_format($removed).'社を掲載から外しました。' : ''));
    }
}
