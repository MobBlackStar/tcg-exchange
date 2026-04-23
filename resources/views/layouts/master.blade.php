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
        <!-- THE NAVBAR -->
        <!-- THE NAVBAR -->
<header class="nav">
    <div class="nav-inner">
        <a href="/" class="logo" aria-label="NBD home">
            <span class="logo-shapes"><span class="s1"></span><span class="s2"></span><span class="s3"></span></span>
            <span class="logo-text">TCG<b>EX</b></span>
        </a>
        <nav class="nav-links">
            <a href="{{ route('catalog') }}">&gt; CATALOG</a>
            <a href="{{ route('cart.index') }}">&gt; MY CART</a>
            <a href="{{ route('inventory.index') }}">&gt; MY BINDER</a>
            <a href="{{ route('decks.index') }}">&gt; MY DECKS</a> <!-- HERE IT IS -->
            
            @auth
                <a href="{{ url('/dashboard') }}">&gt; DASHBOARD</a>
            @else
                <a href="{{ route('login') }}">&gt; LOGIN</a>
            @endauth
        </nav>
    </div>
</header>

        <!-- MAIN CONTENT INJECTION -->
        <main style="min-height: 80vh; padding-top: 100px;">
            <!-- THE DOPAMINE FEEDBACK -->
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

        <!-- THE FOOTER -->
        <footer class="foot">
            <div class="foot-bar">
                <div class="foot-bar-inner">
                    <span>&gt; © 2026 THE TCG EXCHANGE. ALL SIGNALS RESERVED.</span>
                    <span class="sig"><span class="d anim-pulse-glow"></span> system online</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- ========================================== -->
    <!-- THE DUELIST COMM DRAWER (AJAX CHAT UI)     -->
    <!-- ========================================== -->
    <div id="chatDrawer" style="position: fixed; right: -400px; top: 0; width: 350px; height: 100vh; background: var(--bg); border-left: 4px solid var(--a5); z-index: 1000; transition: right 0.4s cubic-bezier(0.19, 1, 0.22, 1); box-shadow: -10px 0 40px rgba(0,0,0,0.8); padding: 25px; display: flex; flex-direction: column;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--a5); padding-bottom: 15px; margin-bottom: 20px;">
            <h3 class="font-display" style="color: var(--a5); margin: 0; font-size: 1.2rem; letter-spacing: 2px;">&gt; DUELIST_COMM</h3>
            <button onclick="toggleChat()" style="color: var(--a1); font-family: 'Share Tech Mono', monospace; cursor: pointer; background: none; border: none;">[X] CLOSE</button>
        </div>

        <!-- Message Container -->
        <div id="chatMessages" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; padding: 10px;" class="pattern-dots-cyan">
            <div class="tag cyan-on-dark" style="width: fit-content;"><span class="dot"></span><span class="label">System: Encrypted channel active.</span></div>
        </div>

        <!-- Input Box -->
        <div style="margin-top: 20px; border-top: 2px solid var(--ink-c); padding-top: 20px;">
            <textarea id="chatInput" placeholder="Enter message..." style="width: 100%; background: var(--ink-c); color: var(--chrome-c); border: 2px solid var(--a4); padding: 12px; font-family: 'Share Tech Mono', monospace; resize: none; margin-bottom: 10px;"></textarea>
            <button onclick="sendMessage()" class="btn magenta sm full"><span class="inner">Transmit</span></button>
        </div>
    </div>

    <!-- Floating Neon Chat Button -->
    <button onclick="toggleChat()" id="chatFab" style="position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; background: var(--a5); border: 4px solid var(--ink-c); cursor: pointer; z-index: 999; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px var(--a5);" class="anim-pulse-glow">
        <svg style="width: 30px; height: 30px; color: var(--ink-c);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>

    <!-- ========================================== -->
    <!-- SYSTEM NOTIFICATION POPUP (Sarah's Polish) -->
    <!-- ========================================== -->
    <div id="neon-notification">
        <div style="display: flex; align-items: center; gap: 15px;">
            <span class="dot anim-pulse-glow" style="width: 10px; height: 10px; background: var(--a5); border-radius: 50%;"></span>
            <span class="msg" id="notification-text">ARCHIVE_SUCCESS</span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MASTER JAVASCRIPT LOGIC                    -->
    <!-- ========================================== -->
    <script>
        // --- 1. NOTIFICATION & WISHLIST LOGIC ---
        function showNotification(message) {
            const toast = document.getElementById('neon-notification');
            const text = document.getElementById('notification-text');
            text.innerText = message;
            toast.classList.add('active');
            setTimeout(() => { toast.classList.remove('active'); }, 3000);
        }

        function toggleWishlist(event, cardId) {
            event.preventDefault();
            var btn = event.currentTarget;
            btn.classList.toggle('active');
            
            if(btn.classList.contains('active')) {
                showNotification("> DATA_SAVED_TO_HEART");
                btn.querySelector('svg').style.fill = 'var(--a1)'; 
                btn.querySelector('svg').style.color = 'var(--a1)';
            } else {
                showNotification("> REMOVED_FROM_ARCHIVE");
                btn.querySelector('svg').style.fill = 'none'; 
                btn.querySelector('svg').style.color = 'var(--chrome-c)';
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
            }
        }

        // --- 3. GOD-TIER AJAX LOGIC (Connecting to Moataz) ---
        async function sendMessage() {
            const input = document.getElementById('chatInput');
            const box = document.getElementById('chatMessages');
            const content = input.value.trim();
            
            if (content === "") return;

            // Instantly show it on screen
            var msg = document.createElement('div');
            msg.className = 'tag';
            msg.style.alignSelf = 'flex-end';
            msg.style.borderColor = 'var(--a4)';
            msg.innerHTML = '<span class="label" style="color: var(--a4);">YOU: ' + content + '</span>';
            box.appendChild(msg);
            box.scrollTop = box.scrollHeight;
            input.value = "";

            // Silently POST to database
            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        receiver_id: 1, // Hardcoded to Seto Kaiba for Demo
                        content: content
                    })
                });
                const data = await response.json();
                if(data.status === 'success') console.log('Message Encrypted and Sent.');
            } catch (error) {
                console.error('Transmission Failed:', error);
                showNotification('> ERROR: TRANSMISSION FAILED');
            }
        }
    </script>

    @stack('scripts')
</body>
</html>