@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">Duelist <span class="accent-m">Profile.</span></h2>

        <div class="tcard" style="padding: 40px; margin-top: 40px; background: hsl(var(--card)/.9); text-align: center;">
            <p style="font-family: 'Share Tech Mono'; color: var(--a5); font-size: 1.2rem;">
                &gt; WELCOME TO THE VOID, {{ strtoupper(auth()->user()->name) }}
            </p>
            <p style="color: var(--chrome-c); opacity: 0.8; margin-top: 10px;">
                Role: {{ strtoupper(auth()->user()->role) }} | Reputation: {{ auth()->user()->reputation_score }} / 5.00
            </p>

            <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 20px; align-items: center;">
                <a href="{{ route('inventory.index') }}" class="btn magenta lg"><span class="inner">Manage Binder</span></a>
                <a href="{{ route('decks.index') }}" class="btn yellow md"><span class="inner">Deck Builder</span></a>
                <a href="{{ route('orders.index') }}" class="btn cyan md"><span class="inner">Order History</span></a>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 40px;">
                @csrf
                <button type="submit" style="color: var(--a1); font-family: 'Share Tech Mono'; cursor: pointer; text-decoration: underline;">[ LOGOUT ]</button>
            </form>
        </div>
    </div>
</section>
@endsection