"""全日本トラック協会の「引越安心マーク」認定事業者一覧から、引越業者の一覧を作る。

出典: 引越安心マーク制度 認定事業者一覧（全日本トラック協会）
  https://jta.or.jp/member/hikkoshi_member/hikkoshi_anshin/list.html
  50音別のページ（column2025_a.html 〜 column2025_w.html）に、事業者名と
  認定証PDFへのリンクが載っている。

引越安心マークは、標準引越運送約款の遵守、お客様窓口の設置、引越管理者講習の
修了者配置などを満たした事業者を、全日本トラック協会が認定する制度。
ここで取るのは公式一覧に載っている事実（事業者名と認定証のリンク）だけで、
料金や評価などの推測は入れない。

使い方: python scripts/build-company-data.py
  → database/data/moving-companies.json を書き出す。
"""
import json
import re
import time
import urllib.request
from datetime import date
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
OUTPUT = ROOT / 'database' / 'data' / 'moving-companies.json'

LIST_PAGE = 'https://jta.or.jp/member/hikkoshi_member/hikkoshi_anshin/list.html'
COLUMN_PAGE = 'https://jta.or.jp/member/hikkoshi_member/hikkoshi_anshin/list/column2025_{}.html'
COLUMNS = {
    'a': 'あ行', 'k': 'か行', 's': 'さ行', 't': 'た行', 'n': 'な行',
    'h': 'は行', 'm': 'ま行', 'y': 'や行', 'r': 'ら行', 'w': 'わ行',
}
UA = ('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
      '(KHTML, like Gecko) Chrome/131.0 Safari/537.36')
DELAY = 1.5


def get(url: str) -> str:
    request = urllib.request.Request(url, headers={'User-Agent': UA, 'Accept-Language': 'ja'})
    with urllib.request.urlopen(request, timeout=30) as response:
        return response.read().decode('utf-8', 'replace')


def main() -> None:
    companies: dict[str, dict] = {}

    for key, label in COLUMNS.items():
        url = COLUMN_PAGE.format(key)
        try:
            html = get(url)
        except Exception as error:
            print(f'{label} 取得に失敗しました: {error}', flush=True)
            time.sleep(DELAY)
            continue

        # 一覧は画像＋イメージマップで作られていて、<area> の alt が事業者名、
        # href がその事業者の認定証PDF。
        entries = re.findall(r'<area[^>]+href="([^"]+)"[^>]*alt="([^"]*)"', html)
        added = 0
        for pdf_url, name in entries:
            name = name.strip()
            if not name or name in companies:
                continue
            companies[name] = {
                'name': name,
                'kana_column': label,
                'certificate_url': pdf_url.strip(),
                'source_url': url,
            }
            added += 1

        print(f'{label} {added}社', flush=True)
        time.sleep(DELAY)

    records = sorted(companies.values(), key=lambda company: (company['kana_column'], company['name']))

    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    OUTPUT.write_text(json.dumps({
        'confirmedOn': date.today().isoformat(),
        'sourceLabel': '全日本トラック協会「引越安心マーク制度 認定事業者一覧」',
        'sourceUrl': LIST_PAGE,
        'companies': records,
    }, ensure_ascii=False), encoding='utf-8')

    print(f'{len(records)}社を書き出しました')


main()
