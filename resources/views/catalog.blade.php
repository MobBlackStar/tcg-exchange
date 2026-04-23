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
        <form method="GET" action="{{ route('catalog') }}" style="display:flex; flex-wrap: wrap; justify-content: center; gap:16px; margin: 0 auto 64px auto; width: 100%; max-width: 1000px; background:hsl(var(--card)/.8); padding: 24px; border: 4px solid var(--ink-c); box-shadow: 6px 6px 0 var(--ink-c), 12px 12px 0 var(--a3); position: relative; z-index: 100;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="&gt; Search name..." style="flex:2; min-width: 200px; padding: 12px; background: #000; color: #fff; border: 2px solid var(--a5); font-family:'Share Tech Mono',monospace;">
            
            <select name="category" style="flex:1; min-width: 150px; padding: 12px; background: #000; color: var(--a5); border: 2px solid var(--ink-c); font-family:'Share Tech Mono',monospace;">
                <option value="">ALL TYPES</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ strtoupper($cat->name) }}</option>
                @endforeach
            </select>

            <select name="sort" style="flex:1; min-width: 150px; padding: 12px; background: #000; color: var(--a3); border: 2px solid var(--ink-c); font-family:'Share Tech Mono',monospace;">
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>PRICE: LOW TO HIGH</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>PRICE: HIGH TO LOW</option>
            </select>

            <button type="submit" class="btn cyan sm" style="flex: 0.5; min-width: 120px;"><span class="inner">Execute</span></button>
        </form>

        <!-- THE CARD GRID -->
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; width: 100%; justify-items: center; position: relative; z-index: 10;">
            @forelse($cards as $card)
                @php
                    $glow = 'shadow-brick';
                    if(isset($card->category) && $card->category->name == 'Monster') $glow = 'shadow-brick-yellow';
                    if(isset($card->category) && $card->category->name == 'Spell') $glow = 'shadow-brick-cyan';
                    if(isset($card->category) && $card->category->name == 'Trap') $glow = 'shadow-brick-magenta';
                    
                    // Wishlist Check (WITH PROPER BRACKETS)
                    $isWishlisted = false;
                    if(auth()->check() && $card->wishlistedBy()->where('user_id', auth()->id())->exists()) {
                        $isWishlisted = true;
                    } // <-- THIS BRACKET WAS MISSING!
                    
                    // Advanced Ownership & Quantity Checks (NO ROGUE CLONES)
                    $activeListings = $card->listings()->where('is_active', true)->where('quantity', '>', 0)->get();
                    $totalQuantity = $activeListings->sum('quantity');
                    $isSelling = auth()->check() && $activeListings->where('seller_id', auth()->id())->isNotEmpty();
                @endphp

                <div class="tcard {{ $glow }} {{ $isWishlisted ? 'wishlisted' : '' }}" style="width: 100%; max-width: 320px;">
                    
                    <div class="titlebar" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 15px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                            <span class="ttl" style="font-size: 10px; font-family: 'Share Tech Mono', monospace;">&gt; {{ $card->passcode }}.bin</span>
                        </div>
                        
                        @auth
                        <button class="btn-wishlist {{ $isWishlisted ? 'active' : '' }}" onclick="toggleWishlist(event, {{ $card->id }})" title="Toggle Wishlist" style="background: none; border: none; cursor: pointer; transition: all 0.3s; color: {{ $isWishlisted ? 'var(--a1)' : 'var(--chrome-c)' }};">
                            <svg style="width: 18px; height: 18px; fill: {{ $isWishlisted ? 'var(--a1)' : 'none' }}; transition: fill 0.2s, color 0.2s;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                        @endauth
                    </div>

                    <div class="body" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 24px;">
                        
                        @if($isSelling)
                            <div class="tag magenta" style="margin-bottom: 15px; width: 100%; justify-content: center;">
                                <span class="label" style="color: #fff;">&gt; YOUR LISTING ACTIVE</span>
                            </div>
                        @endif

                        <div style="position: relative; display: inline-block;">
                            <img src="{{ $card->image_url }}" alt="{{ str_replace('"', '', $card->name) }}" style="width: 180px; height: auto; border: 3px solid #000; margin-bottom: 15px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
                            
                            <!-- MARKET VALUE BADGE -->
                            <div style="position: absolute; top: -10px; right: -15px; background: var(--a5); color: var(--ink-c); padding: 4px 8px; font-family: 'Share Tech Mono', monospace; font-weight: bold; font-size: 0.9rem; border: 2px solid var(--ink-c); transform: skewX(-10deg); box-shadow: 4px 4px 0 var(--ink-c);">
                                @if($card->listings_min_price)
                                    💎 {{ $card->listings_min_price }} DT
                                @else
                                    💎 N/A
                                @endif
                            </div>
                        </div>

                        <h3 style="font-size: 1.1rem; min-height: 2.5rem; display: flex; align-items: center; margin-bottom: 5px; font-family: 'Outfit', sans-serif;">{{ $card->name }}</h3>
                        
                        <p class="mono" style="font-size: 11px; color: var(--chrome-c); margin-bottom: 5px;">QTY AVAIL: <span style="color: var(--a3); font-weight: bold;">{{ $totalQuantity }}</span></p>
                        
                        <p class="mono" style="font-size: 11px; color: var(--a5); margin-bottom: 20px;">[{{ strtoupper($card->category->name ?? 'UNKNOWN') }}]</p>
                        
                        <div style="width: 100%;">
                            <a href="{{ route('cards.show', $card->id) }}" class="btn outline sm full" style="text-decoration: none; border: 2px solid var(--a5); color: var(--a5); padding: 8px; font-family: 'Share Tech Mono';">
                                <span class="inner">Market Data</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px;">
                    <h3 class="text-stack" style="color: var(--a1); font-family: 'Orbitron';">NO DATA FOUND</h3>
                </div>
            @endforelse
        </div>

        <div style="margin: 80px 0; display: flex; justify-content: center; width: 100%;">
            {{ $cards->links() }}
        </div>

    </div>
</section>
@endsection