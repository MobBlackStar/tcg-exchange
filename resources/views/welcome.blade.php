@extends('layouts.master')

@section('content')
<section class="hero" id="top">
    <div class="float-layer" aria-hidden="true">
        <div class="shape circle bgc-4 anim-float" style="top:10%;left:6%;width:56px;height:56px"></div>
        <div class="shape sq-rot bgc-3 anim-float-rev" style="top:20%;left:88%;width:44px;height:44px"></div>
        <div class="shape tri bgc-5 anim-wiggle" style="top:70%;left:4%;width:60px;height:60px"></div>
    </div>

    <span class="hero-bgword" aria-hidden="true">DUEL</span>

    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="tag"><span class="dot anim-pulse-glow"></span><span class="label">v.2026 // online</span></span>

            <h1 class="hero-h1">
                <span class="row1 text-stack">Trade Cards.</span>
                <span class="row2 text-gradient">Build Decks.</span>
                <span class="row3">Total <em>Domination.</em></span>
            </h1>

            <p class="hero-sub">&gt; The premier peer-to-peer Trading Card marketplace. Over 14,000 cards in the database. <span class="cur anim-blink">_</span></p>

            <div class="hero-ctas">
                <a href="/catalog" class="btn magenta lg"><span class="inner">Enter Catalog</span></a>
            </div>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif

        <div class="hero-art">
            <div class="bg"></div>
            <div class="stripe pattern-stripes-light"></div>
            <div class="circle-r anim-pulse-glow"></div>
            <div class="square-y"></div>
            <div class="center"><div class="tri" style="clip-path:polygon(50% 0%, 0% 100%, 100% 100%); width:64px; height:64px; background:var(--a5);"></div></div>
            <div class="dot-stack"><span class="d1"></span><span class="d2"></span><span class="d3"></span></div>
        </div>
    </div>
</section>
@endsection