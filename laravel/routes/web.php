<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageUploadController;
use Illuminate\Support\Facades\Route;

// トップページのルート
Route::get('/', function () {
    return view('welcome');
});

// ダッシュボードページのルート
// middleware(['auth']) - ログインしていないユーザーはアクセス不可（未ログイン時は自動的にログインページにリダイレクト）
// middleware(['verified']) - メール認証が完了していないユーザーはアクセス不可（未認証時は認証ページにリダイレクト）
// 認証関連のルート定義は routes/auth.php を参照
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 認証済みユーザー向けのルートグループ
Route::middleware('auth')->group(function () {
    // プロフィール編集ページの表示
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // プロフィール情報の更新
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // プロフィールの削除
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // ユーザー一覧ページの表示
    Route::get('/users', [UserController::class, 'index'])->name('users');

    // 投稿作成ページの表示
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    // 投稿の保存
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // 投稿編集ページの表示
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    // 投稿の更新
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    // 投稿の削除
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');
    // 投稿編集ページの表示
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    // 投稿の更新
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    // 投稿の削除
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// 投稿一覧ページの表示
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
// 投稿詳細ページ（スラッグでアクセス）
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// 画像アップロード用ルート
Route::post('/upload-image', [ImageUploadController::class, 'store']);

// 認証関連のルートを読み込み
require __DIR__ . '/auth.php';
