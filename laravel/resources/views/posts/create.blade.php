<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('新規投稿作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('posts.store') }}">
                        @csrf

                        <!-- タイトル -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                タイトル <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                id="title"
                                name="title"
                                value="{{ old('title') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                                placeholder="投稿のタイトルを入力してください">
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 概要 -->
                        <div class="mb-6">
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                概要（抜粋）
                            </label>
                            <textarea id="excerpt"
                                name="excerpt"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('excerpt') border-red-500 @enderror"
                                placeholder="投稿の概要を入力してください（任意）">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 本文 -->
                        <div class="mb-6">
                            <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                本文 <span class="text-red-500">*</span>
                            </label>
                            <textarea id="body"
                                name="body"
                                rows="15"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('body') border-red-500 @enderror"
                                placeholder="投稿の本文を入力してください">{{ old('body') }}</textarea>
                            @error('body')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ステータス -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                ステータス <span class="text-red-500">*</span>
                            </label>
                            <select id="status"
                                name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>下書き</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>公開</option>
                            </select>
                            @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ボタン -->
                        <div class="flex justify-between">
                            <a href="{{ route('posts.index') }}"
                                class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                                キャンセル
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                投稿を作成
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>