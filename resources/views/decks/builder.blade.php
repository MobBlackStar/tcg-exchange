@extends('layouts.master')

@section('content')
<style>
    body { overflow: hidden; }
    .omega-pane { background: rgba(10, 10, 20, 0.95); border: 2px solid var(--a5); border-radius: 5px; box-shadow: inset 0 0 20px rgba(0,0,0,0.8); }
    .omega-header { background: linear-gradient(90deg, var(--ink-c), var(--a5), var(--ink-c)); color: white; text-align: center; padding: 5px; font-family: 'Orbitron'; font-size: 14px; border-bottom: 2px solid var(--a5); }
    
    .card-slot { position: relative; cursor: pointer; transition: transform 0.1s; }
    .card-slot:hover { transform: scale(1.1); z-index: 50; box-shadow: 0 0 15px var(--a5); }
    
    .card-qty-badge { position: absolute; top: -5px; right: -5px; background: var(--a1); color: white; font-family: 'Share Tech Mono'; font-size: 10px; padding: 2px 5px; border-radius: 3px; box-shadow: 1px 1px 5px black; z-index: 10; }
    
    #hiddenFileInput { display: none; }
    
    .lib-scroll::-webkit-scrollbar { width: 8px; }
    .lib-scroll::-webkit-scrollbar-track { background: var(--ink-c); }
    .lib-scroll::-webkit-scrollbar-thumb { background: var(--a5); border-radius: 4px; }

    /* Custom Grid to match Omega perfectly */
    .battlefield-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 4px; padding: 10px; align-content: start; }
    .extra-side-grid { display: grid; grid-template-columns: repeat(15, 1fr); gap: 4px; padding: 10px; align-content: start; }
</style>

