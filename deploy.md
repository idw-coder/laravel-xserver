# Laravel プロジェクト Xserver デプロイ手順

## ✅ 前提条件

- Laravelプロジェクトは `laravel-xserver/` ディレクトリ内に構成されている
- XserverにFTPアップロード可能（WinSCP推奨）
- XserverのPHPバージョンは Laravel要件（8.2以上）に設定済み

---

## 1. ローカル環境の準備

```bash
# Laravelルートディレクトリに移動
cd laravel-xserver

# 本番用にComposerパッケージをインストール
composer install --no-dev --optimize-autoloader

# 本番用.envをコピー作成
cp .env.production .env

# APP_KEYの生成（なければ）
php artisan key:generate --show
```

---

## 2. Xserverへのファイルアップロード

### アップロード対象（すべて `laravel-xserver/` 以下に格納）

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/`（内部アセット用）
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `.env`
- `.htaccess` ← `public/.htaccess` をコピー
- `index.php` ← `public/index.php` をコピー・パス修正
- `artisan`, `composer.json`, `composer.lock`

---

## 3. `index.php` の修正（ルートで動かすため）

```php
// 修正前（public/index.php）
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// 修正後（laravel-xserver/index.php）
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

---

## 4. パーミッション設定（FTPで実施）

- `storage/`：`755` 〜 `775`
- `bootstrap/cache/`：`755` 〜 `775`

---

## 5. Laravelにアクセス

- ブラウザで確認：
  ```
  https://yourdomain.com/laravel-xserver/
  ```

- Welcomeページが表示されれば成功！

---

## トラブルシューティング

| 現象 | 原因 |
|------|------|
| 500 Internal Server Error | `vendor/` 欠落 or `index.php` パス修正漏れ |
| Unsupported cipher 〜 | `.env` に `APP_KEY` 未設定 |
| Laravelルーティングが効かない | `.htaccess` 設置忘れ |
| DB接続エラー | `.env` の DB接続情報ミス |

---

## 今後のデプロイ更新手順（コード変更時）

```bash
# ローカルで修正
git commit → build → composer install --no-dev

# FTPで上書きアップロード（.envは除外）
```

---

## 今後の課題

- DBのインポートと `.env` DB接続設定
- シーディング処理の適用（必要に応じて）
- バージョンアップ対応
