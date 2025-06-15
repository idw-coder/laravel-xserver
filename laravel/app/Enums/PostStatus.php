<?php

namespace App\Enums;

/**
 * 投稿のステータスを管理するEnum
 * 
 * このEnumは投稿の状態を表し、以下の3つの状態を定義しています：
 * - DRAFT: 下書き状態。まだ公開されていない投稿
 * - PUBLISHED: 公開済み状態。一般ユーザーが閲覧可能な投稿
 * - ARCHIVED: アーカイブ状態。過去の投稿で、通常の一覧には表示されない
 * 
 * 型安全性:
 * - これら3つの値以外は代入不可（コンパイルエラー）
 * - 文字列での直接代入も不可（例: $post->status = 'draft' はエラー）
 * - データベースのenum定義(['draft', 'published', 'archived'])と完全一致
 */
enum PostStatus: string
{
/** 下書き状態 */
    case DRAFT = 'draft';

/** 公開済み状態 */
    case PUBLISHED = 'published';

/** アーカイブ状態 */
    case ARCHIVED = 'archived';

    /**
     * ステータスの日本語表示名を取得
     * 
     * @return string ステータスの日本語表示名
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => '下書き',
            self::PUBLISHED => '公開済み',
            self::ARCHIVED => 'アーカイブ',
        };
    }
}
