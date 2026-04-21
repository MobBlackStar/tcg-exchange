@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">List <span class="accent-m">your card.</span></h2>
        
        <form action="{{ route('inventory.store') }}" method="POST" class="tcard" style="padding: 24px; margin-top: 40px; background: hsl(var(--card)/.9);">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <label style="font-family: 'Share Tech Mono';">Select Card (From Library):</label>
                <select name="card_id" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white;">
                    @foreach(\App\Models\Card::all() as $card)
                        <option value="{{ $card->id }}">{{ $card->name }} ({{ $card->passcode }})</option>
                    @endforeach
                </select>

                <label style="font-family: 'Share Tech Mono';">Price (DT):</label>
                <input type="number" name="price" step="0.01" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white;">

                <label style="font-family: 'Share Tech Mono';">Condition:</label>
                <select name="condition" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white;">
                    <option value="Mint">Mint</option>
                    <option value="Near Mint">Near Mint</option>
                    <option value="Lightly Played">Lightly Played</option>
                    <option value="Damaged">Damaged</option>
                </select>

                <label style="font-family: 'Share Tech Mono';">Quantity:</label>
                <input type="number" name="quantity" value="1" min="1" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white;">

                <button type="submit" class="btn magenta lg full"><span class="inner">List on Market</span></button>
            </div>
        </form>
    </div>
</section>
@endsection