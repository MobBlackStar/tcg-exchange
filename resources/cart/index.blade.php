@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">Your <span class="accent-c">Vault.</span></h2>
        
        @if(session('success'))
            <div class="tag cyan-on-dark" style="margin-bottom:20px; width:100%; text-align:center;">{{ session('success') }}</div>
        @endif

        @if(empty($cart))
            <div class="tcard" style="padding: 40px; text-align: center;">
                <p>&gt; THE VAULT IS EMPTY. FIND CARDS IN THE CATALOG.</p>
                <a href="{{ route('catalog') }}" class="btn magenta sm" style="margin-top:20px"><span class="inner">To Catalog</span></a>
            </div>
        @else
            <div class="tcard" style="padding: 24px;">
                @foreach($cart as $id => $item)
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid var(--ink-c); padding: 15px 0;">
                        <img src="{{ $item['image'] }}" style="width: 60px; height: auto;">
                        <div>
                            <h3 style="font-size: 1rem;">{{ $item['name'] }}</h3>
                            <p style="font-size: 0.8rem;">Cond: {{ $item['condition'] }} | Qty: {{ $item['quantity'] }}</p>
                        </div>
                        <div style="font-family: 'Share Tech Mono';">{{ $item['price'] * $item['quantity'] }} DT</div>
                        
                        <form action="{{ route('cart.remove') }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="listing_id" value="{{ $id }}">
                            <button type="submit" style="color: var(--a1);">[REMOVE]</button>
                        </form>
                    </div>
                @endforeach

                <div style="margin-top: 30px; text-align: right;">
                    <h3 style="font-size: 1.5rem;">TOTAL: {{ $total }} DT</h3>
                    <form action="{{ route('checkout.process') }}" method="POST" style="margin-top: 20px;">
                        @csrf
                        <button type="submit" class="btn magenta lg"><span class="inner">Execute Checkout</span></button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection