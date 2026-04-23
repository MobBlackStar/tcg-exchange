@extends('layouts.master')

@section('content')
<div class="container-narrow" style="display: flex; justify-content: center; align-items: center; min-height: 60vh; padding-top: 50px;">
    <div class="tcard shadow-brick-cyan" style="width: 100%; max-width: 500px; padding: 40px; text-align: center;">
        
        <span class="tag cyan-on-dark" style="margin-bottom: 20px;"><span class="label">&gt; SYSTEM.VERIFICATION</span></span>
        <h2 class="h2 text-stack-sm" style="font-size: 2.5rem; margin-bottom: 20px;">AWAITING <span class="accent-c">CLEARANCE</span></h2>
        
        <p class="mono" style="color: var(--chrome-c); opacity: 0.8; margin-bottom: 30px; line-height: 1.6;">
            &gt; Network ID unverified. Please check your secure communications channel (email) for the verification link.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="tag yellow" style="margin-bottom: 20px; width: 100%; justify-content: center;">
                <span class="label">&gt; NEW VERIFICATION SIGNAL SENT.</span>
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 15px;">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn cyan md full"><span class="inner">RESEND SIGNAL</span></button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn outline sm full" style="border-color: var(--a1); color: var(--a1);"><span class="inner">ABORT & LOG OUT</span></button>
            </form>
        </div>

    </div>
</div>
@endsection