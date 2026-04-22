@extends('layouts.master')

@section('content')
<section class="section" style="min-height: 100vh; display: flex; flex-direction: column; align-items: center;">
    <div class="pattern-stripes" style="position:absolute;inset:0;opacity:.1;pointer-events:none"></div>

    <div class="container" style="display: flex; flex-direction: column; gap: 40px; align-items: center;">
        
        <!-- Header -->
        <div class="heading-block" style="text-align: center; margin-bottom: 20px;">
            <span class="tag cyan-on-dark" style="margin-bottom:24px"><span class="label">&gt; module.market</span></span>
            <h2 class="h2 text-stack-sm">Market <span class="accent-c">Data.</span></h2>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 40px; width: 100%; justify-content: center; align-items: flex-start;">
            
            <!-- LEFT COLUMN: The Card Itself -->
            <div class="tcard shadow-brick-yellow" style="width: 100%; max-width: 400px; padding: 24px; text-align: center;">
                <div class="corner square bgc-3"></div>
                <h3 style="color: var(--a3); margin-bottom: 10px;">{{ $card->name }}</h3>
                <p class="mono" style="color: var(--chrome-c); opacity: 0.8; margin-bottom: 20px;">Passcode: {{ $card->passcode }}</p>
                <img src="{{ $card->image_url }}" alt="{{ $card->name }}" style="width: 100%; max-width: 300px; border: 4px solid var(--ink-c); box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                <div style="margin-top: 20px; background: var(--bg); padding: 15px; border: 2px solid var(--ink-c); text-align: left; color: var(--chrome-c); font-size: 0.9rem;">
                    {!! nl2br(e($card->description)) !!}
                </div>
            </div>

            <!-- RIGHT COLUMN: The Active Sellers -->
            <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 20px;">
                <h3 class="h2 text-stack-sm" style="font-size: 2rem;">Active <span class="accent-m">Listings.</span></h3>
                
                @forelse($listings as $listing)
                    <div class="tcard shadow-brick" style="padding: 20px; display: flex; justify-content: space-between; align-items: center; border-left: 6px solid var(--a4);">
                        
                        <div>
                            <h4 style="color: var(--chrome-c); font-size: 1.2rem;">Seller: <span style="color: var(--a5);">{{ $listing->seller->name }}</span></h4>
                            <p style="font-family: 'Share Tech Mono'; color: var(--a3); font-size: 0.9rem;">
                                Cond: {{ $listing->condition }} | Qty Available: {{ $listing->quantity }}
                            </p>
                            <p style="font-family: 'Share Tech Mono'; color: var(--chrome-c); font-size: 0.8rem; margin-top: 5px;">
                                Seller Rating: {{ $listing->seller->reputation_score }} / 5.00
                            </p>
                        </div>

                        <div style="text-align: right; display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                            <span class="font-display" style="font-size: 1.8rem; color: var(--a1);">{{ $listing->price }} DT</span>
                            
                            <!-- Moataz's Add To Cart Form -->
                            <form action="{{ route('cart.add') }}" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                @csrf
                                <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                <button type="submit" class="btn magenta sm"><span class="inner">ADD TO CART</span></button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="tcard" style="padding: 40px; text-align: center; border-color: var(--a1);">
                        <p style="color: var(--a1); font-family: 'Share Tech Mono';">&gt; ERROR: NO ACTIVE LISTINGS FOUND.</p>
                        <p style="color: var(--chrome-c); font-size: 0.9rem; margin-top: 10px;">No duelists are currently selling this card.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection