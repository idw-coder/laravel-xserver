<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Enums\PostStatus;

/**
 * 投稿モデル
 * 
 * ブログ記事やニュース記事などの投稿を管理するモデル
 */
class Post extends Model
{
    use SoftDeletes;

    /**
     * 一括代入可能な属性
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'status',
        'published_at',
    ];

    /**
     * 属性のキャスト
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'status' => PostStatus::class,
    ];

    /**
     * 投稿者とのリレーション
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
