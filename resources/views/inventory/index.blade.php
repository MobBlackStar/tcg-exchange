@extends('layouts.master')

@section('content')
<section class="section" style="padding-top: 40px;">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">My <span class="accent-m">Binder.</span></h2>

        <!-- ADD NEW LISTING FORM -->
        <form action="{{ route('inventory.store') }}" method="POST" class="tcard" style="padding: 24px; margin-top: 40px; background: hsl(var(--card)/.9);">
            @csrf
            <h3 style="margin-bottom: 20px; font-family: 'Share Tech Mono'; font-size: 1rem; color: var(--a5);">&gt; LIST NEW CARD</h3>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <label style="font-family: 'Share Tech Mono'; color: var(--chrome-c);">Search Database:</label>
                
                <!-- LIVE AJAX DROPDOWN (Now with images!) -->
                <div style="position: relative;">
                    <input type="text" id="liveSearchInput" placeholder="Type a card name (e.g. Kashtira)..." autocomplete="off" style="width: 100%; background: var(--bg); border: 2px solid var(--ink-c); padding: 12px; color: white; font-family: 'Share Tech Mono'; font-size: 1.1rem;">
                    <input type="hidden" name="card_id" id="hiddenCardId" required>
                    
                    <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: var(--ink-c); border: 2px solid var(--a5); z-index: 100; max-height: 350px; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.8);">
                        <!-- JS will inject image options here -->
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

        <!-- CURRENT LISTINGS & SEARCH -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 40px 0 20px 0;">
            <h3 style="font-family: 'Share Tech Mono'; font-size: 1.2rem; color: var(--a3);">&gt; ACTIVE LISTINGS</h3>
            <input type="text" id="binderSearch" onkeyup="filterBinder()" placeholder="> Search Binder..." style="background: #000; border: 2px solid var(--a5); color: #fff; padding: 8px 15px; font-family: 'Share Tech Mono'; width: 250px;">
        </div>

        <div id="binderList">
            @forelse($myListings as $listing)
                <div class="tcard binder-row" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-left: 4px solid var(--a5);">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="{{ $listing->card->image_url }}" style="width: 40px; height: 60px; object-fit: cover; border: 1px solid var(--a5);">
                        <div style="display: flex; flex-direction: column;">
                            <span class="mono card-name" style="color: var(--chrome-c); font-size: 1.1rem;">{{ $listing->card->name }}</span>
                            <span class="mono" style="color: var(--chrome-c); font-size: 0.8rem; opacity: 0.7;">QTY: {{ $listing->quantity }} | COND: {{ $listing->condition }}</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="font-display" style="color: var(--a5); font-size: 1.2rem;">{{ $listing->price }} DT</span>
                        
                        <button type="button" onclick="openEditModal({{ $listing->id }}, {{ $listing->price }}, {{ $listing->quantity }}, '{{ $listing->condition }}')" style="color: var(--a3); font-family: 'Share Tech Mono'; cursor: pointer; background: none; border: none; font-weight:bold;">[EDIT]</button>
                        
                        <form action="{{ route('inventory.destroy', $listing->id) }}" method="POST" style="margin: 0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="color: var(--a1); font-family: 'Share Tech Mono'; cursor: pointer; background: none; border: none; font-weight:bold;">[REMOVE]</button>
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

<!-- [TECH LEAD FIX]: RESTORED THE EDIT MODAL -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
    <form id="editForm" method="POST" class="tcard shadow-brick-yellow" style="padding:30px; width:400px; background:var(--bg);">
        @csrf @method('PATCH')
        <h3 class="accent-y" style="margin-bottom:20px; font-family:'Orbitron';">&gt; MODIFY_ARTIFACT</h3>
        
        <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">PRICE (DT):</label>
        <input type="number" step="0.01" name="price" id="edit_price" required style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a3); margin-bottom:15px; font-family:'Share Tech Mono';">
        
        <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">QUANTITY:</label>
        <input type="number" name="quantity" id="edit_qty" required style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a3); margin-bottom:15px; font-family:'Share Tech Mono';">
        
        <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">CONDITION:</label>
        <select name="condition" id="edit_cond" required style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a3); margin-bottom:25px; font-family:'Share Tech Mono';">
            <option value="Mint">Mint</option>
            <option value="Near Mint">Near Mint</option>
            <option value="Lightly Played">Lightly Played</option>
            <option value="Damaged">Damaged</option>
        </select>
        
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn yellow sm full"><span class="inner">UPDATE</span></button>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn outline sm full"><span class="inner">CANCEL</span></button>
        </div>
    </form>
</div>

<!-- JAVASCRIPT FOR AJAX SEARCH AND MODALS -->
<script>
    // Live Search for existing listings
    function filterBinder() {
        let term = document.getElementById('binderSearch').value.toLowerCase();
        let rows = document.querySelectorAll('.binder-row');
        rows.forEach(row => {
            let name = row.querySelector('.card-name').innerText.toLowerCase();
            row.style.display = name.includes(term) ? 'flex' : 'none';
        });
    }

    // Open Edit Modal
    function openEditModal(id, price, qty, cond) {
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_qty').value = qty;
        document.getElementById('edit_cond').value = cond;
        document.getElementById('editForm').action = "/inventory/" + id;
        document.getElementById('editModal').style.display = 'grid';
    }

    // [TECH LEAD FIX]: LIVE DATABASE SEARCHER WITH IMAGES
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
                div.style.padding = '10px';
                div.style.cursor = 'pointer';
                div.style.borderBottom = '1px solid rgba(255,255,255,0.1)';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.style.gap = '15px';
                
                // Add Hover Effect via JS
                div.onmouseover = function() { this.style.backgroundColor = 'var(--a4)'; };
                div.onmouseout = function() { this.style.backgroundColor = 'transparent'; };

                // Inject the Image AND the Text!
                div.innerHTML = `
                    <img src="${card.image_url}" style="width: 35px; height: 50px; border: 1px solid var(--a5);">
                    <div style="display:flex; flex-direction:column;">
                        <span style="color: #fff; font-family: 'Share Tech Mono'; font-size: 1rem;">${card.name}</span>
                        <span style="color: var(--a5); font-family: 'Share Tech Mono'; font-size: 0.8rem;">[${card.passcode}]</span>
                    </div>
                `;
                
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