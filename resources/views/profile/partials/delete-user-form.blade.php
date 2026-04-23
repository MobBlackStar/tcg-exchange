<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium" style="color: var(--a1);">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm" style="color: var(--chrome-c);">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button class="btn red sm" style="border-color: var(--a1); color: var(--a1); background: transparent;"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        <span class="inner">{{ __('DELETE ACCOUNT') }}</span>
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" style="background: var(--bg); border: 4px solid var(--a1);">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium" style="color: var(--a1);">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm" style="color: var(--chrome-c);">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    style="background: var(--ink-c); color: var(--chrome-c); border: 2px solid var(--a1);"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" style="color: var(--a1);" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="btn outline sm" x-on:click="$dispatch('close')">
                    <span class="inner">{{ __('CANCEL') }}</span>
                </button>

                <button type="submit" class="btn magenta sm" style="margin-left: 15px;">
                    <span class="inner">{{ __('DELETE ACCOUNT') }}</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>