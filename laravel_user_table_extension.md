# Laravel ユーザーテーブル 拡張手順書

## 概要

このドキュメントは、Laravel の `users` テーブルに以下の3項目を追加する手順を記録したものです。

- 4桁の管理者ID (`admin_id`)
- 資格の有無 (`qualification`)
- ユーザー権限 (`role`)

---

## 1. マイグレーションファイル作成

以下のコマンドを実行し、新しいマイグレーションファイルを作成します。

```bash
php artisan make:migration add_qualification_and_role_to_users_table --table=users
```

---

## 2. マイグレーションファイル編集

生成されたマイグレーションファイルの `up` メソッドを以下のように記述：
Laravelのマイグレーションファイルで使われる up() と down() にはそれぞれ次のような意味と役割があります。
- up() メソッド：構造を「適用」する処理
php artisan migrate 実行時に呼ばれる
- down() メソッド：構造を「元に戻す」処理
php artisan migrate:rollback 実行時に呼ばれる

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('admin_id', 4)->nullable()->comment('4桁の管理者ID');
        $table->enum('qualification', ['qualified', 'unqualified'])->default('unqualified')->comment('資格の有無');
        $table->enum('role', ['general', 'sv', 'sl', 'manager'])->default('general')->comment('ユーザー権限');
    });
}
```

`down` メソッドも忘れず記述：

```php
public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['admin_id', 'qualification', 'role']);
    });
}
```

---

## 3. マイグレーション実行

以下のコマンドでマイグレーションを実行します：

```bash
php artisan migrate
```

---

## 4. User モデルの更新

`app/Models/User.php` の `$fillable` に以下を追加：

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'admin_id',
    'qualification',
    'role',
];
```

---

## 5. 変更の確認

```
php artisan tinker
>>> Schema::getColumnListing('users');
```

Laravel Tinker とは？
Tinker（ティンカー） は、Laravel に付属している インタラクティブなコマンドラインツール です。

---

## 備考

- `nullable()` を付けることで既存データに影響は出ません（空欄可）。
- `enum` 型は将来的に値の種類を制限するのに便利です。
