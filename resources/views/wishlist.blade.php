@extends('layouts.master')

@section('content')
<section class="section" style="padding-top: 50px; min-height: 100vh;">
    <div class="container">
        
        <div style="text-align: center; margin-bottom: 60px;">
            <span class="tag magenta"><span class="label">&gt; USER_HEART_ARCHIVE</span></span>
            <h1 class="h2 text-stack-sm" style="font-size: 80px; margin: 20px 0;">FAVORITE<br><span style="color: var(--a5);">ARTIFACTS.</span></h1>
        </div>

        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; align-items: stretch;">
            @forelse($favorites as $fav)
                <!-- [TECH LEAD FIX]: display: flex and flex-direction: column ensures height stretches uniformly -->
                <div class="tcard shadow-brick-magenta wishlisted" style="display: flex; flex-direction: column; height: 100%;">
                    
                    <!-- TITLEBAR -->
                    <div class="titlebar" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border-bottom: 4px solid var(--ink-c);">
                        <span class="mono" style="color:var(--a4); font-size: 11px;">LOCKED_SIGNAL</span>
                        
                        <!-- THE NEW HEART BUTTON -->
                        <button class="btn-wishlist active" onclick="toggleWishlist(event, {{ $fav->id }})" title="Remove from Wishlist">
                            <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </button>
                    </div>
                    
                    <!-- BODY (Flex-grow 1 pushes button to bottom) -->
                    <div class="body" style="flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 24px;">
                        <img src="{{ $fav->image_url }}" alt="{{ $fav->name }}" style="width: 180px; height: auto; border: 3px solid #000; margin-bottom: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.5);">
                        
                        <!-- margin-bottom: auto pushes the button below it to the absolute bottom -->
                        <h3 style="font-size: 1.2rem; font-family: 'Outfit'; margin-bottom: auto;">{{ $fav->name }}</h3>
                        
                        <div style="width: 100%; margin-top: 20px;">
                            <a href="{{ route('cards.show', $fav->id) }}" class="btn outline sm full">
                                <span class="inner">View Market Data</span>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px; border: 2px dashed var(--a4); background: rgba(0,0,0,0.5);">
                    <p class="lede" style="color: var(--a4); font-size: 1.2rem;">&gt; NO SIGNALS DETECTED IN THE HEART VAULT.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
@endsection