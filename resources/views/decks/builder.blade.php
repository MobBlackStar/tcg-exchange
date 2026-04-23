@extends('layouts.master')

@section('content')
<section class="section" style="padding: 20px;">
    <!-- LAYOUT GRID: Preview (300px) | Main/Extra (Center) | Library (400px) -->
    <div style="display: grid; grid-template-columns: 300px 1fr 400px; gap: 20px; height: 80vh;">
        
        <!-- LEFT: PREVIEW -->
        <div class="tcard" style="padding: 20px; display: flex; flex-direction: column; background: hsl(var(--card)/.9);">
            <h3 class="accent-m" style="margin-bottom:15px;">&gt; PREVIEW</h3>
            @if($deck->previewCard)
                <img src="{{ $deck->previewCard->image_url }}" style="width: 100%; border: 2px solid var(--a3); box-shadow: 0 0 20px var(--a3);">
                <p class="mono" style="margin-top:15px; font-size: 0.9rem;">{{ $deck->previewCard->name }}</p>
            @else
                <p class="mono">&gt; NO PREVIEW</p>
            @endif
        </div>

        <!-- CENTER: MAIN & EXTRA DECK -->
        <div class="tcard" style="padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 30px;">
            
            <!-- MAIN DECK -->
            <div>
                <h3 class="accent-c" style="margin-bottom:10px;">&gt; MAIN DECK ({{ $deck->cards->where('pivot.location', 'main')->sum('pivot.quantity') }}/60)</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 5px;">
                    @foreach($deck->cards->where('pivot.location', 'main') as $card)
                        <div style="position:relative; border:1px solid #444;">
                            <img src="{{ $card->image_url }}" style="width:100%;">
                            <span class="tag yellow" style="position:absolute; bottom:0; right:0; font-size:9px; padding:1px 3px;">{{ $card->pivot->quantity }}</span>
                            <div style="display: flex; gap: 2px;">
                                <form action="{{ route('deck.removeCard', [$deck->id, $card->id]) }}" method="POST" style="flex:1;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn magenta sm" style="width:100%; font-size:8px; padding:0;">[X]</button>
                                </form>
                                <form action="{{ route('deck.setPreview', $deck->id) }}" method="POST" style="flex:1;">
                                    @csrf
                                    <input type="hidden" name="card_id" value="{{ $card->id }}">
                                    <button type="submit" class="btn yellow sm" style="width:100%; font-size:8px; padding:0;">[P]</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- EXTRA DECK -->
            <div>
                <h3 class="accent-m" style="margin-bottom:10px;">&gt; EXTRA DECK ({{ $deck->cards->where('pivot.location', 'extra')->sum('pivot.quantity') }}/15)</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 5px;">
                    @foreach($deck->cards->where('pivot.location', 'extra') as $card)
                        <div style="position:relative; border:1px solid #444;">
                            <img src="{{ $card->image_url }}" style="width:100%;">
                            <span class="tag yellow" style="position:absolute; bottom:0; right:0; font-size:9px; padding:1px 3px;">{{ $card->pivot->quantity }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- RIGHT: LIBRARY -->
        <div class="tcard" style="padding: 20px; overflow-y: auto; background: hsl(var(--card)/.9);">
            <h3 class="accent-y" style="margin-bottom:15px;">&gt; LIBRARY</h3>
            <form action="{{ route('deck.builder', $deck->id) }}" method="GET" style="margin-bottom: 15px;">
                <input type="text" name="library_search" value="{{ request('library_search') }}" placeholder="Search library..." 
                       style="width: 100%; background:var(--bg); border:2px solid var(--a5); padding:10px; color:white; font-family:'Share Tech Mono';">
            </form>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                <!-- [TECH LEAD FIX]: Limited to 50 cards to prevent memory exhaustion -->
                @foreach(\App\Models\Card::where('name', 'like', '%' . request('library_search') . '%')->limit(50)->get() as $card)
                    <button onclick="openSummonModal('{{ $card->id }}', '{{ addslashes($card->name) }}')" 
                            style="background:none; border:none; cursor:pointer; width:100%;">
                        <img src="{{ $card->image_url }}" title="{{ $card->name }}" style="width:100%; border:1px solid #444;">
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- MODAL POPUP -->
<div id="summonModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
    <form id="summonForm" method="POST" class="tcard" style="padding:30px; width:300px; background:var(--bg);">
        @csrf
        <input type="hidden" name="card_id" id="modal_card_id">
        <h3 id="modal_card_name" class="accent-c" style="margin-bottom:20px;"></h3>
        <select name="location" style="width:100%; background:var(--bg); padding:10px; color:white; border:2px solid var(--a3);">
            <option value="main">MAIN DECK</option>
            <option value="extra">EXTRA DECK</option>
        </select>
        <button type="submit" class="btn cyan sm full" style="margin-top:20px;"><span class="inner">CONFIRM</span></button>
        <button type="button" onclick="document.getElementById('summonModal').style.display='none'" class="btn magenta sm full" style="margin-top:10px;"><span class="inner">CANCEL</span></button>
    </form>
</div>

<script>
    function openSummonModal(cardId, cardName) {
        document.getElementById('modal_card_id').value = cardId;
        document.getElementById('modal_card_name').innerText = cardName;
        document.getElementById('summonForm').action = "{{ route('deck.addCard', $deck->id) }}";
        document.getElementById('summonModal').style.display = 'grid';
    }
</script>
@endsection