<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ url('/about') }}</loc>
    <priority>0.3</priority>
  </url>
@foreach ($companyNames as $companyName)
  <url>
    <loc>{{ route('companies.show', ['companyName' => $companyName]) }}</loc>
    <priority>0.7</priority>
  </url>
@endforeach
</urlset>
