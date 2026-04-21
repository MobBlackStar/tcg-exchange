@extends('layouts.master')

@section('content')
<section class="section" style="min-height: 100vh; display: flex; flex-direction: column; align-items: center;">
    <div class="pattern-dots" style="position:absolute;inset:0;opacity:.2;pointer-events:none"></div>
    
    <div class="container" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        
        <!-- HEADER (Centered) -->
        <div class="heading-block" style="text-align: center; margin-bottom: 40px;">
            <span class="tag yellow" style="margin-bottom:24px"><span class="label">&gt; module.catalog</span></span>
            <h2 class="h2 text-stack-sm">Card <br/><span class="accent-m">Database.</span></h2>
            <p class="lede" style="margin: 24px auto 0 auto;">&gt; 14,322 cards detected. Initializing grid...</p>
        </div>

        <!-- SEARCH & FILTER BAR (Centered & Responsive) -->
        <form method="GET" action="{{ route('catalog') }}" style="display:flex; justify-content: center; gap:16px; margin-bottom: 64px; width: 100%; max-width: 1000px; background:hsl(var(--card)/.8); padding: 24px; border: 4px solid var(--ink-c); box-shadow: 6px 6px 0 var(--ink-c), 12px 12px 0 var(--a3);">
            
            <input type="text" name="search" value="{{ request('search') }}" placeholder="&gt; Search name..." style="flex:2; min-width: 200px; padding: 12px; background: var(--bg); color: var(--chrome-c); border: 2px solid var(--ink-c); font-family:'Share Tech Mono',monospace;">
            
            <select name="category" style="flex:1; padding: 12px; background: var(--bg); color: var(--a5); border: 2px solid var(--ink-c); font-family:'Share Tech Mono',monospace;">
                <option value="">ALL TYPES</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ strtoupper($cat->name) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn cyan sm" style="flex: 0.5;"><span class="inner">Execute</span></button>
        </form>

        <!-- THE CARD GRID (Centered) -->
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; width: 100%; justify-items: center;">
            @forelse($cards as $card)
                @php
                    $glow = 'shadow-brick';
                    if($card->category->name == 'Monster') $glow = 'shadow-brick-yellow';
                    if($card->category->name == 'Spell') $glow = 'shadow-brick-cyan';
                    if($card->category->name == 'Trap') $glow = 'shadow-brick-magenta';
                @endphp

                <div class="tcard {{ $glow }}" style="width: 100%; max-width: 320px;">
                    <div class="titlebar">
                        <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                        <span class="ttl">&gt; {{ $card->passcode }}.bin</span>
                    </div>
                    <div class="body" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 24px;">
                        <img src="{{ $card->image_url }}" alt="{{ $card->name }}" style="width: 200px; height: auto; border: 4px solid var(--ink-c); margin-bottom: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
                        <h3 style="font-size: 1.1rem; min-height: 2.5rem; display: flex; align-items: center;">{{ $card->name }}</h3>
                        <p class="mono" style="font-size: 0.8rem; color: var(--a5); opacity: 0.8;">[{{ $card->type }}]</p>
                        
                        <div style="margin-top: 24px; width: 100%;">
                            <a href="#" class="btn outline sm full"><span class="inner">Market Data</span></a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px;">
                    <h3 class="text-stack" style="color: var(--a1);">NO DATA FOUND</h3>
                    <p class="lede">&gt; The search term yielded zero results in the archives.</p>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION (Centered) -->
        <div style="margin: 80px 0; display: flex; justify-content: center; width: 100%; font-family: 'Share Tech Mono', monospace;">
            {{ $cards->links() }}
        </div>

    </div>
</section>
@endsection