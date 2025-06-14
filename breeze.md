```mermaid
graph TB
    A[ユーザー] --> B[ブラウザ]
    B --> C[Laravel Application]
    
    subgraph "Laravel Files"
        C --> D[routes/web.php]
        D --> E[routes/auth.php]
        E --> F[Controllers]
        F --> G[Models]
        F --> H[Views]
        G --> I[(Database)]
    end
    
    subgraph "Controllers"
        F1[RegisteredUserController<br/>app/Http/Controllers/Auth/]
        F2[AuthenticatedSessionController<br/>app/Http/Controllers/Auth/]
    end
    
    subgraph "Models"
        G1[User Model<br/>app/Models/User.php]
    end
    
    subgraph "Views"
        H1[register.blade.php<br/>resources/views/auth/]
        H2[login.blade.php<br/>resources/views/auth/]
    end
    
    subgraph "Database"
        I1[users table<br/>database/migrations/]
        I2[sessions table]
    end
    
    F --> F1
    F --> F2
    G --> G1
    H --> H1
    H --> H2
    I --> I1
    I --> I2
```

## 新規登録フロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant B as ブラウザ
    participant R as routes/auth.php
    participant C as RegisteredUserController
    participant M as User Model
    participant DB as users table
    participant S as Session
    
    U->>B: /register にアクセス
    B->>R: GET /register
    R->>C: create() method
    C->>B: 登録フォーム表示<br/>(resources/views/auth/register.blade.php)
    B->>U: フォーム表示
    
    U->>B: フォーム送信
    B->>R: POST /register
    R->>C: store() method<br/>(app/Http/Controllers/Auth/RegisteredUserController.php)
    C->>C: バリデーション実行
    C->>M: User::create()<br/>(app/Models/User.php)
    M->>DB: INSERT INTO users<br/>(database/migrations/create_users_table.php)
    DB-->>M: レコード作成完了
    M-->>C: ユーザー作成完了
    C->>S: セッション開始<br/>Auth::login($user)
    C->>B: リダイレクト (通常 /dashboard)
    B->>U: ログイン完了
```

## ログインフロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant B as ブラウザ
    participant R as routes/auth.php
    participant C as AuthenticatedSessionController
    participant A as Auth Facade
    participant DB as users table
    participant S as Session
    
    U->>B: /login にアクセス
    B->>R: GET /login
    R->>C: create() method
    C->>B: ログインフォーム表示<br/>(resources/views/auth/login.blade.php)
    B->>U: フォーム表示
    
    U->>B: ログイン情報送信
    B->>R: POST /login
    R->>C: store() method<br/>(app/Http/Controllers/Auth/AuthenticatedSessionController.php)
    C->>C: バリデーション実行
    C->>A: Auth::attempt($credentials)
    A->>DB: SELECT * FROM users WHERE email = ?<br/>(database/migrations/create_users_table.php)
    DB-->>A: ユーザー情報返却
    A->>A: パスワード照合<br/>(Hash::check)
    A-->>C: 認証結果
    C->>S: セッション確立<br/>(config/session.php)
    C->>B: リダイレクト (通常 /dashboard)
    B->>U: ログイン完了
```

## 認証状態確認フロー

```mermaid
sequenceDiagram
    participant U as ユーザー
    participant B as ブラウザ
    participant R as Routes
    participant C as Controller
    participant A as Auth Helper
    participant S as Session
    
    U->>B: 認証が必要なページにアクセス
    B->>R: リクエスト
    R->>C: Controller method
    C->>A: auth()->check()<br/>(Illuminate/Support/helpers.php)
    A->>S: セッション確認<br/>(storage/framework/sessions/)
    S-->>A: セッション情報
    A-->>C: 認証状態 (true/false)
    
    alt ログイン済み
        C->>A: auth()->user()
        A->>S: ユーザー情報取得
        S-->>A: User オブジェクト<br/>(app/Models/User.php)
        A-->>C: ユーザー情報
        C->>B: ページ表示
        B->>U: 認証済みページ
    else 未ログイン
        C->>B: リダイレクト to /login
        B->>U: ログインページ
    end
```

## ファイル構成

```mermaid
graph TD
    A[Laravel Project Root] --> B[app/]
    A --> C[routes/]
    A --> D[resources/]
    A --> E[database/]
    A --> F[config/]
    A --> G[storage/]
    
    B --> B1[Http/Controllers/Auth/]
    B --> B2[Models/]
    B1 --> B11[RegisteredUserController.php]
    B1 --> B12[AuthenticatedSessionController.php]
    B2 --> B21[User.php]
    
    C --> C1[web.php]
    C --> C2[auth.php]
    
    D --> D1[views/auth/]
    D1 --> D11[register.blade.php]
    D1 --> D12[login.blade.php]
    
    E --> E1[migrations/]
    E1 --> E11[create_users_table.php]
    
    F --> F1[auth.php]
    F --> F2[session.php]
    
    G --> G1[framework/sessions/]
```

## 重要ポイント
- Breezeが認証に必要な全てを自動生成
- `/login` や `/register` は特別なルートではなく、普通のルート
- ログイン後は `auth()->user()` でユーザー情報にアクセス可能
- セッションで認証状態を管理

## 次のステップ
1. 実際にユーザー登録してみる
2. `auth()->user()` の値を確認する（`dd(auth()->user())`）
3. ログイン状態での画面表示を試す