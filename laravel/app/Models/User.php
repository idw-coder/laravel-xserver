<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Userモデルは「users」テーブルと対応しています。
 * Authenticatable を継承しているので、このモデルは「ログインユーザー」として扱えます。
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * 部署区分の定数定義
     */
    const DEPARTMENT_IT = 'it';           // 情報システム課
    const DEPARTMENT_ACCOUNTING = 'ac';   // 経理課
    const DEPARTMENT_SALES = 'sl';        // 成約課
    const DEPARTMENT_HR = 'hr';           // 人事課
    const DEPARTMENT_MARKETING = 'mk';    // マーケティング課

    /**
     * 部署区分の選択肢
     */
    public static function getDepartmentOptions(): array
    {
        return [
            self::DEPARTMENT_IT => '情報システム課',
            self::DEPARTMENT_ACCOUNTING => '経理課',
            self::DEPARTMENT_SALES => '成約課',
            self::DEPARTMENT_HR => '人事課',
            self::DEPARTMENT_MARKETING => 'マーケティング課',
        ];
    }

    /**
     * 一括代入可能な属性（カラム）のリスト
     * 
     * これらの属性は、create()やupdate()メソッドで一度に値を設定できます。
     * セキュリティ上、重要な属性（idなど）は含めません。
     * 
     * @var list<string>
     */
    protected $fillable = [
        'name',              // ユーザー名
        'email',             // メールアドレス
        'password',          // パスワード（ハッシュ化される）
        'admin_id',          // 管理者ID
        'qualification',     // 資格の有無
        'role',              // ユーザー権限
        'department',        // 部署区分
    ];

    /**
     * シリアライズ時に隠す属性のリスト
     * 
     * これらの属性は、JSONに変換する際やログ出力時に表示されません。
     * セキュリティ上重要な情報（パスワードなど）を保護します。
     * 
     * @var list<string>
     */
    protected $hidden = [
        'password',        // パスワード（ハッシュ化済みでも隠す）
        'remember_token',  // ログイン保持用トークン
    ];

    /**
     * 属性の型変換ルールを定義
     * 
     * データベースから取得した値を、指定した型に自動変換します。
     * 例：'2023-01-01 10:00:00' → Carbonインスタンス
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // 文字列 → Carbon日時オブジェクト
            'password' => 'hashed',             // パスワードをハッシュ化（Laravel 10以降）
        ];
    }

    /**
     * 部署名を取得する
     * 
     * @return string|null
     */
    public function getDepartmentNameAttribute(): ?string
    {
        if (!$this->department) {
            return null;
        }
        return self::getDepartmentOptions()[$this->department] ?? null;
    }

    /**
     * 部署が設定されているかチェック
     * 
     * @return bool
     */
    public function hasDepartment(): bool
    {
        return !is_null($this->department);
    }

    /**
     * 特定の部署のユーザーを取得するスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    /**
     * 部署が設定されているユーザーのみを取得するスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasDepartment($query)
    {
        return $query->whereNotNull('department');
    }

    /**
     * 部署が設定されていないユーザーのみを取得するスコープ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoDepartment($query)
    {
        return $query->whereNull('department');
    }

    /**
     * ユーザーの投稿一覧を取得するリレーション
     * 
     * リレーションとは、他のテーブルとの関係を定義するものです。
     * このメソッドにより、$user->posts でユーザーの投稿を取得できます。
     * 
     * 例：
     * $user = User::find(1);
     * $posts = $user->posts;  // このユーザーの全投稿を取得
     * 
     * @return HasMany
     */
    public function posts(): HasMany
    {
        // hasMany = 1対多の関係
        // 1人のユーザー → 複数の投稿
        return $this->hasMany(Post::class);
    }

    /**
     * ユーザーの公開済み投稿一覧を取得するリレーション
     * 
     * 通常のposts()リレーションに条件を追加したバリエーションです。
     * status = 'published'の投稿のみを取得します。
     * 
     * 例：
     * $user = User::find(1);
     * $publishedPosts = $user->publishedPosts;  // 公開済み投稿のみ取得
     * 
     * @return HasMany
     */
    public function publishedPosts(): HasMany
    {
        // where()で条件を追加
        return $this->hasMany(Post::class)->where('status', 'published');
    }

    /**
     * 認証に使用するカラム名を取得
     * 
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'admin_id';
    }

    /**
     * 認証に使用するカラムの値を取得
     * 
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * 認証用のパスワードを取得
     * 
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
}
