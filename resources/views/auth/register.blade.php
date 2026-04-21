@extends('layouts.master')

@section('content')
<div class="container-narrow" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="tcard shadow-brick-cyan" style="width: 100%; max-width: 450px;">
        <div class="titlebar">
            <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
            <span class="ttl">&gt; system.auth.register</span>
        </div>
        <div class="corner tri bgc-5"></div>
        <div class="body" style="padding: 40px;">
            
            <h2 class="h2 text-stack-sm" style="font-size: 2rem; margin-bottom: 30px; text-align: center;">NEW <br/><span class="accent-c">DUELIST</span></h2>

            <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; CALLSIGN (NAME)</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('name')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; EMAIL_ADDRESS</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('email')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; PASSWORD</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('password')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" style="font-family: 'Share Tech Mono', monospace; color: var(--chrome-c);">&gt; VERIFY_PASSWORD</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 12px; margin-top: 5px; font-family: 'Share Tech Mono', monospace;">
                    <x-input-error :messages="$errors->get('password_confirmation')" style="color: var(--a1); margin-top: 5px;" />
                </div>

                <!-- Actions -->
                <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 10px;">
                    <button type="submit" class="btn cyan md full">
                        <span class="inner">FORGE ACCOUNT</span>
                    </button>
                    
                    <a href="{{ route('login') }}" style="text-align: center; font-family: 'Share Tech Mono', monospace; color: var(--chrome-c); font-size: 0.8rem; text-decoration: underline;">
                        &gt; Already registered? Authenticate here.
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection