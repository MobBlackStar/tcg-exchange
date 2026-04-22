@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container">
        <h2 class="h2 text-stack-sm">Deck: <span class="accent-c">{{ $deck->name }}</span></h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 50px;">
            
            <!-- COLLECTION (The Vault) -->
            <div class="tcard" style="padding: 20px;">
                <h3 class="accent-m">&gt; VAULT (OWNED)</h3>
                @foreach($ownedCounts as $name => $count)
                    <div style="display:flex; justify-content:space-between; margin:10px 0; font-family:'Share Tech Mono';">
                        <span>{{ $name }}</span>
                        <span class="tag cyan-on-dark">{{ $count }}</span>
                    </div>
                @endforeach
            </div>

            <!-- DECK EDITOR -->
            <div class="tcard" style="padding: 20px;">
                <h3 class="accent-y">&gt; CURRENT DECK</h3>
                @foreach($deckCounts as $name => $count)
                    <div style="display:flex; justify-content:space-between; margin:10px 0; font-family:'Share Tech Mono';">
                        <span>{{ $name }}</span>
                        <span class="tag yellow">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection