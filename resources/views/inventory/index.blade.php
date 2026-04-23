@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">My <span class="accent-m">Binder.</span></h2>

        <!-- Add New Listing Form -->
        <form action="{{ route('inventory.store') }}" method="POST" class="tcard" style="padding: 24px; margin-top: 40px; background: hsl(var(--card)/.9);">
            @csrf
            <h3 style="margin-bottom: 20px; font-family: 'Share Tech Mono'; font-size: 1rem; color: var(--a5);">&gt; LIST NEW CARD</h3>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Search Database:</label>
                
                <!-- [TECH LEAD FIX]: LIVE AJAX DROPDOWN -->
                <div style="position: relative;">
                    <input type="text" id="liveSearchInput" placeholder="Type a card name (e.g. Kashtira)..." autocomplete="off" style="width: 100%; background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                    <input type="hidden" name="card_id" id="hiddenCardId" required>
                    
                    <!-- Dropdown Results Box -->
                    <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--ink-c); border: 2px solid var(--a5); z-index: 100; max-height: 250px; overflow-y: auto;">
                        <!-- JS will inject options here -->
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                    <input type="number" name="price" placeholder="Price (DT)" step="0.01" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                    <select name="condition" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                        <option value="Mint">Mint</option>
                        <option value="Near Mint">Near Mint</option>
                        <option value="Lightly Played">Lightly Played</option>
                        <option value="Damaged">Damaged</option>
                    </select>
                    <input type="number" name="quantity" value="1" min="1" placeholder="Quantity" required style="background: var(--bg); border: 2px solid var(--ink-c); padding: 10px; color: white; font-family: 'Share Tech Mono';">
                </div>
                <button type="submit" class="btn magenta lg full"><span class="inner">Add to Market</span></button>
            </div>
        </form>

        <!-- Current Listings & Live Search -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 40px 0 20px 0;">
            <h3 style="font-family: 'Share Tech Mono'; font-size: 1.2rem; color: var(--a3);">&gt; ACTIVE LISTINGS</h3>
            <input type="text" id="binderSearch" onkeyup="filterBinder()" placeholder="> Search Binder..." style="background: #000; border: 2px solid var(--a5); color: #fff; padding: 8px 15px; font-family: 'Share Tech Mono'; width: 250px;">
        </div>

        <div id="binderList">
            @forelse($myListings as $listing)
                <div class="tcard binder-row" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div style="display: flex; flex-direction: column;">
                        <span class="mono card-name" style="color: var(--chrome-c); font-size: 1.1rem;">{{ $listing->card->name }}</span>
                        <span class="mono" style="color: var(--chrome-c); font-size: 0.8rem; opacity: 0.7;">QTY: {{ $listing->quantity }} | COND: {{ $listing->condition }}</span>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="mono" style="color: var(--a5); font-size: 1.2rem;">{{ $listing->price }} DT</span>
                        <form action="{{ route('inventory.destroy', $listing->id) }}" method="POST" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: var(--a1); font-family: 'Share Tech Mono'; cursor: pointer; background: none; border: none;">[REMOVE]</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="tcard" style="padding: 20px; text-align: center;">
                    <p class="mono" style="color: var(--chrome-c); opacity: 0.7;">&gt; Your binder is empty. Add a card to the market.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- THE LIVE AJAX SCRIPT -->
<script>
    function filterBinder() {
        let term = document.getElementById('binderSearch').value.toLowerCase();
        let rows = document.querySelectorAll('.binder-row');
        rows.forEach(row => {
            let name = row.querySelector('.card-name').innerText.toLowerCase();
            row.style.display = name.includes(term) ? 'flex' : 'none';
        });
    }

    // THE LIVE DATABASE SEARCHER
    const searchInput = document.getElementById('liveSearchInput');
    const dropdown = document.getElementById('searchResults');
    const hiddenId = document.getElementById('hiddenCardId');

    searchInput.addEventListener('keyup', async function() {
        let q = this.value.trim();
        if(q.length < 3) { dropdown.style.display = 'none'; return; }
        
        let res = await fetch('/api/cards/search?q=' + encodeURIComponent(q));
        let cards = await res.json();
        
        dropdown.innerHTML = '';
        if(cards.length > 0) {
            cards.forEach(card => {
                let div = document.createElement('div');
                div.style.padding = '12px';
                div.style.cursor = 'pointer';
                div.style.borderBottom = '1px solid var(--a5)';
                div.style.color = 'var(--a4)';
                div.style.fontFamily = "'Share Tech Mono'";
                div.innerHTML = `> ${card.name} (${card.passcode})`;
                
                div.onclick = function() {
                    hiddenId.value = card.id;
                    searchInput.value = card.name;
                    dropdown.style.display = 'none';
                };
                dropdown.appendChild(div);
            });
            dropdown.style.display = 'block';
        }
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', function(e) {
        if(!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
@endsection