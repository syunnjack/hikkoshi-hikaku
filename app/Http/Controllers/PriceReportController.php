<?php

namespace App\Http\Controllers;

use App\Models\MovingCompany;
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

        $companies = MovingCompany::orderBy('name')->get();
        $companiesByColumn = $companies
            ->groupBy('kana_column')
            ->sortBy(fn ($group, string $column) => array_search($column, MovingCompany::COLUMN_ORDER, true));

        return view('reports.index', compact('recentCompanies', 'companies', 'companiesByColumn'));
    }

    /** 検索フォームからの遷移。業者ページの正しいURLへ送る。 */
    public function search(Request $request)
    {
        $companyName = trim((string) $request->input('company_name', ''));

        if ($companyName === '') {
            return redirect()->route('reports.index');
        }

        return redirect()->route('companies.show', ['companyName' => $companyName], 301);
    }

    public function show(string $companyName)
    {
        $companyName = trim($companyName);

        if ($companyName === '') {
            return redirect()->route('reports.index');
        }

        $company = MovingCompany::where('name', $companyName)->first();
        $reports = PriceReport::where('company_name', $companyName)->latest()->get();

        // 認定事業者でもなく、口コミも無い業者名は、中身の無いページになるため出さない。
        if ($company === null && $reports->isEmpty()) {
            abort(404);
        }

        $averagePrice = $reports->count() > 0 ? (int) round($reports->avg('total_price')) : null;

        $isWatching = session('line_user_local_id')
            ? Watch::where('line_user_id', session('line_user_local_id'))->where('company_name', $companyName)->exists()
            : false;

        $relatedCompanies = $company
            ? MovingCompany::where('kana_column', $company->kana_column)
                ->where('id', '!=', $company->id)
                ->orderBy('name')
                ->take(12)
                ->get()
            : collect();

        return view('reports.results', compact(
            'companyName', 'company', 'reports', 'averagePrice', 'isWatching', 'relatedCompanies'
        ));
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
        // 認定事業者と、口コミが投稿された業者の両方を載せる。
        $names = MovingCompany::pluck('name')
            ->merge(PriceReport::select('company_name')->distinct()->pluck('company_name'))
            ->unique()
            ->sort()
            ->values();

        $xml = view('sitemap', ['companyNames' => $names])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
