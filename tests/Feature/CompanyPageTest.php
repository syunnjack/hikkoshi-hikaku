<?php

namespace Tests\Feature;

use App\Models\MovingCompany;
use App\Models\PriceReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_page_lists_certified_companies(): void
    {
        $this->company();

        $this->get('/')
            ->assertOk()
            ->assertSee('アート引越センター')
            ->assertSee('引越安心マーク');
    }

    public function test_company_page_shows_certification_with_its_source(): void
    {
        $company = $this->company();

        $this->get('/company/'.rawurlencode($company->name))
            ->assertOk()
            ->assertSee('アート引越センターの相見積もり額・口コミ')
            ->assertSee('引越安心マーク')
            ->assertSee('jta.or.jp', false);
    }

    public function test_company_page_shows_posted_reports(): void
    {
        $company = $this->company();
        PriceReport::create([
            'company_name' => $company->name,
            'total_price' => 88000,
            'nickname' => '匿名',
            'ip_hash' => 'test',
        ]);

        $this->get('/company/'.rawurlencode($company->name))
            ->assertOk()
            ->assertSee('88,000');
    }

    public function test_search_redirects_to_the_company_page(): void
    {
        $company = $this->company();

        $this->get('/search?company_name='.urlencode($company->name))
            ->assertRedirect(route('companies.show', ['companyName' => $company->name]));
    }

    public function test_unknown_company_without_reports_is_not_found(): void
    {
        $this->get('/company/'.rawurlencode('存在しない引越社'))->assertNotFound();
    }

    public function test_sitemap_lists_company_pages(): void
    {
        $company = $this->company();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('companies.show', ['companyName' => $company->name]), false);
    }

    private function company(): MovingCompany
    {
        return MovingCompany::create([
            'name' => 'アート引越センター',
            'kana_column' => 'あ行',
            'certificate_url' => 'https://jta.or.jp/pdf/hikkoshi_anshin/column2025/jigyosha/23-0111.pdf',
            'source_url' => 'https://jta.or.jp/member/hikkoshi_member/hikkoshi_anshin/list/column2025_a.html',
            'confirmed_on' => '2026-08-19',
        ]);
    }
}
