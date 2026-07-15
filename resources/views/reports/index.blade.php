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
          <a href="{{ route('reports.search', ['company_name' => $company->company_name]) }}" class="btn btn-outline-primary w-100">
            {{ $company->company_name }}（{{ $company->reports_count }}件）
          </a>
        </div>
      @endforeach
    </div>
  @endif

  <section class="mt-5 pt-4 border-top">
    <h2 class="h5">相見積もり額を投稿する</h2>
    <p class="text-muted small">引越しをした経験がある方は、下記の検索から業者ページに移動して「相見積もり額を投稿する」から投稿できます。</p>
  </section>
</div>
@endsection
