@extends('layouts.master')

@section('content')
<section class="section" style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding-top: 100px;">
    <div class="pattern-dots" style="position:absolute;inset:0;opacity:.2;pointer-events:none"></div>
    
    <div class="container" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        
        <!-- HEADER -->
        <div class="heading-block" style="text-align: center; margin-bottom: 40px;">
            <span class="tag yellow" style="margin-bottom:24px"><span class="label">&gt; module.catalog</span></span>
            <h2 class="h2 text-stack-sm">Card <br/><span class="accent-m">Database.</span></h2>
            <p class="lede" style="margin: 24px auto 0 auto;">&gt; 14,322 cards detected. Initializing grid...</p>
        </div>

        <!-- SEARCH & FILTER BAR -->
        <form method="GET" action="{{ route('catalog') }}" style="display:flex; justify-content: center; gap:16px; margin-bottom: 64px; width: 100%; max-width: 1000px; background:hsl(var(--card)/.8); padding: 24px; border: 4px solid var(--ink-c); box-shadow: 6px 6px 0 var(--ink-c), 12px 12px 0 var(--a3);">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="&gt; Search name..." style="flex:2; min-width: 200px; padding: 12px; background: #000; color: #fff; border: 2px solid var(--a5); font-family:'Share Tech Mono',monospace;">
            
            <select name="category" style="flex:1; padding: 12px; background: #000; color: var(--a5); border: 2px solid var(--ink-c); font-family:'Share Tech Mono',monospace;">
                <option value="">ALL TYPES</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ strtoupper($cat->name) }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn cyan sm" style="flex: 0.5;"><span class="inner">Execute</span></button>
        </form>

        <!-- THE CARD GRID -->
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; width: 100%; justify-items: center;">
            @forelse($cards as $card)
                @php
                    $glow = 'shadow-brick-yellow';
                    if($card->category->name == 'Spell') $glow = 'shadow-brick-cyan';
                    if($card->category->name == 'Trap') $glow = 'shadow-brick-magenta';
                @endphp

                <div class="tcard {{ $glow }}" style="width: 100%; max-width: 320px;">
                    <!-- TITLEBAR -->
                    <div class="titlebar" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 15px; border-bottom: 4px solid var(--ink-c);">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                            <span class="ttl" style="font-size: 10px; font-family: 'Share Tech Mono', monospace;">&gt; {{ $card->passcode }}.bin</span>
                        </div>
                        <button class="btn-wishlist" onclick="toggleWishlist(event, {{ $card->id }})" title="Add to Wishlist" style="background:none; border: 2px solid var(--a4); color: var(--a4); cursor: pointer; padding: 4px;">
                            ❤
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="body" style="padding: 25px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                        <img src="{{ $card->image_url }}" alt="{{ $card->name }}" style="width: 180px; height: auto; border: 3px solid #000; margin-bottom: 15px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
                        
                        <h3 style="font-size: 1.1rem; min-height: 2.5rem; display: flex; align-items: center; margin-bottom: 5px; font-family: 'Outfit', sans-serif;">{{ $card->name }}</h3>
                        
                        <p class="mono" style="font-size: 11px; color: var(--a5); margin-bottom: 20px; font-family: 'Share Tech Mono', monospace;">[{{ strtoupper($card->type) }}]</p>
                        
                        <!-- ACTION BUTTONS -->
                        <div style="width: 100%; display: flex; flex-direction: column; gap: 10px;">
                            <a href="{{ route('card.show', $card->id) }}" class="btn outline sm full" style="text-decoration: none; border: 2px solid var(--a5); color: var(--a5); padding: 8px; font-family: 'Share Tech Mono';">
                                <span class="inner">Market Data</span>
                            </a>
                            <button onclick="launchNotification('> ADDED_TO_CART')" class="btn yellow sm full" style="padding: 8px; font-family: 'Share Tech Mono';">
                                <span class="inner">+ ADD TO CART</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px;">
                    <h3 class="text-stack" style="color: var(--a1); font-family: 'Orbitron';">NO DATA FOUND</h3>
                    <p class="lede" style="font-family: 'Share Tech Mono';">&gt; The search term yielded zero results in the archives.</p>
                </div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        <div style="margin: 80px 0; display: flex; justify-content: center; width: 100%;">
            {{ $cards->links() }}
        </div>

    </div>
</section>
@endsection