<section style="height: calc(100vh - 100px); padding: 10px; display: flex; gap: 15px; font-family: 'Share Tech Mono';">

    <!-- ============================================== -->
    <!-- LEFT PANE: THE HOLOGRAM & CONTROLS             -->
    <!-- ============================================== -->
    <div style="width: 300px; display: flex; flex-direction: column; gap: 10px;">
        
        <div class="omega-pane" style="padding: 10px; border-color: var(--a4);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="color: var(--a4); font-size: 16px; font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ strtoupper($deck->name) }}</span>
                <span class="tag magenta"><span class="label">DECK_EDIT</span></span>
            </div>

            <!--[PROJECT OMEGA]: IMPORT / EXPORT TOOLS -->
            <div style="border-top: 4px solid var(--ink-c); padding-top: 15px; margin-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                
                <a href="{{ route('deck.export', $deck->id) }}" class="btn magenta sm full" style="margin-bottom: 5px;">
                    <span class="inner">⬇ EXPORT DECK (.YDK)</span>
                </a>

                <!-- FILE IMPORT -->
                <form action="{{ route('deck.import', $deck->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 5px;">
                    @csrf
                    <input type="file" name="ydk_file" id="hiddenFileInput" accept=".ydk,.txt" style="display:none;" onchange="this.form.submit();">
                    <button type="button" onclick="document.getElementById('hiddenFileInput').click();" class="btn outline sm full" style="border-color: var(--a3); color: var(--a3);"><span class="inner">⬆ IMPORT FILE</span></button>
                </form>

                <!-- CLIPBOARD IMPORT -->
                <form action="{{ route('deck.importText', $deck->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                    @csrf
                    <textarea name="clipboard_data" placeholder="Paste Base64, YDK, or Recipe here..." required style="width: 100%; background: var(--ink-c); border: 2px solid var(--a5); color: var(--chrome-c); padding: 8px; font-family: 'Share Tech Mono'; font-size: 10px; resize: none; height: 60px;"></textarea>
                    <button type="submit" class="btn cyan sm full"><span class="inner">INJECT CLIPBOARD</span></button>
                </form>
            </div>
            
            <form action="{{ route('nexus.upload') }}" method="POST" style="margin-top: 5px;">
                @csrf
                <input type="hidden" name="ydk_deck_id" value="{{ $deck->id }}">
                <button type="submit" class="btn magenta sm full" style="padding: 0;"><span class="inner" style="font-size: 10px;">APPRAISE & BUY DECK</span></button>
            </form>
        </div>

        <!-- The Dynamic Hologram -->
        <div class="omega-pane" style="flex: 1; display: flex; flex-direction: column; align-items: center; padding: 10px; border-color: var(--a5);">
            <div class="omega-header" style="width: 100%; margin-bottom: 10px;" id="holo-title">&gt; WAITING FOR SIGNAL</div>
            
            @if($deck->previewCard)
                <img id="holo-image" src="{{ $deck->previewCard->image_url }}" style="width: 100%; max-width: 250px; border: 2px solid var(--a5); box-shadow: 0 0 15px var(--a5);">
            @else
                <div id="holo-image-container" style="width: 100%; max-width: 250px; height: 350px; border: 2px dashed var(--a5); display: flex; justify-content: center; align-items: center;">
                    <img id="holo-image" src="" style="width: 100%; display: none; border: 2px solid var(--a5); box-shadow: 0 0 15px var(--a5);">
                    <span id="holo-placeholder" style="color: var(--a5); opacity: 0.5;">NO ARTIFACT SELECTED</span>
                </div>
            @endif
            
            <div id="holo-desc" style="margin-top: 15px; font-size: 11px; color: var(--chrome-c); overflow-y: auto; flex: 1; border: 1px solid rgba(255,255,255,0.1); padding: 10px; width: 100%; background: rgba(0,0,0,0.5); text-align: justify;">
                Hover over an artifact in the Library or your Deck to view telemetry data.
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- CENTER PANE: THE BATTLEFIELD                   -->
    <!-- ============================================== -->
    <div class="omega-pane" style="flex: 1; display: grid; grid-template-rows: minmax(250px, 60%) minmax(100px, 20%) minmax(100px, 20%); padding: 10px; gap: 10px; border-color: var(--a3);">
        
        <!-- MAIN DECK -->
        <div style="display: flex; flex-direction: column; overflow: hidden;">
            @php $mainCount = $deck->cards->where('pivot.location', 'main')->sum('pivot.quantity'); @endphp
            <div class="omega-header" style="background: linear-gradient(90deg, var(--ink-c), var(--a3), var(--ink-c)); color: var(--ink-c);">MAIN DECK[ {{ $mainCount }} / 60 ]</div>
            <div class="battlefield-grid lib-scroll" style="flex: 1; overflow-y: auto; background: rgba(0,0,0,0.3);">
                @foreach($deck->cards->where('pivot.location', 'main') as $card)
                    @for($i = 0; $i < $card->pivot->quantity; $i++)
                        <!-- [TECH LEAD FIX]: Unbreakable Data Attributes -->
                        <div class="card-slot" data-name="{{ $card->name }}" data-image="{{ $card->image_url }}" data-desc="{{ $card->description }}" onmouseover="updateHoloFromElement(this)" onclick="openActionModal('{{ $card->id }}', '{{ addslashes($card->name) }}', 'main')">
                            <img src="{{ $card->image_url }}" style="width: 100%; border: 1px solid #444;">
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>

        <!-- EXTRA DECK -->
        <div style="display: flex; flex-direction: column; overflow: hidden;">
            @php $extraCount = $deck->cards->where('pivot.location', 'extra')->sum('pivot.quantity'); @endphp
            <div class="omega-header" style="background: linear-gradient(90deg, var(--ink-c), var(--a4), var(--ink-c));">EXTRA DECK[ {{ $extraCount }} / 15 ]</div>
            <div class="extra-side-grid lib-scroll" style="flex: 1; overflow-y: auto; background: rgba(0,0,0,0.3);">
                @foreach($deck->cards->where('pivot.location', 'extra') as $card)
                    @for($i = 0; $i < $card->pivot->quantity; $i++)
                        <div class="card-slot" data-name="{{ $card->name }}" data-image="{{ $card->image_url }}" data-desc="{{ $card->description }}" onmouseover="updateHoloFromElement(this)" onclick="openActionModal('{{ $card->id }}', '{{ addslashes($card->name) }}', 'extra')">
                            <img src="{{ $card->image_url }}" style="width: 100%; border: 1px solid #444;">
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>

        <!-- SIDE DECK -->
        <div style="display: flex; flex-direction: column; overflow: hidden;">
            @php $sideCount = $deck->cards->where('pivot.location', 'side')->sum('pivot.quantity'); @endphp
            <div class="omega-header" style="background: linear-gradient(90deg, var(--ink-c), var(--a2), var(--ink-c));">SIDE DECK[ {{ $sideCount }} / 15 ]</div>
            <div class="extra-side-grid lib-scroll" style="flex: 1; overflow-y: auto; background: rgba(0,0,0,0.3);">
                @foreach($deck->cards->where('pivot.location', 'side') as $card)
                    @for($i = 0; $i < $card->pivot->quantity; $i++)
                        <div class="card-slot" data-name="{{ $card->name }}" data-image="{{ $card->image_url }}" data-desc="{{ $card->description }}" onmouseover="updateHoloFromElement(this)" onclick="openActionModal('{{ $card->id }}', '{{ addslashes($card->name) }}', 'side')">
                            <img src="{{ $card->image_url }}" style="width: 100%; border: 1px solid #444;">
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- RIGHT PANE: THE GLOBAL LIBRARY                 -->
    <!-- ============================================== -->
    <div class="omega-pane" style="width: 300px; display: flex; flex-direction: column; padding: 10px; border-color: var(--a5);">
        <div class="omega-header">GLOBAL LIBRARY</div>
        
        <input type="text" id="libSearch" placeholder="> Search database..." autocomplete="off" style="width: 100%; background: #000; border: 1px solid var(--a5); color: #fff; padding: 8px; margin-top: 15px; font-family: 'Share Tech Mono'; outline: none;">
        
        <div id="libResults" class="lib-scroll" style="flex: 1; overflow-y: auto; margin-top: 15px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; align-content: start; padding-right: 5px;">
            @php $initialLibrary = \App\Models\Card::limit(50)->get(); @endphp
            @foreach($initialLibrary as $card)
                <div class="card-slot" data-name="{{ $card->name }}" data-image="{{ $card->image_url }}" data-desc="{{ $card->description }}" onmouseover="updateHoloFromElement(this)" onclick="openSummonModal('{{ $card->id }}', '{{ addslashes($card->name) }}')">
                    <img src="{{ $card->image_url }}" style="width:100%; border:1px solid #444;">
                </div>
            @endforeach
        </div>
    </div>

