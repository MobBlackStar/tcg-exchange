@extends('layouts.master')

@section('content')
<div class="container-narrow" style="min-height: 70vh; padding-top: 60px;">
    
    <div style="margin-bottom: 40px;">
        <span class="tag cyan-on-dark" style="margin-bottom: 15px;"><span class="label">&gt; SYSTEM.DUELIST_LINK</span></span>
        <h2 class="h2 text-stack-sm" style="font-size: 3rem;">DUELIST <span class="accent-c">TERMINAL</span></h2>
    </div>

    <div class="features-grid">
        <!-- 1. THE USER'S ID CARD -->
        <div class="tcard shadow-brick-cyan">
            <div class="titlebar">
                <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                <span class="ttl">&gt; user_profile.dat</span>
            </div>
            <div class="corner tri bgc-5"></div>
            <div class="body" style="padding: 30px;">
                <h3 style="color: var(--chrome-c); margin-bottom: 10px;">{{ strtoupper(Auth::user()->name) }}</h3>
                <p style="color: var(--a5); font-family: 'Share Tech Mono', monospace; font-size: 1.1rem; margin-bottom: 5px;">
                    &gt; CALLSIGN: {{ Auth::user()->name }}
                </p>
                <p style="color: hsl(var(--chrome)/.7); font-family: 'Share Tech Mono', monospace; margin-bottom: 5px;">
                    &gt; NETWORK_ID: {{ Auth::user()->email }}
                </p>
                <p style="color: hsl(var(--chrome)/.7); font-family: 'Share Tech Mono', monospace; margin-bottom: 25px;">
                    &gt; CLEARANCE: {{ strtoupper(Auth::user()->role) }}
                </p>
                
                <a href="{{ route('profile.edit') }}" class="btn cyan sm full">
                    <span class="inner">UPDATE SECURE DATA</span>
                </a>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px; display: flex; justify-content: center;">
                @csrf
                <button type="submit" class="btn sm" style="
                    background: linear-gradient(90deg, #2a0000 80%, #ff3b3b 100%);
                    color: #ff3b3b;
                    border: 2.5px solid #ff3b3b;
                    min-width: 120px;
                    font-size: 0.95rem;
                    font-family: 'Share Tech Mono', monospace;
                    font-weight: bold;
                    letter-spacing: 1px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5em;
                    position: relative;
                    box-shadow:
                        0 0 16px 4px #ff3b3b,
                        0 0 32px 8px #ff3b3b88,
                        0 4px 0 0 #000,
                        0 8px 24px 0 #ff3b3b44;
                    text-shadow: 0 0 8px #ff3b3b, 0 0 2px #fff;
                    border-radius: 8px;
                    transition: box-shadow 0.2s, background 0.2s, color 0.2s;
                ">
                    <span class="inner" style="padding: 0 2px;">LOGOUT</span>
                    <span aria-label="sad face" style="font-size:1.1em; filter: drop-shadow(0 0 6px #ff3b3b);">😢</span>
                </button>
            </form>
        </div>

        <!-- 2. QUICK NAVIGATION MODULES -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            
            <!-- Link to Moataz's Inventory -->
            <div class="tcard rot-r shadow-brick-yellow">
                <div class="titlebar">
                    <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                    <span class="ttl">&gt; active_listings.exe</span>
                </div>
                <div class="corner square bgc-3"></div>
                <div class="body" style="padding: 20px;">
                    <h3 style="font-size: 1.5rem; color: var(--a3);">MY BINDER</h3>
                    <p>Manage your cards for sale.</p>
                    <a href="{{ route('inventory.index') }}" class="btn yellow sm" style="margin-top: 15px;"><span class="inner">OPEN BINDER</span></a>
                </div>
            </div>

            <!-- Link to Moataz's Order History -->
            <div class="tcard rot-l shadow-brick-magenta">
                <div class="titlebar">
                    <span class="dots"><span class="d1"></span><span class="d2"></span><span class="d3"></span></span>
                    <span class="ttl">&gt; transaction_log.bin</span>
                </div>
                <div class="corner circle bgc-4"></div>
                <div class="body" style="padding: 20px;">
                    <h3 style="font-size: 1.5rem; color: var(--a4);">ORDER HISTORY</h3>
                    <p>Track your incoming and outgoing shipments.</p>
                    <a href="{{ route('orders.index') }}" class="btn magenta sm" style="margin-top: 15px;"><span class="inner">VIEW LOGS</span></a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection