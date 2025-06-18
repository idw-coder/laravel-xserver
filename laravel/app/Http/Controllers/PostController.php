<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Enums\PostStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as AuthFacade;

class PostController extends Controller
{
    /**
     * 公開済み投稿一覧表示
     * 
     * ■ get() と paginate() の違い：
     * - get(): 全てのデータを取得し、Collection（配列のような）オブジェクトを返す
     * - paginate(数値): 指定した件数ずつに分割し、LengthAwarePaginatorオブジェクトを返す
     * 
     * ■ Paginatorオブジェクトの特徴：
     * - データだけでなく、ページネーション情報（現在のページ、総ページ数など）も含む
     * - links()メソッドが使用可能（「前へ」「次へ」ボタンのHTMLを生成）
     * - Collectionとしても使用可能（foreachでループできる）
     * 
     * ■ with('author')について：
     * - Eager Loading（一括読み込み）と呼ばれる機能
     * - PostとUserテーブルを事前に結合して取得（N+1問題を回避）
     * - without: 投稿100件なら、投稿取得1回 + 各投稿の作者取得100回 = 101回のクエリ
     * - with: 投稿取得1回 + 全作者取得1回 = 2回のクエリ（大幅な高速化）
     */
    public function index()
    {
        // ページネーション付きで投稿一覧を取得
        $posts = Post::with('author')  // 作者情報も一緒に取得（Eager Loading）
            ->where(function ($query) {
                $query->where('status', PostStatus::PUBLISHED)  // 公開済み
                    ->orWhere(function ($q) {
                        $q->where('status', PostStatus::DRAFT)  // 下書き
                            ->where('user_id', Auth::user()->id);  // 自分の投稿のみ
                    });
            })
            ->orderBy('published_at', 'desc')  // 公開日時の降順（新しい順）
            ->paginate(10);  // 10件ずつページ分割（LengthAwarePaginatorを返す）

        // ビューにデータを渡す
        // compact('posts') は ['posts' => $posts] と同じ意味
        return view('posts.index', compact('posts'));
    }

    /**
     * 新規投稿作成フォーム表示
     * 
     * ■ 認証について：
     * - このメソッドは web.php で auth ミドルウェアが適用されているため
     * - ログインしていないユーザーはアクセスできない
     * - 自動的にログインページにリダイレクトされる
     */
    public function create()
    {
        // 投稿作成フォームのビューを表示
        return view('posts.create');
    }

    /**
     * 投稿保存処理
     * 
     * ■ バリデーションについて：
     * - validate()メソッドで入力データの検証を行う
     * - 検証に失敗すると自動的に前のページに戻り、エラーメッセージを表示
     * - 検証に成功すると、検証済みデータが $validated に格納される
     * 
     * ■ スラッグ（slug）について：
     * - URLで使用する文字列（例：/posts/my-first-post）
     * - 日本語タイトル「私の最初の投稿」→ スラッグ「my-first-post」
     * - SEO対策とURL可読性向上のため
     */
    public function store(Request $request)
    {
        // 入力データのバリデーション（検証）
        $validated = $request->validate([
            'title' => 'required|string|max:255',  // 必須、文字列、255文字以内
            'excerpt' => 'nullable|string|max:500',  // 任意、文字列、500文字以内
            'body' => 'required|string',  // 必須、文字列
            'status' => 'required|in:draft,published',  // 必須、draft または published のみ
        ]);

        // タイトルからスラッグを自動生成
        $slug = Str::slug($validated['title']) ?: Str::random(10);

        // 同じスラッグが既に存在する場合は番号を付加して重複を回避
        $originalSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // データベースに保存するデータを配列で準備
        $postData = [
            'user_id' => Auth::user()->id,  // 現在ログイン中のユーザーID
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'body' => $validated['body'],
            'status' => PostStatus::from($validated['status']),  // 文字列をEnumに変換
        ];

        // 公開ステータスの場合は公開日時を現在時刻に設定
        if ($validated['status'] === 'published') {
            $postData['published_at'] = now();
        }

        Post::create($postData);

        return redirect()->route('posts.index')
            ->with('success', '投稿が正常に作成されました。');
    }

    /**
     * 投稿詳細表示
     * 
     * ■ ルートモデルバインディングについて：
     * - Route::get('/posts/{post:slug}', [PostController::class, 'show'])
     * - {post:slug} は slug カラムでモデルを検索することを指定
     * - Laravel が自動的に slug の値で Post モデルを検索
     * - 見つからない場合は自動的に 404 エラー
     * - メソッドの引数に Post $post で受け取れる
     * 
     * ■ 認可（アクセス制御）について：
     * - 公開済み投稿：全員が閲覧可能
     * - 下書き投稿：作成者のみが閲覧可能
     * - 削除済み投稿：表示されない（ソフトデリート）
     */
    public function show(Post $post)
    {
        // 公開済み投稿は誰でも閲覧可能
        // 下書き投稿は作成者のみ閲覧可能
        if (
            $post->status !== PostStatus::PUBLISHED &&
            (!Auth::check() || Auth::user()->id !== $post->user_id)
        ) {
            abort(404);  // 404 Not Found エラーを発生
        }

        // 投稿の作者情報も一緒に読み込む
        $post->load('author');

        return view('posts.show', compact('post'));
    }

    /**
     * 投稿編集フォーム表示
     * 
     * ■ 認可（Authorization）について：
     * - 認証（Authentication）：ユーザーが誰かを確認
     * - 認可（Authorization）：そのユーザーが特定の操作を行う権限があるかを確認
     * - ここでは投稿の作成者のみが編集可能
     * 
     * 現在はコメントアウト中（未実装）
     */
    public function edit(Post $post)
    {
        // 投稿の作成者以外はアクセス拒否
        if (Auth::user()->id !== $post->user_id) {
            abort(403);  // 403 Forbidden エラーを発生
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * 投稿更新処理
     * 
     * ■ update() と create() の違い：
     * - create(): 新しいレコードを作成
     * - update(): 既存のレコードを更新
     * - fill() + save() でも同様の更新が可能
     * 
     * 現在はコメントアウト中（未実装）
     */
    public function update(Request $request, Post $post)
    {
        // 投稿の作成者以外は更新拒否
        if (Auth::user()->id !== $post->user_id) {
            abort(403);
        }

        // 入力データの検証
        $validated = $request->validate([
            'title' => 'required|max:255',
            'excerpt' => 'nullable|max:500',
            'body' => 'required',
            'status' => 'required|in:draft,published,archived'
        ]);

        // 下書きから公開に変更された場合、公開日時を設定
        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        // 既存の投稿データを更新
        $post->update($validated);

        // 詳細ページにリダイレクト
        return redirect()->route('posts.show', $post->slug)
            ->with('success', '投稿を更新しました');
    }

    /**
     * 投稿削除処理
     * 
     * ■ ソフトデリートについて：
     * - 物理削除：データベースからレコードを完全に削除
     * - ソフトデリート：deleted_at カラムに削除日時を記録（レコードは残る）
     * - Post モデルで SoftDeletes トレイトを使用しているため、delete() はソフトデリート
     * - 完全削除したい場合は forceDelete() を使用
     * - 削除されたデータの復元は restore() で可能
     * 
     * 現在はコメントアウト中（未実装）
     */
    public function destroy(Post $post)
    {
        // 投稿の作成者以外は削除拒否
        if (Auth::user()->id !== $post->user_id) {
            abort(403);
        }

        // ソフトデリート実行
        $post->delete();

        // 一覧ページにリダイレクト
        return redirect()->route('posts.index')
            ->with('success', '投稿を削除しました');
    }
}
