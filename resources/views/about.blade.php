@extends('layouts.app')

@section('title', 'このサイトについて | ' . config('app.name'))
@section('description', config('app.name') . 'の運営方針、投稿内容の取り扱い、LINE通知の仕組みについて説明しています。')

@section('content')
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">{{ config('app.name') }}</a></li>
      <li class="breadcrumb-item active" aria-current="page">このサイトについて</li>
    </ol>
  </nav>

  <h1>このサイトについて</h1>

  <section class="mb-4">
    <h2 class="h5">サイトの目的</h2>
    <p>
      「{{ config('app.name') }}」は、実際に引越しをした人が投稿する「実際にいくらだったか」の口コミから、引越し業者ごとの実際の相場を確認できるサイトです。
      一括見積もりサイトの概算額とは違う、実際の契約結果に基づく情報を集めています。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h5">掲載データについて</h2>
    <p>
      掲載している契約額・引越し形態・移動距離・コメントは、すべて利用者からの投稿によるものです。運営による事実確認は行っておらず、
      投稿内容の正確性を保証するものではありません。季節（繁忙期・閑散期）や交渉の有無によって金額は大きく変動するため、あくまで参考情報としてご利用ください。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h5">LINE通知について</h2>
    <p>
      業者ページから「🔔 新しい口コミが投稿されたらLINEで通知」を選ぶと、LINEログインのうえその業者をウォッチ登録できます。
      登録した業者に新しい相見積もり額の口コミが投稿されると、LINE公式アカウントからお知らせします。
    </p>
  </section>

  <section class="mb-4">
    <h2 class="h5">投稿について</h2>
    <p>
      投稿は、どなたでもログイン不要で行えます。投稿内容は運営による事前確認を行わず即時公開されますが、
      不適切な投稿を発見された場合は内容を精査のうえ対応します。
    </p>
  </section>
</div>
@endsection
