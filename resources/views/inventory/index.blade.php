@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">My <span class="accent-m">Binder.</span></h2>

        <!-- MOATAZ DOMAIN: Add New Listing Form -->
        <form action="{{ route('inventory.store') }}" method="POST" class="tcard" style="padding: 24px; margin-top: 40px; background: hsl(var(--card)/.9);">
            @csrf
            <h3 style="margin-bottom: 20px; font-family: 'Share Tech Mono'; font-size: 1rem; color: var(--a5);">&gt; LIST NEW CARD</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Select Card (From Library):</label>
                <!-- [TECH LEAD FIX]: Limited to 100 to prevent 14,000 tag memory crash -->
                <select name="card_id" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                    @foreach(\App\Models\Card::limit(100)->get() as $card)
                        <option value="{{ $card->id }}">{{ $card->name }} ({{ $card->passcode }})</option>
                    @endforeach
                </select>

                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Price (DT):</label>
                <input type="number" name="price" placeholder="Price (DT)" step="0.01" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">

                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Condition:</label>
                <select name="condition" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                    <option value="Mint">Mint</option>
                    <option value="Near Mint">Near Mint</option>
                    <option value="Lightly Played">Lightly Played</option>
                    <option value="Damaged">Damaged</option>
                </select>

                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" placeholder="Quantity" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">

                <button type="submit" class="btn magenta lg full"><span class="inner">Add to Market</span></button>
            </div>
        </form>

        <!-- MOATAZ DOMAIN: Current Listings -->
        <h3 style="margin: 40px 0 20px 0; font-family: 'Share Tech Mono'; font-size: 1.2rem; color: var(--a3);">&gt; ACTIVE LISTINGS</h3>
        @forelse($myListings as $listing)
            <div class="tcard" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span class="mono" style="color: var(--chrome-c);">{{ $listing->card->name }}</span>
                <span class="mono" style="color: var(--a5);">{{ $listing->price }} DT</span>
                <form action="{{ route('inventory.destroy', $listing->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" style="color: var(--a1); font-family: 'Share Tech Mono'; cursor: pointer; background: none; border: none;">[REMOVE]</button>
                </form>
            </div>
        @empty
            <div class="tcard" style="padding: 20px; text-align: center;">
                <p class="mono" style="color: var(--chrome-c); opacity: 0.7;">&gt; Your binder is empty. Add a card to the market.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection