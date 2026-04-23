@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <span class="tag magenta"><span class="label">&gt; USER_HEART_ARCHIVE</span></span>
            <h1 class="h2 text-stack-sm" style="font-size: 80px; margin: 20px 0;">FAVORITE<br><span style="color: var(--a5);">ARTIFACTS.</span></h1>
        </div>

        <div class="features-grid">
            @forelse($favorites as $fav)
                <div class="tcard shadow-brick-magenta wishlisted">
                    <div class="titlebar" style="display: flex; justify-content: space-between; padding: 8px 15px; border-bottom: 4px solid var(--ink-c);">
                        <span class="mono" style="color:var(--a4); font-size: 10px;">LOCKED_SIGNAL</span>
                        <!-- Un-heart button -->
                        <button class="btn-wishlist active" onclick="toggleWishlist(event, {{ $fav->card->id }})" style="background: none; border: none; color: var(--a1); cursor: pointer;">
                            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        </button>
                    </div>
                    <div class="body" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 24px;">
                        <img src="{{ $fav->card->image_url }}" style="width: 180px; border: 3px solid #000; margin-bottom: 20px;">
                        <h3 style="font-size: 1.1rem;">{{ $fav->card->name }}</h3>
                        <a href="{{ route('card.show', $fav->card->id) }}" class="btn outline sm full" style="margin-top: 15px;"><span class="inner">View Market Data</span></a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px; border: 2px dashed var(--a4);">
                    <p class="lede" style="color: var(--a4);">&gt; NO SIGNALS DETECTED IN THE HEART VAULT.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection