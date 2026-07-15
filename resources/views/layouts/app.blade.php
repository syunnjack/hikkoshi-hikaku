<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name') . ' | 実際の相見積もり額の口コミで引越し業者の相場がわかる')</title>
    <meta name="description" content="@yield('description', '実際に引越しをした人が投稿する相見積もり額の口コミサイトです。業者ごとの相場を確認でき、新しいレポートが投稿されるとLINEで通知を受け取れます。')">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', config('app.name') . ' | 実際の相見積もり額の口コミで引越し業者の相場がわかる')">
    <meta property="og:description" content="@yield('description', '実際に引越しをした人が投稿する相見積もり額の口コミサイトです。業者ごとの相場を確認でき、新しいレポートが投稿されるとLINEで通知を受け取れます。')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="ja_JP">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', config('app.name') . ' | 実際の相見積もり額の口コミで引越し業者の相場がわかる')">
    <meta name="twitter:description" content="@yield('description', '実際に引越しをした人が投稿する相見積もり額の口コミサイトです。業者ごとの相場を確認でき、新しいレポートが投稿されるとLINEで通知を受け取れます。')">

    <link rel="icon" href="/favicon.ico" sizes="any">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .btn-line { background: #06c755; color: #fff; border: none; }
      .btn-line:hover { background: #05a848; color: #fff; }
    </style>

    @stack('structured-data')
</head>
<body>
    <nav class="navbar navbar-dark bg-dark text-white p-3 mb-4">
        <div class="container">
            <a href="{{ route('reports.index') }}" class="h4 mb-0 text-white text-decoration-none">🚚 {{ config('app.name') }}</a>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

    <footer class="container text-center text-muted small py-4 mt-4 border-top">
        <a href="{{ route('about') }}" class="text-muted">このサイトについて</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
