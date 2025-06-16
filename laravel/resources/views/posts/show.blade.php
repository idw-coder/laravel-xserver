{{--
    投稿詳細ページ
    - 投稿の全内容を表示
    - 作成者情報と公開日時を表示
    - 作成者には編集・削除ボタンを表示（今後実装予定）
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    投稿詳細
                </h2>
                {{-- パンくずナビゲーション --}}
                <nav class="text-sm text-gray-500 mt-1">
                    <a href="{{ route('posts.index') }}" class="hover:text-gray-700">投稿一覧</a>
                    <span class="mx-2">›</span>
                    <span class="text-gray-700">{{ Str::limit($post->title, 30) }}</span>
                </nav>
            </div>

            {{-- 作成者のみに表示される管理ボタン --}}
            @auth
            @if(auth()->id() === $post->user_id)
            <div class="flex space-x-2">
                {{-- 編集ボタン（今後実装予定） --}}
                <a href="#"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors opacity-50 cursor-not-allowed"
                    title="編集機能は準備中です">
                    編集
                </a>
                {{-- 削除ボタン（今後実装予定） --}}
                <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors opacity-50 cursor-not-allowed"
                    title="削除機能は準備中です">
                    削除
                </button>
            </div>
            @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- 成功メッセージ表示（編集・削除後に使用予定） --}}
            @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif

            <div class="px-4 py-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <article class="p-8">
                    {{-- 投稿ヘッダー --}}
                    <header class="mb-8">
                        {{-- タイトル --}}
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">
                            {{ $post->title }}
                        </h1>

                        {{-- メタ情報 --}}
                        <div class="flex flex-wrap items-center text-sm text-gray-600 space-x-4">
                            {{-- 作成者 --}}
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                投稿者: {{ $post->author->name }}
                            </div>

                            {{-- 公開日時 --}}
                            @if($post->published_at)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                公開日: {{ $post->published_at->format('Y年n月j日 H:i') }}
                            </div>
                            @endif

                            {{-- ステータス --}}
                            <div class="flex items-center">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($post->status === App\Enums\PostStatus::PUBLISHED) bg-green-100 text-green-800
                                    @elseif($post->status === App\Enums\PostStatus::DRAFT) bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $post->status->label() }}
                                </span>
                            </div>

                            {{-- 更新日時（作成日時と異なる場合のみ表示） --}}
                            @if($post->updated_at->gt($post->created_at))
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                最終更新: {{ $post->updated_at->format('Y年n月j日 H:i') }}
                            </div>
                            @endif
                        </div>
                    </header>

                    {{-- 概要（excerpt）がある場合は表示 --}}
                    @if($post->excerpt)
                    <div class="bg-gray-50 border-l-4 border-blue-500 p-4 mb-8">
                        <p class="text-gray-700 font-medium">{{ $post->excerpt }}</p>
                    </div>
                    @endif

                    {{-- 投稿本文 --}}
                    <div class="max-w-none">
                        {{-- 本文の表示：nl2br()で改行を<br>タグに変換 --}}
                        <div class="text-gray-800 leading-relaxed whitespace-pre-wrap text-lg">
                            {!! nl2br(e($post->body)) !!}
                        </div>
                    </div>

                    {{-- 投稿フッター --}}
                    <footer class="mt-12 pt-8 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            {{-- 戻るボタン --}}
                            <a href="{{ route('posts.index') }}"
                                class="inline-flex items-center px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                </svg>
                                投稿一覧に戻る
                            </a>

                            {{-- 投稿情報 --}}
                            <div class="text-sm text-gray-500">
                                <p>スラッグ: <code class="bg-gray-100 px-2 py-1 rounded">{{ $post->slug }}</code></p>
                            </div>
                        </div>
                    </footer>
                </article>
            </div>
        </div>
    </div>
</x-app-layout>