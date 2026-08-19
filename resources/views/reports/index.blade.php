@extends('layouts.app')

@section('title', config('app.name') . ' | 実際の相見積もり額の口コミで引越し業者の相場がわかる')
@section('description', '実際に引越しをした人が投稿する相見積もり額の口コミサイトです。業者ごとの相場を確認でき、新しいレポートが投稿されるとLINEで通知を受け取れます。')

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => config('app.name'),
    'url' => url('/'),
    'description' => '実際に引越しをした人が投稿する相見積もり額の口コミサイト。',
    'inLanguage' => 'ja',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container">
  <h1>実際の相見積もり額の口コミで引越し業者の相場を調べる</h1>
  <p class="text-muted">
    {{ config('app.name') }}では、実際に引越しをした人が投稿した「実際にいくらだったか」の口コミから、業者ごとのリアルな相場を確認できます。
    一括見積もりサイトの概算とは違う、実際の契約結果を集めています。
  </p>

  @if (session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
  @endif

  <form method="GET" action="{{ route('reports.search') }}" class="row g-2 mb-4">
    <div class="col-9 col-md-10">
      <input type="text" name="company_name" class="form-control" placeholder="例：アート引越センター、サカイ引越センターなど" required>
    </div>
    <div class="col-3 col-md-2">
      <button type="submit" class="btn btn-primary w-100">検索</button>
    </div>
  </form>

  @if($recentCompanies->isNotEmpty())
    <h2 class="h5">投稿の多い業者</h2>
    <div class="row row-cols-2 row-cols-md-4 g-2 mt-1 mb-4">
      @foreach($recentCompanies as $company)
        <div class="col">
          <a href="{{ route('companies.show', ['companyName' => $company->company_name]) }}" class="btn btn-outline-primary w-100">
            {{ $company->company_name }}（{{ $company->reports_count }}件）
          </a>
        </div>
      @endforeach
    </div>
  @endif

  @if($companies->isNotEmpty())
    <section class="mt-4">
      <h2 class="h5">引越安心マークの認定事業者から探す（{{ number_format($companies->count()) }}社）</h2>
      <p class="text-muted small">
        全日本トラック協会が「引越安心マーク」を認定している引越業者の一覧です。
        業者名を選ぶと、その業者の相見積もり額の口コミと、認定の内容を確認できます。
      </p>
      @foreach($companiesByColumn as $column => $group)
        <h3 class="h6 mt-3">{{ $column }}（{{ $group->count() }}社）</h3>
        <div class="d-flex flex-wrap gap-2">
          @foreach($group as $company)
            <a href="{{ route('companies.show', ['companyName' => $company->name]) }}" class="btn btn-sm btn-outline-secondary">{{ $company->name }}</a>
          @endforeach
        </div>
      @endforeach
      <p class="text-muted small mt-3">
        出典：<a href="{{ $companies->first()->source_url }}" target="_blank" rel="noopener">全日本トラック協会「引越安心マーク制度 認定事業者一覧」</a>
        （{{ optional($companies->first()->confirmed_on)->format('Y年n月j日') }}時点）
      </p>
    </section>
  @endif

  <section class="mt-5 pt-4 border-top">
    <h2 class="h5">相見積もり額を投稿する</h2>
    <p class="text-muted small">引越しをした経験がある方は、業者ページの「相見積もり額を投稿する」から投稿できます。金額は実際に契約した額を入れてください。</p>
  </section>
</div>
@endsection
