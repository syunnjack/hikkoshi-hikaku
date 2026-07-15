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
@foreach ($companies as $company)
  <url>
    <loc>{{ url('/search?company_name=' . urlencode($company->company_name)) }}</loc>
    <priority>0.7</priority>
  </url>
@endforeach
</urlset>
