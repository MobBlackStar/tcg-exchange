<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The TCG Exchange | Neon Bauhaus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet" />

    <!-- Neon Bauhaus Core Styles -->
    <link rel="stylesheet" href="{{ asset('css/neon.css') }}">
    @stack('styles')
</head>
<body class="scanlines">
    <!-- BACKGROUND ATMOSPHERE -->
    <div class="atmosphere" aria-hidden="true">
        <div class="atmo-sun"></div>
        <div class="atmo-grid pattern-grid-floor"></div>
        <div class="atmo-dots pattern-dots"></div>
    </div>

    <div class="page">
        <header class="nav">
            <div class="nav-inner">
                <a href="/" class="logo">
                    <span class="logo-shapes"><span class="s1"></span><span class="s2"></span><span class="s3"></span></span>
                    <span class="logo-text">TCG<b>EX</b></span>
                </a>
                <nav class="nav-links">
                    <a href="{{ route('catalog') }}">&gt; Catalog</a>
                    <a href="{{ route('wishlist.index') }}">&gt; Favorites</a>
                    <a href="{{ route('cart.index') }}">&gt; My Cart</a>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}">&gt; My Binder</a>
                    @else
                        <a href="{{ route('login') }}">&gt; Login</a>
                        <a href="{{ route('register') }}">&gt; Register</a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- MAIN CONTENT INJECTION -->
        <main style="min-height: 80vh; padding-top: 100px;">
            @yield('content')
        </main>

        <footer class="foot">
            <div class="foot-bar-inner" style="text-align: center; padding: 20px; color: var(--chrome-c); opacity: 0.5; font-family: 'Share Tech Mono';">
                <span>&gt; © 2026 THE TCG EXCHANGE. ALL SIGNALS RESERVED.</span>
            </div>
        </footer>
    </div>

    <!-- UI COMPONENTS: THE DUELIST COMM DRAWER (AJAX CHAT UI) -->
    <div id="chatDrawer" style="position: fixed; right: -400px; top: 0; width: 350px; height: 100vh; background: var(--bg); border-left: 4px solid var(--a5); z-index: 1000; transition: right 0.4s cubic-bezier(0.19, 1, 0.22, 1); box-shadow: -10px 0 40px rgba(0,0,0,0.8); padding: 25px; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--a5); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 class="font-display" style="color: var(--a5); margin: 0; font-size: 1.2rem; letter-spacing: 2px;">&gt; DUELIST_COMM</h3>
            <button onclick="toggleChat()" style="color: var(--a1); font-family: 'Share Tech Mono', monospace; cursor: pointer; background: none; border: none;">[X] CLOSE</button>
        </div>

        <div id="chatMessages" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; padding: 10px;" class="pattern-dots-cyan">
            <div class="tag cyan-on-dark" style="width: fit-content;"><span class="dot"></span><span class="label">System: Encrypted channel active.</span></div>
        </div>

        <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 20px;">
            <textarea id="chatInput" placeholder="Enter message..." style="width: 100%; background: var(--ink-c); color: var(--chrome-c); border: 2px solid var(--a4); padding: 12px; font-family: 'Share Tech Mono', monospace; resize: none; margin-bottom: 10px;"></textarea>
            <button onclick="sendMessage()" class="btn magenta sm full"><span class="inner">Transmit</span></button>
        </div>
    </div>

    <!-- Floating Neon Chat Button -->
    <button onclick="toggleChat()" id="chatFab" style="position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; background: var(--a5); border: 4px solid var(--ink-c); cursor: pointer; z-index: 999; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px var(--a5);" class="anim-pulse-glow">
        <svg style="width: 30px; height: 30px; color: var(--ink-c);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>

    <!-- SYSTEM NOTIFICATION POPUP -->
    <div id="neon-notification" style="position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%); z-index: 9999; background: var(--bg); border: 3px solid var(--a5); box-shadow: 0 0 20px var(--a5), 6px 6px 0 var(--ink-c); padding: 15px 30px; transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1); pointer-events: none;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <span class="dot anim-pulse-glow" style="width: 10px; height: 10px; background: var(--a5); border-radius: 50%;"></span>
            <span class="msg" id="notification-text" style="font-family: 'Share Tech Mono', monospace; color: var(--a5); text-transform: uppercase; letter-spacing: 2px; font-weight: bold;">ARCHIVE_SUCCESS</span>
        </div>
    </div>

    <!-- MASTER JAVASCRIPT LOGIC -->
    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const AUTH_USER_ID = '{{ auth()->id() ?? 0 }}';
        let chatPartnerId = 1; // Default to Seto Kaiba for testing

        // --- 1. NOTIFICATION & WISHLIST LOGIC ---
        function showNotification(message, vibrate = false) {
            const toast = document.getElementById('neon-notification');
            const text = document.getElementById('notification-text');
            text.innerText = message;
            
            toast.classList.add('active');
            if(vibrate) toast.style.animation = "vibrate 0.2s linear infinite";
            toast.style.bottom = '40px'; 

            setTimeout(() => { 
                toast.style.bottom = '-100px'; 
                toast.style.animation = "none";
            }, 3000);
        }

        async function toggleWishlist(event, cardId) {
            event.preventDefault();
            const btn = event.currentTarget;
            const wholeCard = btn.closest('.tcard');

            try {
                const response = await fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ card_id: cardId })
                });
                const data = await response.json();

                if (data.status === 'added') {
                    btn.classList.add('active');
                    if(wholeCard) wholeCard.classList.add('wishlisted');
                    showNotification("ADD ME TO CARD", true);
                } else {
                    btn.classList.remove('active');
                    if(wholeCard) wholeCard.classList.remove('wishlisted');
                    showNotification("> SIGNAL_RELEASED", false);
                }
            } catch (e) {
                showNotification("> LOGIN_REQUIRED", true);
            }
        }

        // --- 2. CHAT UI LOGIC ---
        function toggleChat() {
            var drawer = document.getElementById('chatDrawer');
            var fab = document.getElementById('chatFab');
            if (drawer.style.right === '0px') {
                drawer.style.right = '-400px';
                fab.style.display = 'flex';
            } else {
                drawer.style.right = '0px';
                fab.style.display = 'none';
                fetchMessages();
            }
        }

        // --- 3. AJAX CHAT LOGIC ---
        async function sendMessage() {
            const input = document.getElementById('chatInput');
            const content = input.value.trim();
            if (content === "") return;

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ receiver_id: chatPartnerId, content: content })
                });
                if (response.ok) {
                    input.value = "";
                    fetchMessages();
                }
            } catch (error) { console.error('Transmission Failed:', error); }
        }

        async function fetchMessages() {
            try {
                const res = await fetch('/chat/fetch/' + chatPartnerId);
                const msgs = await res.json();
                const box = document.getElementById('chatMessages');
                box.innerHTML = '';
                msgs.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'tag';
                    const isMe = m.sender_id == AUTH_USER_ID;
                    div.style.alignSelf = isMe ? 'flex-end' : 'flex-start';
                    div.style.borderColor = isMe ? 'var(--a4)' : 'var(--a5)';
                    div.innerHTML = '<span class="label" style="color:' + (isMe ? 'var(--a4)' : 'var(--a5)') + ';">' + (isMe ? 'YOU' : 'OPPONENT') + ': ' + m.content + '</span>';
                    box.appendChild(div);
                });
                box.scrollTop = box.scrollHeight;
            } catch (e) { }
        }
        setInterval(fetchMessages, 4000);
    </script>

    @stack('scripts')
</body>
</html>