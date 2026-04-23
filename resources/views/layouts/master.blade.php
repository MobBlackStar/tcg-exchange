<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>The TCG Exchange | Neon Bauhaus</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet" />

    <!-- Neon Bauhaus Core Styles -->
    <link rel="stylesheet" href="{{ asset('css/neon.css') }}">
    
    <style>
        #neon-notification {
            position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%); z-index: 9999;
            background: var(--bg); border: 3px solid var(--a5); box-shadow: 0 0 20px var(--a5), 6px 6px 0 var(--ink-c);
            padding: 15px 30px; transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1); pointer-events: none;
        }
        #neon-notification.active { bottom: 40px; }
        
        @keyframes vibrate {
            0%, 100% { transform: translateX(-50%) translate(0); }
            20% { transform: translateX(-50%) translate(-3px, 3px); }
            40% { transform: translateX(-50%) translate(-3px, -3px); }
            60% { transform: translateX(-50%) translate(3px, 3px); }
            80% { transform: translateX(-50%) translate(3px, -3px); }
        }
        .vibrate-alert { animation: vibrate 0.2s linear infinite !important; border-color: var(--a1) !important; box-shadow: 0 0 30px var(--a1) !important; }
    </style>
    @stack('styles')
</head>

<body class="scanlines">
    <div class="atmosphere" aria-hidden="true">
        <div class="atmo-sun"></div>
        <div class="atmo-grid pattern-grid-floor"></div>
        <div class="atmo-dots pattern-dots"></div>
    </div>

    <div class="page">
        <!-- NAVBAR -->
        <header class="nav">
            <div class="nav-inner">
                <a href="/" class="logo">
                    <span class="logo-shapes"><span class="s1"></span><span class="s2"></span><span class="s3"></span></span>
                    <span class="logo-text">TCG<b>EX</b></span>
                </a>
                <nav class="nav-links">
                    <a href="{{ route('catalog') }}">&gt; CATALOG</a>
                    
                    @auth
                        <a href="{{ route('chat.inbox') }}">&gt; INBOX</a>
                        <a href="{{ route('wishlist.index') }}">&gt; FAVORITES</a>
                        <a href="{{ route('cart.index') }}">&gt; MY CART</a>
                        <a href="{{ route('inventory.index') }}">&gt; MY BINDER</a>
                        <a href="{{ route('decks.index') }}">&gt; MY DECKS</a>
                        <a href="{{ url('/dashboard') }}">&gt; DASHBOARD</a>
                    @else
                        <a href="{{ route('cart.index') }}">&gt; MY CART</a>
                        <a href="{{ route('login') }}">&gt; LOGIN</a>
                        <a href="{{ route('register') }}">&gt; REGISTER</a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- MAIN CONTENT INJECTION -->
        <main style="min-height: 80vh; padding-top: 100px;">
            <div class="container" style="margin-top: 20px;">
                @if(session('success'))
                    <div class="tag yellow" style="width: 100%; justify-content: center; margin-bottom: 20px;">
                        <span class="label">&gt; {{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="tag cyan-on-dark" style="width: 100%; justify-content: center; margin-bottom: 20px; border-color: var(--a1);">
                        <span class="label" style="color: var(--a1);">&gt; {{ session('error') }}</span>
                    </div>
                @endif
            </div>
            
            @yield('content')
        </main>

        <footer class="foot">
            <div class="foot-bar">
                <div class="foot-bar-inner" style="text-align: center; width: 100%; justify-content: center; color: var(--chrome-c); opacity: 0.5; font-family: 'Share Tech Mono';">
                    <span>&gt; © 2026 THE TCG EXCHANGE. ALL SIGNALS RESERVED.</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- UI COMPONENTS: THE DUELIST COMM DRAWER (AJAX CHAT UI) -->
    <div id="chatDrawer" style="position: fixed; right: -450px; top: 0; width: 450px; height: 100vh; background: var(--bg); border-left: 4px solid var(--a5); z-index: 1000; transition: right 0.4s cubic-bezier(0.19, 1, 0.22, 1); box-shadow: -10px 0 40px rgba(0,0,0,0.8); padding: 25px; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--a5); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 class="font-display" style="color: var(--a5); margin: 0; font-size: 1.2rem; letter-spacing: 2px;">&gt; DUELIST_COMM</h3>
            <button onclick="toggleChat()" style="color: var(--a1); font-family: 'Share Tech Mono', monospace; cursor: pointer; background: none; border: none;">[X] CLOSE</button>
        </div>
        <div id="chatMessages" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; padding: 10px;" class="pattern-dots-cyan">
            <div class="tag cyan-on-dark" style="width: fit-content;"><span class="dot"></span><span class="label">System: Encrypted channel active.</span></div>
        </div>
        
        <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 20px;">
            <textarea id="chatInput" placeholder="Enter message..." style="width: 100%; background: var(--ink-c); color: var(--chrome-c); border: 2px solid var(--a4); padding: 12px; font-family: 'Share Tech Mono', monospace; resize: none; margin-bottom: 10px;"></textarea>
            <div style="display: flex; gap: 10px;">
                <button onclick="initiateTrade()" class="btn yellow sm" style="flex: 1;"><span class="inner">⇄ OFFER</span></button>
                <button onclick="sendMessage()" class="btn magenta sm" style="flex: 2;"><span class="inner">Transmit</span></button>
            </div>
        </div>
    </div>

    <!-- CUSTOM NEON TRADE MODAL -->
    <div id="tradeModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2000; place-items:center;">
        <div class="tcard shadow-brick-yellow" style="padding:30px; width:400px; background:var(--bg);">
            <h3 class="accent-y" style="margin-bottom:20px; font-family:'Orbitron';">&gt; INITIATE_TRADE</h3>
            
            <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">DT OFFER:</label>
            <input type="number" id="tradeDt" placeholder="0" style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a3); margin-bottom:15px; font-family:'Share Tech Mono';">
            
            <label class="mono" style="color:var(--chrome-c); display:block; margin-bottom:5px;">ARTIFACT TO OFFER (Optional):</label>
            <input type="text" id="tradeCard" placeholder="Card Name..." style="width:100%; background:var(--ink-c); padding:10px; color:white; border:2px solid var(--a3); margin-bottom:20px; font-family:'Share Tech Mono';">
            
            <div style="display:flex; gap:10px;">
                <button onclick="submitTrade()" class="btn yellow sm full"><span class="inner">SEND OFFER</span></button>
                <button onclick="document.getElementById('tradeModal').style.display='none'" class="btn outline sm full"><span class="inner">CANCEL</span></button>
            </div>
        </div>
    </div>

    <!-- Floating Neon Chat Button -->
    <button onclick="toggleChat()" id="chatFab" style="position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; background: var(--a5); border: 4px solid var(--ink-c); cursor: pointer; z-index: 999; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px var(--a5);" class="anim-pulse-glow">
        <svg style="width: 30px; height: 30px; color: var(--ink-c);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>

    <!-- SYSTEM NOTIFICATION POPUP -->
    <div id="neon-notification">
        <div style="display: flex; align-items: center; gap: 15px;">
            <span class="dot anim-pulse-glow" style="width: 10px; height: 10px; background: var(--a5); border-radius: 50%;"></span>
            <span class="msg" id="notification-text" style="font-family: 'Share Tech Mono', monospace; color: var(--a5); text-transform: uppercase; letter-spacing: 2px; font-weight: bold;">ARCHIVE_SUCCESS</span>
        </div>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
        const AUTH_USER_ID = '{{ auth()->id() ?? 0 }}';
        let chatPartnerId = 1; 

        // --- 1. NOTIFICATIONS & WISHLIST ---
        function showNotification(message, vibrate = false, priority = false) {
            const toast = document.getElementById('neon-notification');
            const text = document.getElementById('notification-text');
            text.innerText = message;
            
            if (priority) {
                text.style.color = "var(--a1)";
                toast.style.borderColor = "var(--a1)";
                toast.style.boxShadow = "0 0 25px var(--a1), 6px 6px 0 var(--ink-c)";
            } else {
                text.style.color = "var(--a4)";
                toast.style.borderColor = "var(--a4)";
                toast.style.boxShadow = "0 0 20px var(--a4), 6px 6px 0 var(--ink-c)";
            }
            
            if(vibrate) toast.classList.add('vibrate-alert');
            else toast.classList.remove('vibrate-alert');

            toast.classList.add('active');
            setTimeout(() => { toast.classList.remove('active'); }, 3000);
        }

        async function toggleWishlist(event, cardId) {
            event.preventDefault();
            const btn = event.currentTarget;
            const wholeCard = btn.closest('.tcard');

            try {
                const response = await fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ card_id: cardId })
                });
                
                if (!response.ok) throw new Error('Unauthorized');
                const data = await response.json();

                if (data.status === 'added') {
                    btn.classList.add('active');
                    if(wholeCard) wholeCard.classList.add('wishlisted');
                    showNotification("> DATA_SAVED_TO_HEART", true, true); 
                } else {
                    btn.classList.remove('active');
                    if(wholeCard) wholeCard.classList.remove('wishlisted');
                    showNotification("> REMOVED_FROM_ARCHIVE", false, false);
                }
            } catch (e) {
                showNotification("> ERROR: LOGIN REQUIRED", true, true);
            }
        }

        // --- 2. CHAT UI LOGIC ---
        function toggleChat() {
            var drawer = document.getElementById('chatDrawer');
            var fab = document.getElementById('chatFab');
            if (drawer.style.right === '0px') {
                drawer.style.right = '-450px';
                fab.style.display = 'flex';
            } else {
                drawer.style.right = '0px';
                fab.style.display = 'none';
                fetchMessages();
            }
        }

        function openChatWith(sellerId, sellerName) {
            chatPartnerId = sellerId; 
            document.querySelector('#chatDrawer h3').innerText = '> COMM: ' + sellerName.toUpperCase();
            document.getElementById('chatMessages').innerHTML = '<div class="tag cyan-on-dark" style="width: fit-content;"><span class="dot"></span><span class="label">System: Encrypted channel active with ' + sellerName + '.</span></div>';
            document.getElementById('chatDrawer').style.right = '0px';
            const fab = document.getElementById('chatFab');
            if(fab) fab.style.display = 'none';
            fetchMessages(); 
        }

        // --- 3. THE TRADE ILLUSION ---
        function openTradeModal() { document.getElementById('tradeModal').style.display = 'grid'; }

        function submitTrade() {
            let dt = document.getElementById('tradeDt').value || 0;
            let card = document.getElementById('tradeCard').value || 'None';
            let tradeString = `[TRADE_OFFER] | DT: ${dt} | Card: ${card}`;
            
            document.getElementById('tradeModal').style.display = 'none';
            document.getElementById('tradeDt').value = '';
            document.getElementById('tradeCard').value = '';
            
            sendMessage(tradeString);
        }

        function acceptTrade() {
            sendMessage("[SYSTEM ALERT]: Trade Accepted! I am modifying my listing price now. Please proceed with the checkout when ready.");
        }

        function formatMessage(content, isMe) {
            if (content.startsWith('[TRADE_OFFER]')) {
                let parts = content.split('|');
                let dtOffer = parts[1] ? parts[1].replace('DT:', '').trim() : '0';
                let cardOffer = parts[2] ? parts[2].replace('Card:', '').trim() : 'None';
                
                let acceptBtn = '';
                if (!isMe) {
                    acceptBtn = `<button onclick="acceptTrade()" class="btn cyan sm" style="margin-top: 15px; width: 100%;"><span class="inner">ACCEPT TRADE</span></button>`;
                }

                return `
                    <div style="border: 2px dashed ${isMe ? 'var(--a4)' : 'var(--a5)'}; padding: 15px; background: rgba(0,0,0,0.5); text-align: center; margin-top: 5px; width: 100%;">
                        <span style="color: var(--a3); font-family: 'Orbitron'; font-size: 1.1rem;">⇄ TRADE PROPOSAL</span><br>
                        <span style="color: var(--chrome-c); font-size: 0.9rem;">OFFERS: <b style="color: var(--a5);">${dtOffer} DT</b></span><br>
                        <span style="color: var(--chrome-c); font-size: 0.9rem;">+ CARD: <b style="color: var(--a4);">${cardOffer}</b></span>
                        ${acceptBtn}
                    </div>
                `;
            }
            return `<span class="label" style="color: ${isMe ? 'var(--a4)' : 'var(--a5)'};">${isMe ? 'YOU' : 'OPPONENT'}: ${content}</span>`;
        }

        // --- 4. GOD-TIER AJAX LOGIC (GHOSTS BANISHED) ---
        async function sendMessage(overrideContent = null) {
            var input = document.getElementById('chatInput');
            var content = overrideContent !== null ? overrideContent : input.value.trim();

            if (content === "") return;
            if (overrideContent === null) input.value = ""; 

            try {
                const response = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ receiver_id: chatPartnerId, content: content })
                });
                
                const data = await response.json();
                if(data.status === 'success') fetchMessages(); 
            } catch (error) {
                showNotification('> ERROR: TRANSMISSION FAILED', true, true);
            }
        }

        async function fetchMessages() {
            try {
                const response = await fetch('/chat/fetch/' + chatPartnerId);
                if (!response.ok) return;

                const data = await response.json();
                const box = document.getElementById('chatMessages');

                data.messages.forEach(msg => {
                    const existingMessage = document.getElementById('msg-' + msg.id);
                    if (!existingMessage) {
                        const msgDiv = document.createElement('div');
                        msgDiv.id = 'msg-' + msg.id; 
                        msgDiv.className = 'tag';
                        
                        const isMe = msg.sender_id == AUTH_USER_ID;
                        msgDiv.style.alignSelf = isMe ? 'flex-end' : 'flex-start';
                        msgDiv.style.borderColor = isMe ? 'var(--a4)' : 'var(--a5)';
                        
                        msgDiv.innerHTML = formatMessage(msg.content, isMe);
                        
                        box.appendChild(msgDiv);
                        box.scrollTop = box.scrollHeight;
                    }
                });
            } catch (error) {}
        }

        // --- 5. THE SMART POLLER (Exponential Backoff) ---
        @auth
        let pollInterval = 3000; // Start at 3 seconds
        let idleTime = 0;
        let poller;

        // Reset idle time when user moves mouse or types
        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;

        function resetTimer() {
            idleTime = 0;
            if (pollInterval !== 3000) {
                pollInterval = 3000; // Wake up immediately!
                clearInterval(poller);
                startPoller();
            }
        }

        async function performPoll() {
            idleTime += pollInterval;
            
            // If AFK for more than 30 seconds, slow the polling down to save server CPU!
            if (idleTime > 30000 && pollInterval === 3000) {
                pollInterval = 15000; // Slow down to 15 seconds
                clearInterval(poller);
                startPoller();
            }

            var drawer = document.getElementById('chatDrawer');
            var fab = document.getElementById('chatFab');
            
            if (drawer && drawer.style.right === '0px') {
                fetchMessages();
            } else {
                try {
                    const res = await fetch('/chat/unread');
                    const alertData = await res.json();
                    
                    if (alertData.unread > 0) {
                        if (fab && !fab.classList.contains('vibrate-alert')) {
                            fab.style.background = 'var(--a1)';
                            fab.style.borderColor = '#fff';
                            fab.style.boxShadow = '0 0 30px var(--a1), 0 0 10px #fff';
                            fab.classList.add('vibrate-alert');
                            showNotification("> INCOMING COMM: " + alertData.unread + " NEW ALERT(S)", true, true);
                        }
                    } else {
                        if (fab) {
                            fab.style.background = 'var(--a5)';
                            fab.style.borderColor = 'var(--ink-c)';
                            fab.style.boxShadow = '0 0 20px var(--a5)';
                            fab.classList.remove('vibrate-alert');
                        }
                    }
                } catch (e) {}
            }
        }

        function startPoller() {
            poller = setInterval(performPoll, pollInterval);
        }

        // Start the engine
        startPoller();
        @endauth
    </script>
    @stack('scripts')
</body>
</html>