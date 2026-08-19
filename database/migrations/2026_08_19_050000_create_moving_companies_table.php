<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 引越業者の一覧（全日本トラック協会「引越安心マーク」認定事業者）。
     *
     * このサイトの中身は利用者が投稿する相見積もり額だが、投稿が集まるまでは
     * 業者ページが1つも無く、検索から入ってくる人の受け皿が無かった。
     * 公的に確認できる認定事業者の一覧を土台として持ち、口コミはその上に載せる。
     */
    public function up(): void
    {
        Schema::create('moving_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('kana_column', 10)->index();   // あ行・か行…
            $table->string('certificate_url');            // 認定証PDF（公式）
            $table->string('source_url');                 // 一覧ページ（公式）
            $table->date('confirmed_on')->nullable();     // 出典を確認した日
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moving_companies');
    }
};
