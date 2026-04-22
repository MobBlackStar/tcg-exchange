@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">Order <span class="accent-c">History.</span></h2>

        @if(session('success'))
            <div class="tag cyan-on-dark" style="margin-bottom:20px; width:100%; text-align:center;">{{ session('success') }}</div>
        @endif

        @forelse($orders as $order)
            <div class="tcard" style="margin-bottom: 20px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="font-size: 1.2rem;">Order #{{ $order->uuid }}</h3>
                        <p style="font-family:'Share Tech Mono'; opacity: 0.7;">Date: {{ $order->created_at->format('Y-m-d') }}</p>
                    </div>
                    <!-- The French Statuses demanded by the rubric -->
                    <div class="tag {{ $order->status == 'Validée' ? 'yellow' : 'cyan-on-dark' }}">
                        <span class="label">{{ $order->status }}</span>
                    </div>
                </div>

                <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 15px;">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span>{{ $item->listing->card->name }} (x{{ $item->quantity }})</span>
                            <span class="mono">{{ $item->price_locked }} DT</span>
                        </div>
                    @endforeach
                    <div style="margin-top: 10px; font-weight: bold; border-top: 1px solid var(--ink-c); padding-top: 10px;">
                        TOTAL: {{ $order->total_price }} DT
                    </div>
                </div>
            </div>
        @empty
            <div class="tcard" style="padding: 40px; text-align: center;">
                <p>&gt; NO ORDERS FOUND. THE VAULT IS SILENT.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection