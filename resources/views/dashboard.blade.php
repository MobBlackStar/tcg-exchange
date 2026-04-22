@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container">
        
        <!-- 1. HEADER & STATS -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 60px; flex-wrap: wrap; gap: 20px;">
            <div>
                <span class="tag cyan-on-dark"><span class="label">&gt; USER_ARCHIVE</span></span>
                <h1 class="h2 text-stack-sm" style="font-size: 60px; margin-top: 10px;">WELCOME, <span style="color: var(--a5);">{{ Auth::user()->name }}</span></h1>
            </div>
            
            <!-- MINI STATS BAR -->
            <div style="display: flex; gap: 20px;">
                <div style="border: 2px solid var(--a3); padding: 15px 25px; background: rgba(0,0,0,0.4);">
                    <p class="mono" style="color: var(--a3); font-size: 12px; margin: 0;">REPUTATION</p>
                    <p class="font-display" style="font-size: 24px; margin: 0;">5.00</p>
                </div>
                <div style="border: 2px solid var(--a4); padding: 15px 25px; background: rgba(0,0,0,0.4);">
                    <p class="mono" style="color: var(--a4); font-size: 12px; margin: 0;">DECK_VALUE</p>
                    <p class="font-display" style="font-size: 24px; margin: 0;">$0.00</p>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 40px;">
            
            <!-- 2. SIDEBAR NAVIGATION -->
            <aside>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="#" class="btn magenta full"><span class="inner">My Inventory</span></a>
                    <a href="#" class="btn outline full"><span class="inner">Order History</span></a>
                    <a href="#" class="btn outline full" style="border-color: var(--a1); color: var(--a1);"><span class="inner">Security Settings</span></a>
                    
                    <div style="margin-top: 40px; padding: 20px; border: 2px dashed var(--a5); opacity: 0.6;">
                        <p class="mono" style="font-size: 12px;">&gt; SYSTEM_STATUS: STABLE<br>&gt; ENCRYPTION: ACTIVE</p>
                    </div>
                </div>
            </aside>

            <!-- 3. CONTENT AREA: YOUR LISTINGS -->
            <div style="background: rgba(26,16,46,0.6); border: 4px solid var(--ink-c); padding: 40px; box-shadow: 10px 10px 0 var(--ink-c);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                    <h3 class="mono" style="color: var(--a5);">&gt; ACTIVE_LISTINGS_IN_MARKET</h3>
                    <a href="#" class="btn yellow sm"><span class="inner">+ List New Card</span></a>
                </div>

                <!-- TABLE OF CARDS (Sarah's Neon Style) -->
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-family: 'Share Tech Mono', monospace;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--a5); text-align: left; color: var(--a5);">
                                <th style="padding: 15px;">CARD_ID</th>
                                <th style="padding: 15px;">CONDITION</th>
                                <th style="padding: 15px;">PRICE</th>
                                <th style="padding: 15px;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- This will be a @foreach later when Moataz gives us the data -->
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <td style="padding: 15px;">#89631139 (Blue-Eyes)</td>
                                <td style="padding: 15px;"><span class="tag" style="border-color: var(--a3);"><span class="label" style="color: var(--a3);">MINT</span></span></td>
                                <td style="padding: 15px;">$250.00</td>
                                <td style="padding: 15px;">
                                    <button class="mono" style="color: var(--a5); background: none; border: none; cursor: pointer;">[EDIT]</button>
                                    <button class="mono" style="color: var(--a1); background: none; border: none; cursor: pointer; margin-left: 10px;">[DELETE]</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="text-align: center; margin-top: 40px; padding: 40px; border: 2px dashed rgba(255,255,255,0.1);">
                        <p class="lede">&gt; NO OTHER DATA DETECTED IN YOUR LOCAL BINDER.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
