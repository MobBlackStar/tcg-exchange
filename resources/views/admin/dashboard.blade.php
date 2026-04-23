@extends('layouts.master')

@section('content')
<section class="section" style="padding-top: 50px;">
    <div class="container">
        
        <div class="heading-block" style="text-align: center; margin-bottom: 40px;">
            <span class="tag red" style="margin-bottom:24px; border-color: var(--a1);"><span class="label" style="color: var(--a1);">&gt; OVERSEER_TERMINAL</span></span>
            <h2 class="h2 text-stack-sm" style="color: var(--a1);">Admin <span class="accent-c">Control.</span></h2>
            <p class="lede" style="margin: 24px auto 0 auto;">&gt; Absolute authority over users and transactions.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 40px;">
            
            <!-- USER MANAGEMENT (La gestion des utilisateurs) -->
            <div class="tcard shadow-brick-red" style="padding: 30px; border-color: var(--a1);">
                <h3 class="font-display" style="color: var(--a1); font-size: 1.5rem; margin-bottom: 20px;">&gt; NETWORK_USERS</h3>
                
                <table style="width: 100%; border-collapse: collapse; font-family: 'Share Tech Mono';">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--a1); color: var(--a1); text-align: left;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">NAME</th>
                            <th style="padding: 10px;">EMAIL</th>
                            <th style="padding: 10px;">ROLE</th>
                            <th style="padding: 10px;">STATUS</th>
                            <th style="padding: 10px;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--chrome-c);">
                                <td style="padding: 10px;">{{ $u->id }}</td>
                                <td style="padding: 10px; color: var(--a5);">{{ $u->name }}</td>
                                <td style="padding: 10px;">{{ $u->email }}</td>
                                <td style="padding: 10px; color: {{ $u->role == 'admin' ? 'var(--a1)' : 'var(--a3)' }};">{{ strtoupper($u->role) }}</td>
                                <td style="padding: 10px;">
                                    @if($u->email_verified_at)
                                        <span style="color: var(--a5);">VERIFIED</span>
                                    @else
                                        <span style="color: var(--a4);">UNVERIFIED</span>
                                    @endif
                                </td>
                                <td style="padding: 10px; display: flex; gap: 10px;">
                                    <!-- MANUAL VERIFY BUTTON -->
                                    @if(!$u->email_verified_at)
                                        <form action="{{ route('admin.verify', $u->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn cyan sm" style="padding: 0 10px;"><span class="inner">VERIFY</span></button>
                                        </form>
                                    @endif
                                    
                                    <!-- PURGE BUTTON -->
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.delete_user', $u->id) }}" method="POST" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn outline sm" style="border-color: var(--a1); color: var(--a1); padding: 0 10px;"><span class="inner">PURGE</span></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- GLOBAL ORDER MANAGEMENT (La gestion des commandes) -->
            <div class="tcard shadow-brick-yellow" style="padding: 30px;">
                <h3 class="font-display" style="color: var(--a3); font-size: 1.5rem; margin-bottom: 20px;">&gt; GLOBAL_TRANSACTIONS</h3>
                
                <table style="width: 100%; border-collapse: collapse; font-family: 'Share Tech Mono';">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--a3); color: var(--a3); text-align: left;">
                            <th style="padding: 10px;">ORDER_UUID</th>
                            <th style="padding: 10px;">BUYER</th>
                            <th style="padding: 10px;">TOTAL</th>
                            <th style="padding: 10px;">STATUS</th>
                            <th style="padding: 10px;">DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $o)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--chrome-c);">
                                <td style="padding: 10px;">{{ explode('-', $o->uuid)[0] }}...</td>
                                <td style="padding: 10px; color: var(--a5);">{{ $o->buyer->name ?? 'Purged User' }}</td>
                                <td style="padding: 10px; color: var(--a3);">{{ $o->total_price }} DT</td>
                                <td style="padding: 10px;">{{ $o->status }}</td>
                                <td style="padding: 10px;">{{ $o->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>
@endsection