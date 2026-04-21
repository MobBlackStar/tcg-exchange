<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The TCG Exchange | Neon Bauhaus</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&family=Orbitron:wght@500;700;900&family=Share+Tech+Mono&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/neon.css') }}">
    
    @stack('styles')
</head>

<body class="scanlines">
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
                    <a href="/catalog">&gt; Catalog</a>
                    <a href="#">&gt; My Cart</a>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}">&gt; My Binder</a>
                    @else
                        <!-- Note: If these show red, it is just because the routes are being built by Ritej -->
                        <a href="{{ route('login') }}">&gt; Login</a>
                        <a href="{{ route('register') }}">&gt; Register</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main style="min-height: 80vh; padding-top: 100px;">
            @yield('content')
        </main>

        <footer class="foot">
            <div class="foot-bar">
                <div class="foot-bar-inner">
                    <span>&gt; © 2026 THE TCG EXCHANGE. ALL SIGNALS RESERVED.</span>
                    <span class="sig"><span class="d anim-pulse-glow"></span> system online</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- THE DUELIST COMM DRAWER -->
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

    <button onclick="toggleChat()" id="chatFab" style="position: fixed; bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; background: var(--a5); border: 4px solid var(--ink-c); cursor: pointer; z-index: 999; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 20px var(--a5);" class="anim-pulse-glow">
        <svg style="width: 30px; height: 30px; color: var(--ink-c);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>

    <script>
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

        function sendMessage() {
            var input = document.getElementById('chatInput');
            var box = document.getElementById('chatMessages');
            
            if (input.value.trim() === "") return;

            var msg = document.createElement('div');
            msg.className = 'tag';
            msg.style.alignSelf = 'flex-end';
            msg.style.borderColor = 'var(--a4)';
            // Replaced backticks with standard quotes for linter safety
            msg.innerHTML = '<span class="label" style="color: var(--a4);">YOU: ' + input.value + '</span>';
            
            box.appendChild(msg);
            input.value = "";
            box.scrollTop = box.scrollHeight;
        }

        function toggleWishlist(event, cardId) {
            event.preventDefault();
            var btn = event.currentTarget;
            btn.classList.toggle('active');
            console.log("System: Card " + cardId + " targeted for the Heart Archives.");
        }
        function showNotification(message) {
    const toast = document.getElementById('neon-notification');
    const text = document.getElementById('notification-text');
    
    text.innerText = message;
    toast.classList.add('active');
    
    // Hide it after 3 seconds
    setTimeout(() => {
        toast.classList.remove('active');
    }, 3000);
}

// Update your toggleWishlist to use the notification!
function toggleWishlist(event, cardId) {
    event.preventDefault();
    var btn = event.currentTarget;
    btn.classList.toggle('active');
    
    if(btn.classList.contains('active')) {
        showNotification("> DATA_SAVED_TO_HEART");
    } else {
        showNotification("> REMOVED_FROM_ARCHIVE");
    }
}
    </script>

    @stack('scripts')
    <!-- SYSTEM NOTIFICATION POPUP -->
<div id="neon-notification">
    <div style="display: flex; align-items: center; gap: 15px;">
        <span class="dot anim-pulse-glow" style="width: 10px; height: 10px; background: var(--a5); border-radius: 50%;"></span>
        <span class="msg" id="notification-text">ARCHIVE_SUCCESS</span>
    </div>
</div>
</body>
</html>