</section>

<!-- ============================================== -->
<!-- OMEGA MODALS                                   -->
<!-- ============================================== -->

<div id="actionModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
    <div class="tcard shadow-brick-magenta" style="padding:20px; width:300px; background:var(--bg); text-align:center;">
        <h3 id="action_card_name" style="color: var(--chrome-c); margin-bottom: 15px;"></h3>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <form id="removeForm" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn outline sm full" style="border-color: var(--a1); color: var(--a1);"><span class="inner">REMOVE 1x COPY</span></button>
            </form>
            <form action="{{ route('deck.setPreview', $deck->id) }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="card_id" id="preview_card_id">
                <button type="submit" class="btn yellow sm full"><span class="inner">SET AS BOX ART</span></button>
            </form>
            <button onclick="document.getElementById('actionModal').style.display='none'" class="btn outline sm full" style="border-color: var(--chrome-c); color: var(--chrome-c);"><span class="inner">CANCEL</span></button>
        </div>
    </div>
</div>

<div id="summonModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
    <form id="summonForm" method="POST" action="{{ route('deck.addCard', $deck->id) }}" class="tcard shadow-brick-cyan" style="padding:20px; width:300px; background:var(--bg); text-align:center;">
        @csrf
        <input type="hidden" name="card_id" id="summon_card_id">
        <h2 class="font-display" style="color: var(--a5); margin-bottom: 10px;">&gt; DEPLOY ARTIFACT</h2>
        <h3 id="summon_card_name" class="mono" style="color: var(--chrome-c); margin-bottom:25px; opacity: 0.9;"></h3>
        
        <select name="location" style="width:100%; background:var(--ink-c); padding:10px; color:white; border:1px solid var(--a5); font-family: 'Share Tech Mono'; font-size: 1.1rem; margin-bottom: 15px; outline: none;">
            <option value="main">>> MAIN DECK</option>
            <option value="extra">>> EXTRA DECK</option>
            <option value="side">>> SIDE DECK</option>
        </select>
        
        <div style="display:flex; gap:10px;">
            <button type="submit" class="btn cyan lg full"><span class="inner">ADD</span></button>
            <button type="button" onclick="document.getElementById('summonModal').style.display='none'" class="btn outline lg full"><span class="inner">ABORT</span></button>
        </div>
    </form>
