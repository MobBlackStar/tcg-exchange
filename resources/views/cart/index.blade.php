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
                                
                                <!-- [TECH LEAD FIX]: Quantity Update Form -->
                                <td style="padding: 15px;">
                                    <form action="{{ route('cart.update') }}" method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="listing_id" value="{{ $id }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['max_stock'] ?? 1 }}" style="width: 60px; background: #000; border: 1px solid var(--a5); color: #fff; padding: 5px; text-align: center;">
                                        <button type="submit" style="color: var(--a3); background: none; border: none; cursor: pointer; font-family: 'Share Tech Mono';">[UPD]</button>
                                    </form>
                                </td>
                                
                                <td style="padding: 15px; color: var(--a3);">{{ $item['price'] * $item['quantity'] }} DT</td>
                                <td style="padding: 15px;">
                                    <form action="{{ route('cart.remove') }}" method="POST" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="listing_id" value="{{ $id }}">
                                        <button type="submit" style="color: var(--a1); background:none; border:none; cursor:pointer; font-family: 'Share Tech Mono';">[DROP]</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

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