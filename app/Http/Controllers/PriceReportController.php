<?php

namespace App\Http\Controllers;

use App\Models\PriceReport;
use App\Models\Watch;
use App\Support\ContentModeration;
use Illuminate\Http\Request;

class PriceReportController extends Controller
{
    public function index()
    {
        $recentCompanies = PriceReport::select('company_name')
            ->selectRaw('COUNT(*) as reports_count')
            ->groupBy('company_name')
            ->orderByDesc('reports_count')
            ->take(12)
            ->get();

        return view('reports.index', compact('recentCompanies'));
    }

    public function search(Request $request)
    {
        $companyName = trim($request->input('company_name', ''));
        if ($companyName === '') {
            return redirect()->route('reports.index');
        }

        $reports = PriceReport::where('company_name', $companyName)->latest()->get();
        $averagePrice = $reports->count() > 0 ? (int) round($reports->avg('total_price')) : null;

        $isWatching = session('line_user_local_id')
            ? Watch::where('line_user_id', session('line_user_local_id'))->where('company_name', $companyName)->exists()
            : false;

        return view('reports.results', compact('companyName', 'reports', 'averagePrice', 'isWatching'));
    }

    public function store(Request $request)
    {
        if (! empty($request->input('website'))) {
            return redirect()->route('reports.index')->with('success', '投稿を受け付けました。');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:100',
            'move_type' => 'nullable|string|max:20',
            'distance_range' => 'nullable|string|max:20',
            'total_price' => 'required|integer|min:1000|max:10000000',
            'comment' => 'nullable|string|max:1000',
            'nickname' => 'nullable|string|max:30',
        ]);

        if (! empty($validated['comment']) && ContentModeration::containsNgWord($validated['comment'])) {
            return back()->withErrors(['comment' => '投稿内容に使用できない文字列が含まれています。'])->withInput();
        }

        $ipHash = ContentModeration::clientIpHash($request);
        if (ContentModeration::isTooSoon("price-report:{$ipHash}", 30)) {
            return back()->withErrors(['total_price' => '投稿間隔が短すぎます。しばらく待ってから再度お試しください。'])->withInput();
        }

        PriceReport::create([
            'company_name' => $validated['company_name'],
            'move_type' => $validated['move_type'] ?? null,
            'distance_range' => $validated['distance_range'] ?? null,
            'total_price' => $validated['total_price'],
            'comment' => $validated['comment'] ?? null,
            'nickname' => ($validated['nickname'] ?? '') !== '' ? $validated['nickname'] : '匿名',
            'ip_hash' => $ipHash,
        ]);

        return redirect()->route('reports.search', ['company_name' => $validated['company_name']])
            ->with('success', '相見積もり額の口コミを投稿しました。ありがとうございます。');
    }

    public function sitemap()
    {
        $companies = PriceReport::select('company_name')->distinct()->get();
        $xml = view('sitemap', compact('companies'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
