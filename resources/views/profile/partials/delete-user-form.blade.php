<section class="space-y-6" style="text-align: center;">
    <header>
        <h2 class="text-lg font-medium" style="color: var(--a1); font-size: 1.5rem; font-family: 'Orbitron', sans-serif;">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-sm" style="color: var(--chrome-c); font-family: 'Share Tech Mono', monospace; font-size: 1rem; margin-bottom: 20px;">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <div style="display: flex; justify-content: center;">
    <button class="btn outline sm" style="border-color: var(--a1); color: var(--a1);"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        <span class="inner">{{ __('INITIATE SELF DESTRUCT') }}</span>
    </button>

    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding: 30px; background: var(--bg); border: 4px solid var(--a1); box-shadow: 0 0 30px rgba(255,0,0,0.5); text-align: center;">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium" style="color: var(--a1); font-size: 1.5rem; font-family: 'Orbitron', sans-serif;">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm" style="color: var(--chrome-c); font-family: 'Share Tech Mono', monospace; font-size: 1rem; margin-bottom: 20px; margin-top: 10px;">
                {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div style="margin-top: 20px; display: flex; justify-content: center;">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    style="max-width: 400px; width: 100%; padding: 12px; background: var(--ink-c); color: var(--chrome-c); border: 2px solid var(--a1); font-family: 'Share Tech Mono', monospace; font-size: 1.1rem;"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" style="color: var(--a1); margin-top: 10px;" />
            </div>

            <div style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 15px;">
                <button type="button" class="btn outline sm" x-on:click="$dispatch('close')">
                    <span class="inner">{{ __('CANCEL') }}</span>
                </button>

                <button type="submit" class="btn red sm">
                    <span class="inner">{{ __('CONFIRM DELETION') }}</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>