</div>

<!-- ============================================== -->
<!-- OMEGA JAVASCRIPT ENGINE                        -->
<!-- ============================================== -->
<script>
    // [TECH LEAD FIX]: Unbreakable Data Attribute Extraction
    function updateHoloFromElement(el) {
        const name = el.getAttribute('data-name');
        const imgUrl = el.getAttribute('data-image');
        const desc = el.getAttribute('data-desc');
        updateHolo(name, imgUrl, desc);
    }

    function updateHolo(name, imgUrl, desc) {
        document.getElementById('holo-title').innerText = "> " + name.toUpperCase();
        
        const imgEl = document.getElementById('holo-image');
        const placeholder = document.getElementById('holo-placeholder');
        
        imgEl.src = imgUrl;
        imgEl.style.display = 'block';
        imgEl.style.opacity = '1';
        if(placeholder) placeholder.style.display = 'none';
        
        document.getElementById('holo-desc').innerHTML = desc.replace(/\n/g, "<br>");
    }

    function openActionModal(cardId, cardName, location) {
        document.getElementById('action_card_name').innerText = cardName;
        document.getElementById('preview_card_id').value = cardId;
        document.getElementById('removeForm').action = "/deck/{{ $deck->id }}/remove/" + cardId;
        document.getElementById('actionModal').style.display = 'grid';
    }

    function openSummonModal(cardId, cardName) {
        document.getElementById('summon_card_id').value = cardId;
        document.getElementById('summon_card_name').innerText = cardName;
        document.getElementById('summonModal').style.display = 'grid';
    }

    const searchInput = document.getElementById('libSearch');
    const libResults = document.getElementById('libResults');
    let debounceTimer;

    searchInput.addEventListener('keyup', function() {
        clearTimeout(debounceTimer);
        let q = this.value.trim();
        
        if(q.length < 3) {
            if(q.length === 0) window.location.reload(); 
            return; 
        }
        
        debounceTimer = setTimeout(async () => {
            try {
                let res = await fetch('/api/cards/search?q=' + encodeURIComponent(q));
                let cards = await res.json();
                
                libResults.innerHTML = '';
                cards.forEach(card => {
                    let safeName = card.name.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    // We escape the description to prevent HTML injection errors
                    let safeDesc = (card.description || "Passcode: " + card.passcode).replace(/"/g, "&quot;"); 
                    
                    let div = document.createElement('div');
                    div.className = 'card-slot';
                    div.setAttribute('data-name', safeName);
                    div.setAttribute('data-image', card.image_url);
                    div.setAttribute('data-desc', safeDesc);
                    div.onmouseover = function() { updateHoloFromElement(this); };
                    div.onclick = () => openSummonModal(card.id, safeName);
                    
                    div.innerHTML = `<img src="${card.image_url}" style="width:100%; border:1px solid #444;">`;
                    libResults.appendChild(div);
                });
            } catch (e) { console.error('Library search failed'); }
        }, 300);
    });
</script>
@endsection