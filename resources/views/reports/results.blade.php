@extends('layouts.app')

@section('title', $companyName . 'の相見積もり額・口コミ | ' . config('app.name'))
@section('description', $companyName . 'を実際に利用した人の相見積もり額の口コミ一覧です。' . ($averagePrice ? '平均額は' . number_format($averagePrice) . '円です。' : ''))

@push('structured-data')
<script type="application/ld+json">
{!! json_encode([
    '@@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => config('app.name'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $companyName . 'の相場'],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">{{ config('app.name') }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $companyName }}</li>
    </ol>
  </nav>

  <h1>{{ $companyName }}の相見積もり額・口コミ</h1>

  @if (session('success'))
    <div class="alert alert-success py-2">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
  @endif

  @if($averagePrice)
    <p class="fs-5">平均額: <strong>{{ number_format($averagePrice) }}円</strong>（{{ $reports->count() }}件の口コミより）</p>
  @else
    <p class="text-muted">まだ「{{ $companyName }}」の口コミがありません。最初のレポートを投稿してみませんか？</p>
  @endif

  <form method="POST" action="{{ route('watches.toggle') }}" class="mb-4">
    @csrf
    <input type="hidden" name="company_name" value="{{ $companyName }}">
    @if ($isWatching)
      <button type="submit" class="btn btn-outline-secondary btn-sm">🔕 ウォッチをやめる</button>
    @else
      {{-- LINEの認証情報が未設定のうちは、押すとLINE側でエラーになるので出さない --}}
      @if (config('services.line.login_channel_id'))
      <button type="submit" class="btn btn-line btn-sm">🔔 新しい口コミが投稿されたらLINEで通知</button>
      @else
        <button type="button" class="btn btn-secondary" disabled>🔔 新しい口コミが投稿されたらLINEで通知（準備中）</button>
      @endif
    @endif
  </form>

  <section class="mb-5">
    <h2 class="h5">口コミ一覧</h2>
    @forelse($reports as $report)
      <div class="border rounded p-3 mb-2">
        <div class="d-flex justify-content-between">
          <strong>{{ number_format($report->total_price) }}円</strong>
          <span class="text-muted small">{{ $report->created_at->format('Y-m-d') }}</span>
        </div>
        <div class="small text-muted">
          {{ $report->move_type }}
          {{ $report->distance_range ? ' / ' . $report->distance_range : '' }}
          / {{ $report->nickname }}
        </div>
        @if($report->comment)
          <p class="mb-0 mt-1">{{ $report->comment }}</p>
        @endif
      </div>
    @empty
      <p class="text-muted">まだ口コミがありません。</p>
    @endforelse
  </section>

  <section class="mt-4 pt-4 border-top">
    <h2 class="h5">相見積もり額を投稿する</h2>
    <form method="POST" action="{{ route('reports.store') }}" class="bg-light p-3 rounded">
      @csrf
      <input type="hidden" name="company_name" value="{{ $companyName }}">
      <div style="position:absolute;left:-9999px;" aria-hidden="true">
        <label>ウェブサイト <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
      </div>

      <div class="row">
        <div class="col-6 mb-2">
          <label class="form-label small">引越し形態（任意）</label>
          <select name="move_type" class="form-select form-select-sm">
            <option value="">選択してください</option>
            <option value="単身">単身</option>
            <option value="家族">家族</option>
            <option value="法人・オフィス">法人・オフィス</option>
          </select>
        </div>
        <div class="col-6 mb-2">
          <label class="form-label small">移動距離の目安（任意）</label>
          <select name="distance_range" class="form-select form-select-sm">
            <option value="">選択してください</option>
            <option value="同一市区町村内">同一市区町村内</option>
            <option value="同一都道府県内">同一都道府県内</option>
            <option value="近隣県">近隣県</option>
            <option value="長距離（遠方）">長距離（遠方）</option>
          </select>
        </div>
      </div>
      <div class="mb-2">
        <label class="form-label small">実際の契約額（円） <span class="text-danger">*</span></label>
        <input type="number" name="total_price" class="form-control form-control-sm" min="0" required>
      </div>
      <div class="mb-2">
        <label class="form-label small">ニックネーム（任意）</label>
        <input type="text" name="nickname" class="form-control form-control-sm" maxlength="30">
      </div>
      <div class="mb-2">
        <label class="form-label small">コメント（任意）</label>
        <textarea name="comment" class="form-control form-control-sm" rows="3" maxlength="1000" placeholder="例：他社より3万円安く交渉できました。"></textarea>
      </div>
      <button type="submit" class="btn btn-dark">投稿する</button>
    </form>
  </section>
</div>
@endsection
