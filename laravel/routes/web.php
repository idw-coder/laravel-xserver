<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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
    Route::get('/users', [UserController::class, 'index']);
});

// 認証関連のルートを読み込み
require __DIR__ . '/auth.php';
