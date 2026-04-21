@extends('layouts.master')

@section('content')
<section class="section" style="height: 80vh; display: flex; align-items: center; justify-content: center; text-align: center;">
    <div class="container-tight">
        <div class="shadow-brick-red" style="background: var(--bg); border: 4px solid var(--a1); padding: 60px; position: relative; overflow: hidden;">
            
            <!-- GLITCH EFFECT OVERLAY -->
            <div class="pattern-stripes" style="position:absolute; inset:0; opacity:0.2; pointer-events:none;"></div>
            
            <h1 class="font-display anim-wiggle" style="font-size: 120px; color: var(--a1); line-height: 1; margin: 0;">404</h1>
            <h2 class="h2 text-stack-sm" style="font-size: 30px; margin-top: 20px; color: var(--chrome-c);">SIGNAL_LOST</h2>
            
            <p class="mono" style="margin-top: 32px; color: var(--a1); opacity: 0.8;">
                &gt; ERROR_CODE: 0xTCG_VOID<br>
                &gt; DESTINATION: UNREACHABLE<br>
                &gt; ACTION: RE-ESTABLISH CONNECTION
            </p>

            <div style="margin-top: 48px;">
                <a href="{{ route('catalog') }}" class="btn magenta lg">
                    <span class="inner">Return to Catalog</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection