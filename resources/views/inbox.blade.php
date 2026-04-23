@extends('layouts.master')

@section('content')
<section class="section" style="min-height: 100vh; padding-top: 50px;">
    <div class="container-narrow">
        
        <!-- HEADER -->
        <div class="heading-block" style="text-align: center; margin-bottom: 40px;">
            <span class="tag cyan-on-dark" style="margin-bottom:24px"><span class="label">&gt; module.comm</span></span>
            <h2 class="h2 text-stack-sm">Duelist <br/><span class="accent-c">Inbox.</span></h2>
            <p class="lede" style="margin: 24px auto 0 auto;">&gt; Active encrypted channels.</p>
        </div>
        
        <!-- THE INBOX FEED -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @forelse($conversations as $convo)
                <div class="tcard shadow-brick-cyan" style="padding: 20px; display: flex; justify-content: space-between; align-items: center; border-left: 6px solid var(--a5);">
                    
                    <div>
                        <h3 class="mono" style="color: var(--a5); font-size: 1.5rem;">&gt; {{ strtoupper($convo['partner']->name) }}</h3>
                        <p style="color: var(--chrome-c); opacity: 0.7; font-family: 'Share Tech Mono'; margin-top: 5px;">
                            {{ \Illuminate\Support\Str::limit($convo['latest_message']->content, 60) }}
                        </p>
                        <p style="font-size: 0.8rem; color: var(--a4); margin-top: 5px;">
                            {{ $convo['latest_message']->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 15px;">
                        @if($convo['unread_count'] > 0)
                            <div class="tag magenta anim-pulse-glow">
                                <span class="label">{{ $convo['unread_count'] }} UNREAD</span>
                            </div>
                        @endif
                        
                        <!-- [TECH LEAD FIX]: Uses our global JS to instantly open the Drawer! -->
                        <button onclick="openChatWith({{ $convo['partner']->id }}, '{{ addslashes($convo['partner']->name) }}')" class="btn outline sm" style="border-color: var(--a5); color: var(--a5);">
                            <span class="inner">OPEN COMM</span>
                        </button>
                    </div>

                </div>
            @empty
                <div class="tcard" style="padding: 40px; text-align: center; border-color: var(--a4);">
                    <p class="mono" style="color: var(--a4);">&gt; YOUR INBOX IS EMPTY. NO SIGNALS DETECTED.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
@endsection