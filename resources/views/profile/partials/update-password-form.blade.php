<style>
    .neon-input-magenta {
        width: 100%; 
        max-width: 500px; 
        padding: 14px 18px; 
        background: rgba(0, 0, 0, 0.4); 
        border: 2px solid hsl(var(--accent-4) / 0.5); 
        color: var(--chrome-c); 
        font-family: 'Share Tech Mono', monospace; 
        font-size: 1.1rem; 
        outline: none; 
        transition: all 0.2s ease-in-out;
    }
    .neon-input-magenta:focus {
        border-color: var(--a4);
        box-shadow: 0 0 15px hsl(var(--accent-4) / 0.4);
        background: rgba(0, 0, 0, 0.8);
        transform: translateX(5px);
    }
</style>

<section>
    <header style="margin-bottom: 30px;">
        <h2 style="color: var(--chrome-c); font-size: 1.6rem; font-family: 'Orbitron', sans-serif; text-transform: uppercase;">
            {{ __('Update Password') }}
        </h2>
        <p style="color: var(--a4); font-family: 'Share Tech Mono', monospace; font-size: 0.95rem; margin-top: 5px;">
            &gt; Ensure your account is using a long, random cipher to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" style="display: flex; flex-direction: column; gap: 25px;">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="form-label">&gt; CURRENT CIPHER</label>
            <input id="update_password_current_password" name="current_password" type="password" class="neon-input-magenta" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" style="color: var(--a1);" />
        </div>

        <div>
            <label for="update_password_password" class="form-label">&gt; NEW CIPHER</label>
            <input id="update_password_password" name="password" type="password" class="neon-input-magenta" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" style="color: var(--a1);" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label">&gt; CONFIRM NEW CIPHER</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="neon-input-magenta" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" style="color: var(--a1);" />
        </div>

        <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
            <button class="btn magenta md"><span class="inner">{{ __('UPDATE CIPHER') }}</span></button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="color: var(--a4); font-family: 'Share Tech Mono', monospace; font-size: 1rem; margin: 0; text-shadow: 0 0 8px var(--a4);">
                    &gt; CIPHER SECURED.
                </p>
            @endif
        </div>
    </form>
</section>