@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-narrow">
        <h2 class="h2 text-stack-sm" style="text-align: center; margin-bottom: 50px;">Logistics <span class="accent-c">Terminal.</span></h2>

        <!-- ============================================== -->
        <!-- SECTION 1: INCOMING ORDERS (GESTION DES COMMANDES) -->
        <!-- ============================================== -->
        <h3 class="font-display" style="color: var(--a5); font-size: 2rem; border-bottom: 2px solid var(--a5); padding-bottom: 10px; margin-bottom: 20px;">&gt; CUSTOMER ORDERS (SALES)</h3>
        
        @forelse($incomingOrders as $order)
            @php
                $statusEn = 'PENDING';
                $statusColor = 'cyan-on-dark';
                if ($order->status == 'Validée') { $statusEn = 'VALIDATED'; $statusColor = 'yellow'; }
                if ($order->status == 'Annulée') { $statusEn = 'CANCELLED'; $statusColor = 'red'; }
            @endphp
            <div class="tcard" style="margin-bottom: 24px; padding: 24px; border-color: var(--a5);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="font-size: 1.2rem; color: var(--a5);">Order #{{ explode('-', $order->uuid)[0] }}</h3>
                        <p class="mono" style="opacity: 0.7;">Buyer: {{ $order->buyer->name }} | Date: {{ $order->created_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="tag {{ $statusColor }}"><span class="label">{{ $statusEn }}</span></div>
                </div>

                <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 15px;">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-family: 'Share Tech Mono';">
                            <span>{{ $item->quantity }}x {{ $item->listing->card->name }}</span>
                            <span>{{ $item->price_locked }} DT</span>
                        </div>
                    @endforeach
                    
                    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                        <span class="font-display" style="font-size: 1.5rem; color: var(--a5);">TOTAL: {{ $order->total_price }} DT</span>
                        
                        <!-- SELLER CONTROLS: Approve or Reject -->
                        @if($order->status == 'En attente')
                            <div style="display: flex; gap: 10px;">
                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Validée">
                                    <button type="submit" class="btn yellow sm"><span class="inner">VALIDATE (SHIP)</span></button>
                                </form>
                                <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Annulée">
                                    <button type="submit" class="btn outline sm" style="border-color: var(--a1); color: var(--a1);"><span class="inner">CANCEL</span></button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="tcard" style="padding: 40px; text-align: center;">
                <p class="mono" style="opacity: 0.7;">&gt; NO INCOMING ORDERS. YOUR MERCHANT QUEUE IS EMPTY.</p>
            </div>
        @endforelse

        <div style="height: 60px;"></div> <!-- Spacer -->

        <!-- ============================================== -->
        <!-- SECTION 2: MY PURCHASES -->
        <!-- ============================================== -->
        <h3 class="font-display" style="color: var(--a4); font-size: 2rem; border-bottom: 2px solid var(--a4); padding-bottom: 10px; margin-bottom: 20px;">&gt; MY PURCHASES</h3>
        
        @forelse($myOrders as $order)
            @php
                $statusEn = 'PENDING';
                $statusColor = 'cyan-on-dark';
                if ($order->status == 'Validée') { $statusEn = 'VALIDATED'; $statusColor = 'yellow'; }
                if ($order->status == 'Annulée') { $statusEn = 'CANCELLED'; $statusColor = 'red'; }
            @endphp
            <div class="tcard" style="margin-bottom: 24px; padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="font-size: 1.2rem;">Order #{{ explode('-', $order->uuid)[0] }}</h3>
                        <p class="mono" style="opacity: 0.7;">Date: {{ $order->created_at->format('Y-m-d') }}</p>
                    </div>
                    <div class="tag {{ $statusColor }}"><span class="label">{{ $statusEn }}</span></div>
                </div>

                <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 15px;">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-family: 'Share Tech Mono';">
                            <span>{{ $item->listing->card->name }} (x{{ $item->quantity }})</span>
                            <span>{{ $item->price_locked }} DT</span>
                        </div>
                    @endforeach
                    <div style="margin-top: 15px; font-weight: bold; font-family: 'Orbitron'; border-top: 1px solid var(--ink-c); padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span>TOTAL: {{ $order->total_price }} DT</span>
                        
                        <!-- BUYER CONTROLS: Leave a Review (Only if order is Validated!) -->
                        @if($order->status == 'Validée')
                            <button onclick="openReviewModal({{ $order->id }}, {{ $order->items->first()->listing->seller_id }})" class="btn magenta sm">
                                <span class="inner">LEAVE REVIEW</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="tcard" style="padding: 40px; text-align: center;">
                <p class="mono" style="opacity: 0.7;">&gt; NO PURCHASES FOUND. THE VAULT IS SILENT.</p>
            </div>
        @endforelse

    </div>
</section>

<!-- REVIEW MODAL POPUP -->
<div id="reviewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
    <form method="POST" action="{{ route('reviews.store') }}" class="tcard shadow-brick-magenta" style="padding:30px; width:400px; background:var(--bg);">
        @csrf
        <input type="hidden" name="order_id" id="review_order_id">
        <!-- [TECH LEAD FIX]: Ensures the seller ID is explicitly passed to the backend -->
        <input type="hidden" name="seller_id" id="review_seller_id">
        
        <h3 class="accent-m" style="margin-bottom:20px; font-family:'Orbitron';">&gt; EVALUATE_SELLER</h3>
        
        <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">RATING (1-5 STARS):</label>
        <input type="number" name="rating" min="1" max="5" value="5" required style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a4); margin-bottom:15px; font-family:'Share Tech Mono';">
        
        <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">COMMENT (Optional):</label>
        <textarea name="comment" rows="3" style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a4); margin-bottom:20px; font-family:'Share Tech Mono'; resize:none;"></textarea>
        
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn magenta sm full"><span class="inner">SUBMIT LOG</span></button>
            <button type="button" onclick="document.getElementById('reviewModal').style.display='none'" class="btn outline sm full"><span class="inner">CANCEL</span></button>
        </div>
    </form>
</div>

<script>
    // [TECH LEAD FIX]: Force the Modal to accept the IDs correctly
    function openReviewModal(orderId, sellerId) {
        document.getElementById('review_order_id').value = orderId;
        document.getElementById('review_seller_id').value = sellerId;
        document.getElementById('reviewModal').style.display = 'grid';
    }
</script>
@endsection