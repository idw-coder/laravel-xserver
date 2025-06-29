<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Admin ID -->
        <div>
            <x-input-label for="admin_id" :value="__('管理者ID')" />
            <x-text-input id="admin_id" class="block mt-1 w-full" type="text" name="admin_id" :value="old('admin_id')" required autofocus autocomplete="username" placeholder="管理者IDを入力してください" />
            <x-input-error :messages="$errors->get('admin_id')" class="mt-2" />
        </div>

        <!-- Original Name field (commented out)
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        -->

        <!-- Original Email Address field (commented out)
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        -->

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password"
                placeholder="8文字以上で入力してください" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('パスワード確認')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password"
                placeholder="パスワードを再入力してください" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('すでにアカウントをお持ちですか？') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('登録') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>