<style>
    .neon-input-cyan {
        width: 100%; 
        max-width: 500px; /* Ergonomic constraint */
        padding: 14px 18px; 
        background: rgba(0, 0, 0, 0.4); 
        border: 2px solid hsl(var(--accent-5) / 0.5); 
        color: var(--chrome-c); 
        font-family: 'Share Tech Mono', monospace; 
        font-size: 1.1rem; 
        outline: none; 
        transition: all 0.2s ease-in-out;
    }
    .neon-input-cyan:focus {
        border-color: var(--a5);
        box-shadow: 0 0 15px hsl(var(--accent-5) / 0.4);
        background: rgba(0, 0, 0, 0.8);
        transform: translateX(5px); /* Cyberpunk sliding effect */
    }
    .form-label {
        display: block; color: var(--chrome-c); font-family: 'Share Tech Mono', monospace; 
        font-size: 1rem; text-transform: uppercase; margin-bottom: 8px; opacity: 0.85;
    }
</style>

<section>
    <header style="margin-bottom: 30px;">
        <h2 style="color: var(--chrome-c); font-size: 1.6rem; font-family: 'Orbitron', sans-serif; text-transform: uppercase;">
            {{ __('Profile Information') }}
        </h2>
        <p style="color: var(--a5); font-family: 'Share Tech Mono', monospace; font-size: 0.95rem; margin-top: 5px;">
            &gt; Update your account's profile information and email address.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="display: flex; flex-direction: column; gap: 25px;">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="form-label">&gt; CALLSIGN (Name)</label>
            <input id="name" name="name" type="text" class="neon-input-cyan" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" style="color: var(--a1);" />
        </div>

        <div>
            <label for="email" class="form-label">&gt; NETWORK_ID (Email)</label>
            <input id="email" name="email" type="email" class="neon-input-cyan" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" style="color: var(--a1);" />
        </div>

        <div style="display: flex; align-items: center; gap: 20px; margin-top: 10px;">
            <button class="btn cyan md"><span class="inner">{{ __('SAVE DATA') }}</span></button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="color: var(--a5); font-family: 'Share Tech Mono', monospace; font-size: 1rem; margin: 0; text-shadow: 0 0 8px var(--a5);">
                    &gt; SAVED SUCCESSFULLY.
                </p>
            @endif
        </div>
    </form>
</section>