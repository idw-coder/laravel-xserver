<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void //bootはアプリケーションが起動するときに呼び出されるメソッド
    {
        // ユーザー作成権限の定義
        Gate::define('create-user', function ($user) {
            // nullガード（ユーザーが存在し、かつ部署がITの場合にtrueを返す）
            return $user && $user->department === 'it';
        });
    }
}
