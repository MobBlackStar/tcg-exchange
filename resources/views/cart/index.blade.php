@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-narrow">
        <!-- HEADER -->
        <span class="tag yellow"><span class="label">&gt; VOID_CART_ACTIVE</span></span>
        <h1 class="h2 text-stack-sm" style="font-size: 60px; margin: 20px 0;">YOUR <span style="color: var(--a5);">CARGO.</span></h1>

        @if(session('success'))
            <div class="tag cyan-on-dark" style="margin-bottom:20px; width:100%; text-align:center;">
                <span class="label">{{ session('success') }}</span>
            </div>
        @endif

        <div style="background: rgba(26,16,46,0.8); border: 4px solid var(--ink-c); padding: 40px; box-shadow: 10px 10px 0 var(--a5);">
            
            @if(empty($cart))
                <!-- EMPTY STATE -->
                <div style="text-align: center; padding: 60px 20px; border: 2px dashed rgba(255,255,255,0.1);">
                    <p class="lede" style="margin-bottom: 30px;">&gt; THE VAULT IS EMPTY. NO ARTIFACTS DETECTED.</p>
                    <a href="{{ route('catalog') }}" class="btn magenta sm">
                        <span class="inner">Return to Catalog</span>
                    </a>
                </div>
            @else
                <!-- THE DYNAMIC NEON TABLE -->
                <table style="width: 100%; border-collapse: collapse; font-family: 'Share Tech Mono', monospace;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--a5); color: var(--a5); text-align: left;">
                            <th style="padding: 15px;">IMAGE</th>
                            <th style="padding: 15px;">ARTIFACT</th>
                            <th style="padding: 15px;">QTY</th>
                            <th style="padding: 15px;">PRICE</th>
                            <th style="padding: 15px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $id => $item)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <td style="padding: 15px;">
                                    <img src="{{ $item['image'] }}" style="width: 50px; border: 1px solid var(--a5);">
                                </td>
                                <td style="padding: 15px;">
                                    <div style="font-weight: bold;">{{ $item['name'] }}</div>
                                    <div style="font-size: 0.7rem; color: var(--a5);">COND: {{ $item['condition'] }}</div>
                                </td>
                                <td style="padding: 15px;">{{ $item['quantity'] }}</td>
                                <td style="padding: 15px; color: var(--a3);">{{ $item['price'] * $item['quantity'] }} DT</td>
                                <td style="padding: 15px;">
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="listing_id" value="{{ $id }}">
                                        <button type="submit" style="color: var(--a1); background:none; border:none; cursor:pointer; font-family: 'Share Tech Mono';">[DROP]</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- TOTAL & CHECKOUT -->
                <div style="margin-top: 40px; border-top: 4px solid var(--ink-c); padding-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <h3 class="mono">TOTAL_VALUE: <span style="color: var(--a3);">{{ $total }} DT</span></h3>
                    
                    <form action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn magenta lg">
                            <span class="inner">Execute Checkout</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection