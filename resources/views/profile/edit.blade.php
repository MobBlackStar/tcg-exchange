@extends('layouts.master')

@section('content')
<div class="container-narrow" style="padding-top: 60px; padding-bottom: 60px; text-align: center;">
    
    <div style="margin-bottom: 40px; display: inline-block; text-align: left;">
        <span class="tag cyan-on-dark" style="margin-bottom: 15px;"><span class="label">&gt; SECURE_CONNECTION</span></span>
        <h2 class="h2 text-stack-sm" style="font-size: 3rem;">DATA <span class="accent-m">FORGE</span></h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 40px; align-items: center;">

        <!-- PROFILE INFORMATION FORM -->
        <div class="tcard shadow-brick-cyan">
            <div class="titlebar">
                <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                <span class="ttl">&gt; update_profile.exe</span>
            </div>
            <div class="corner square bgc-5"></div>
            <div class="body" style="padding: 30px; background: var(--ink-c);">
                <!-- We include the default Breeze form, but force text to be white/neon inside it -->
                <div style="color: var(--chrome-c);">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <!-- PASSWORD UPDATE FORM -->
        <div class="tcard shadow-brick-magenta">
            <div class="titlebar">
                <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                <span class="ttl">&gt; change_cipher.sys</span>
            </div>
            <div class="corner circle bgc-4"></div>
            <div class="body" style="padding: 30px; background: var(--ink-c);">
                <div style="color: var(--chrome-c);">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- DELETE ACCOUNT FORM -->
        <div class="tcard shadow-brick-red">
            <div class="titlebar">
                <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                <span class="ttl">&gt; self_destruct.bin</span>
            </div>
            <div class="corner tri bgc-1"></div>
            <div class="body" style="padding: 30px; background: var(--ink-c);">
                <div style="color: var(--chrome-c);">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

    </div>
</div>
@endsection