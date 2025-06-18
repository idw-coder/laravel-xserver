# Laravel Enum完全ガイド

## Enumとは何か？

**Enum = Enumeration（列挙型）**  
「決められた値の中からしか選べない」ようにするプログラミングの仕組みです。

### 現実世界の例

| 例 | 選択肢 | 説明 |
|---|---|---|
| 信号機 | `赤` `青` `黄` | 「紫」はない |
| 曜日 | `月` `火` `水` `木` `金` `土` `日` | これ以外はない |
| 注文状況 | `注文中` `発送済み` `配達完了` | 明確な状態 |

---

## PHP 8.1以前（Enumがなかった時代）

### 定数で管理（古い方法）

```php
class PostStatus 
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const ARCHIVED = 'archived';
}

// 使用例
$status = PostStatus::DRAFT;
```

#### 問題点
```php
// こんなことができてしまう（危険！）
$status = 'publihsed';  // タイポでもエラーにならない
$status = 'random';     // 意味不明な値でもOK
$status = 'banana';     // なんでもあり
```

### 文字列で直接管理（もっと古い方法）

```php
// データベースに保存する時
$post_status = 'published';  // OK
$post_status = 'publihsed';  // タイポ！でもエラーにならない
$post_status = 'banana';     // 意味不明！でもエラーにならない
```

---

## PHP 8.1+ Enum（新しい方法）

### Enumの定義

```php
enum PostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
```

### Enumの使用

```php
// 正しい使い方
$status = PostStatus::DRAFT;      // OK
$status = PostStatus::PUBLISHED;  // OK

// エラーになる（これが良いこと！）
$status = PostStatus::PUBLIHSED;  // 存在しないのでエラー！
$status = 'banana';               // Enumじゃないのでエラー！
```

---

## なぜEnumが良いのか？

### 1. タイポ防止

| 従来の方法 | Enum使用 |
|---|---|
| ```php<br>// タイポに気づけない<br>if ($post->status === 'publihsed') {<br>    echo '公開済み'; // 実行されない<br>}<br>``` | ```php<br>// タイポするとエラーになる<br>if ($post->status === PostStatus::PUBLISHED) {<br>    echo '公開済み'; // 正しく実行される<br>}<br>``` |

### 2. IDEの補完機能

```php
PostStatus:: // ←ここで「::」を打つと候補が表示される
```

**表示される候補：**
- `DRAFT`
- `PUBLISHED`  
- `ARCHIVED`

### 3. 値の制限

| 従来の方法 | Enum使用 |
|---|---|
| ```php<br>// 何でも入る（危険）<br>$post->status = 'なんでも入る';<br>$post->status = 'banana';<br>$post->status = 123;<br>``` | ```php<br>// 決められた値だけ（安全）<br>$post->status = PostStatus::DRAFT;<br>// これ以外は不可能<br>``` |

---

## 実際の使用例

### 投稿ステータスの例

```php
<?php
// app/Enums/PostStatus.php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';           // 下書き
    case PUBLISHED = 'published';   // 公開
    case ARCHIVED = 'archived';     // アーカイブ

    /**
     * 日本語名を取得
     */
    public function japanese(): string
    {
        return match($this) {
            self::DRAFT => '下書き',
            self::PUBLISHED => '公開',
            self::ARCHIVED => 'アーカイブ',
        };
    }

    /**
     * 編集可能かどうか
     */
    public function isEditable(): bool
    {
        return match($this) {
            self::DRAFT => true,
            self::PUBLISHED => false,
            self::ARCHIVED => false,
        };
    }

    /**
     * 選択肢一覧を取得
     */
    public static function options(): array
    {
        return [
            self::DRAFT->value => self::DRAFT->japanese(),
            self::PUBLISHED->value => self::PUBLISHED->japanese(),
            self::ARCHIVED->value => self::ARCHIVED->japanese(),
        ];
    }
}
```

### 使用方法

```php
// 投稿を作成
$post = new Post();
$post->title = 'テスト記事';
$post->status = PostStatus::DRAFT; // Enumを使用

// ステータスチェック
if ($post->status === PostStatus::PUBLISHED) {
    echo 'この記事は公開されています';
}

// 日本語表示
echo $post->status->japanese(); // "下書き"

// 編集可能性チェック
if ($post->status->isEditable()) {
    echo '編集できます';
}
```

---

## Enumディレクトリの作成

### ディレクトリ構造

```
app/
├── Http/
├── Models/
├── Providers/
├── Enums/          ← 自分で作成する！
│   ├── PostStatus.php
│   ├── UserRole.php
│   └── OrderStatus.php
└── ...
```

### 作成手順

```bash
# 1. ディレクトリ作成
mkdir app/Enums

# 2. Laravel 12の場合（Artisanコマンド使用）
php artisan make:enum PostStatus

# 3. 手動作成の場合
touch app/Enums/PostStatus.php
```

---

## LaravelでのEnum使用

### モデルで使用

```php
<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PostStatus;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'status',
    ];

    protected $casts = [
        'status' => PostStatus::class, // Enumとして扱う
    ];

    /**
     * 公開済みの投稿のみ取得
     */
    public function scopePublished($query)
    {
        return $query->where('status', PostStatus::PUBLISHED);
    }
}
```

### マイグレーションで使用

```php
<?php
// database/migrations/xxxx_create_posts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['draft', 'published', 'archived'])
                  ->default('draft'); // Enumの値を明示
            $table->timestamps();
        });
    }
};
```

### コントローラーで使用

```php
<?php
// app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Enums\PostStatus;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // 公開済みの投稿のみ取得
        $posts = Post::where('status', PostStatus::PUBLISHED)->get();
        
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        // フォームで使用する選択肢
        $statusOptions = PostStatus::options();
        
        return view('posts.create', compact('statusOptions'));
    }

    public function store(Request $request)
    {
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'status' => PostStatus::from($request->status), // Enumに変換
        ]);

        return redirect()->route('posts.show', $post);
    }
}
```

### ビューで使用

```blade
{{-- resources/views/posts/create.blade.php --}}

<form method="POST" action="{{ route('posts.store') }}">
    @csrf
    
    <div>
        <label for="title">タイトル</label>
        <input type="text" name="title" id="title">
    </div>

    <div>
        <label for="status">ステータス</label>
        <select name="status" id="status">
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit">保存</button>
</form>
```

```blade
{{-- resources/views/posts/show.blade.php --}}

<article>
    <h1>{{ $post->title }}</h1>
    <div class="status-badge status-{{ $post->status->value }}">
        {{ $post->status->japanese() }}
    </div>
    
    @if($post->status->isEditable())
        <a href="{{ route('posts.edit', $post) }}">編集</a>
    @endif
</article>
```

---

## まとめ

### 従来の方法 vs Enum

| 項目 | 従来の方法 | Enum使用 |
|---|---|---|
| **型安全性** | なし | あり |
| **タイポ防止** | なし | あり |
| **IDEサポート** | なし | 完全サポート |
| **拡張性** | 低い | 高い |
| **保守性** | 低い | 高い |

### Enumを使うべき場面

- **状態管理が必要な場合**
  - 投稿ステータス（下書き、公開、アーカイブ）
  - 注文状況（注文中、発送済み、配達完了）
  - ユーザー権限（管理者、編集者、一般ユーザー）

- **選択肢が決まっている場合**
  - 性別、曜日、優先度レベル
  - カテゴリ、タグ、分類

- **データベースの整合性を保ちたい場合**
  - ENUM型として定義
  - バリデーションで値を制限

**Enum = 選択肢を限定する仕組み**

- **従来：** 文字列なので何でも入る → バグの原因
- **Enum：** 決められた値だけ → 安全

つまり、**「決められたルールの中でしか選べない」ようにして、プログラムをより安全にする**技術です！
    