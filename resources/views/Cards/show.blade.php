@extends('layouts.master')

@section('content')
<section class="section" style="min-height: 100vh; padding-top: 50px;">
    <div class="container-narrow">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: start;">
            
            <!-- LEFT: Holographic Card Art -->
            <div style="position: relative;">
                <div class="tcard shadow-brick-cyan anim-float" style="width: 100%; padding: 20px; background: hsl(var(--card)/.9); max-width: 400px; margin: 0 auto;">
                    <img src="{{ $card->image_url }}" alt="{{ $card->name }}" style="width: 100%; border: 4px solid var(--ink-c); box-shadow: 0 0 30px rgba(0,0,0,0.8);">
                </div>
            </div>

            <!-- RIGHT: The Market Data Feed -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <span class="tag yellow" style="width: fit-content;"><span class="label">&gt; artifact.detected</span></span>
                
                <h1 class="hero-h1" style="font-size: 40px; line-height: 1;">
                    <span class="row1 text-stack">{{ $card->name }}</span>
                </h1>

                <div style="background: hsl(var(--card)/.6); border-left: 4px solid var(--a5); padding: 20px;">
                    <p class="mono" style="color: var(--chrome-c); line-height: 1.8;">
                        &gt; TYPE: {{ $card->type }}<br>
                        &gt; CATEGORY: {{ strtoupper($card->category->name ?? 'UNKNOWN') }}<br>
                        &gt; PASSCODE: {{ $card->passcode }}
                    </p>
                </div>

                <p class="lede" style="font-size: 14px;">{{ $card->description }}</p>

                <!-- THE MARKETPLACE (Moataz's Listings) -->
                <div style="border: 4px solid var(--ink-c); padding: 24px; background: var(--bg); box-shadow: 6px 6px 0 var(--a3);">
                    <h4 class="mono" style="color: var(--a3); margin-bottom: 16px; font-size: 20px;">&gt; ACTIVE_SELLERS</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @forelse($listings as $listing)
                            <div style="border-bottom: 1px solid var(--a5); padding-bottom: 15px; margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="color: var(--a5); font-family: 'Share Tech Mono';">DUELIST: {{ $listing->seller->name }}</span><br>
                                        <span style="color: var(--chrome-c); font-size: 12px;">COND: {{ $listing->condition }} | QTY: {{ $listing->quantity }}</span><br>
                                        <span style="color: var(--a3); font-size: 14px; text-shadow: 0 0 5px var(--a3);">
                                            Reputation: {{ number_format((float)($listing->seller->reputation_score ?? 5), 2) }} / 5.00 ★
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <span class="font-display" style="color: var(--a3); font-size: 20px;">{{ $listing->price }} DT</span>
                                        @if(auth()->check() && auth()->id() !== $listing->seller_id)
                                            <button onclick="openChatWith({{ $listing->seller->id }}, '{{ addslashes($listing->seller->name) }}')" class="btn outline sm" style="border-color: var(--a5); color: var(--a5);">
                                                <span class="inner">NEGOTIATE</span>
                                            </button>
                                        @endif
                                        <form action="{{ route('cart.add') }}" method="POST" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                            <button type="submit" class="btn cyan sm" onclick="showNotification('> INITIATING_TRANSACTION', false)"><span class="inner">BUY</span></button>
                                        </form>
                                    </div>
                                </div>

                                <!-- [TECH LEAD FIX]: DISPLAY THE ACTUAL COMMENTS -->
                                @php
                                    // Fetch the 3 most recent reviews for this specific seller
                                    $sellerReviews = \App\Models\Review::where('seller_id', $listing->seller_id)->with('reviewer')->latest()->take(3)->get();
                                @endphp
                                
                                @if($sellerReviews->count() > 0)
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--a5);">
                                        <p class="mono" style="color: var(--a4); font-size: 12px; margin-bottom: 10px;">&gt; RECENT REVIEWS:</p>
                                        @foreach($sellerReviews as $rev)
                                            <div style="background: rgba(0,0,0,0.3); padding: 10px; margin-bottom: 5px; border-left: 2px solid var(--a3);">
                                                <p style="font-size: 11px; color: var(--chrome-c); margin-bottom: 5px;">
                                                    <span style="color: var(--a3);">{{ $rev->rating }}.00 ★</span> 
                                                    - {{ $rev->reviewer->name ?? 'Unknown' }}
                                                </p>
                                                <p style="font-size: 12px; color: var(--chrome-c); font-family: 'Share Tech Mono';">"{{ $rev->comment }}"</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="mono" style="color: var(--a1);">&gt; NO ACTIVE LISTINGS FOUND FOR THIS ARTIFACT.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection