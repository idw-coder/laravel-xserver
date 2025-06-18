<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('投稿一覧') }}
            </h2>
            {{--
                @auth ディレクティブ：
                - ユーザーがログインしている場合のみ表示
                - Auth::check() と同じ機能
                - @guest は逆にログインしていない場合のみ表示
            --}}
            @auth
            <a href="{{ route('posts.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                新規投稿作成
            </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{--
                フラッシュメッセージの表示：
                - session('success') でセッションからメッセージを取得
                - PostController の store() メソッドで設定したメッセージ
                - 一度表示されると自動的にセッションから削除される
            --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{--
                        count() メソッドについて：
                        - Paginator オブジェクトでも count() が使用可能
                        - 現在のページに表示されている件数を返す
                        - 総件数を取得したい場合は total() メソッドを使用
                    --}}
                    @if($posts->count() > 0)
                    <div class="space-y-6">
                        {{--
                                @foreach でページネーションデータをループ：
                                - $posts は LengthAwarePaginator オブジェクト
                                - Paginator は Iterable インターフェースを実装
                                - そのため foreach でループ可能
                                - 現在のページのデータのみが取得される
                            --}}
                        @foreach($posts as $post)
                        <article class="border-b border-gray-200 pb-6 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <h2 class="text-xl font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                    <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
                                </h2>
                                <span class="text-sm text-gray-500">
                                    {{--
                                                published_at の表示：
                                                - ? 演算子（Null合体演算子）で null チェック
                                                - format() は Carbon（日時ライブラリ）のメソッド
                                                - Y年n月j日 = 2025年1月1日 形式
                                            --}}
                                    @if($post->status === App\Enums\PostStatus::DRAFT)
                                    <span class="text-yellow-600">下書き</span>
                                    @else
                                    {{ $post->published_at ? $post->published_at->format('Y年n月j日') : '未公開' }}
                                    @endif
                                </span>
                            </div>

                            <div class="text-sm text-gray-600 mb-3">
                                {{--
                                            リレーション（関連）データの表示：
                                            - $post->author は User モデルのインスタンス
                                            - with('author') で事前読み込みしているため追加クエリは発生しない
                                            - Eager Loading していない場合は N+1 問題が発生
                                        --}}
                                投稿者: {{ $post->author->name }}
                            </div>

                            @if($post->excerpt)
                            <p class="text-gray-700 mb-3">{{ $post->excerpt }}</p>
                            @else
                            {{--
                                            Str::limit() ヘルパー関数：
                                            - 文字列を指定した長さで切り取り
                                            - strip_tags() でHTMLタグを除去
                                            - 第3引数で省略記号をカスタマイズ可能（デフォルト: ...）
                                        --}}
                            <p class="text-gray-700 mb-3">{{ Str::limit(strip_tags($post->body), 200) }}</p>
                            @endif

                            <div class="flex justify-between items-center">
                                <a href="{{ route('posts.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    続きを読む →
                                </a>
                                {{--
                                            Enum の label() メソッド：
                                            - PostStatus Enum で定義したカスタムメソッド
                                            - 'published' → '公開' のように日本語で表示
                                        --}}
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($post->status === App\Enums\PostStatus::PUBLISHED) bg-green-100 text-green-800
                                    @elseif($post->status === App\Enums\PostStatus::DRAFT) bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $post->status->label() }}
                                </span>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    {{--
                            ■ ページネーション（links()メソッド）について：
                            
                            1. links() メソッドの動作：
                               - LengthAwarePaginator オブジェクトのメソッド
                               - 「前へ」「次へ」ボタンとページ番号のHTMLを自動生成
                               - Tailwind CSS のクラスが自動適用される
                               - Collection には存在しないため get() 使用時はエラー
                            
                            2. ページネーションの仕組み：
                               - URL パラメータ ?page=2 でページを指定
                               - 1ページ目: posts/?page=1 (省略可能)
                               - 2ページ目: posts/?page=2
                               - Laravel が自動的にページ番号を処理
                            
                            3. ページネーション情報の取得方法：
                               - $posts->currentPage() : 現在のページ番号
                               - $posts->lastPage() : 最後のページ番号
                               - $posts->total() : 総レコード数
                               - $posts->perPage() : 1ページあたりの件数
                               - $posts->count() : 現在ページの件数
                            
                            4. カスタマイズ方法：
                               - config/app.php で 'locale' => 'ja' に設定すると日本語表示
                               - resources/views/pagination/ でテンプレートをカスタマイズ可能
                               - links('カスタムビュー名') で独自デザインを適用可能
                        --}}
                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                    @else
                    {{-- 投稿が0件の場合の表示 --}}
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg mb-4">まだ投稿がありません。</p>
                        @auth
                        <a href="{{ route('posts.create') }}"
                            class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            最初の投稿を作成する
                        </a>
                        @endauth
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>