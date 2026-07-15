<?php

namespace App\Http\Controllers;

use App\Models\PriceReport;
use App\Models\Watch;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
        ]);
        $companyName = $validated['company_name'];

        $lineUserLocalId = $request->session()->get('line_user_local_id');

        if (! $lineUserLocalId) {
            return redirect()->route('line.login', ['company_name' => $companyName]);
        }

        $watch = Watch::where('line_user_id', $lineUserLocalId)
            ->where('company_name', $companyName)
            ->first();

        if ($watch) {
            $watch->delete();

            return back()->with('success', 'ウォッチを解除しました。');
        }

        Watch::create([
            'line_user_id' => $lineUserLocalId,
            'company_name' => $companyName,
            'last_checked_report_id' => PriceReport::where('company_name', $companyName)->max('id') ?? 0,
        ]);

        return back()->with('success', '新しい相見積もり額の口コミが投稿されるとLINEでお知らせします。');
    }
}
