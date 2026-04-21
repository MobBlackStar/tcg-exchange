@extends('layouts.master')

@section('content')
<div class="container-narrow" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="tcard shadow-brick-magenta" style="width: 100%; max-width: 450px;">
        <div class="titlebar">
            <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
            <span class="ttl">&gt; system.auth.login</span>
        </div>
        <div class="corner square bgc-4"></div>
        <div class="body" style="padding: 40px;">
            
            <h2 class="h2 text-stack-sm" style="font-size: 2rem; margin-bottom: 30px; text-align: center;">ACCESS <br/><span class="accent-m">VOID</span></h2>

            <!-- Session Status (Handled by Breeze) -->
            <x-auth-session-status style="color: var(--a5); margin-bottom: 15px;" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; EMAIL_ADDRESS</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('email')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; PASSWORD</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('password')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Remember Me -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input id="remember_me" type="checkbox" name="remember" style="accent-color: var(--a5);">
                    <label for="remember_me" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c); font-size: 0.9rem;">Keep session active</label>
                </div>

                <!-- Actions -->
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 10px;">
                    <button type="submit" class="btn magenta md full">
                        <span class="inner">INITIALIZE LINK</span>
                    </button>
                    
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="text-align: center; font-family: 'Share Tech Mono', monospace; color: var(--chrome-c); font-size: 0.8rem; text-decoration: underline;">
                            &gt; Recover lost cipher?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection