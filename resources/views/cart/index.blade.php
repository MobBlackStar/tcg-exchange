@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-narrow">
        <span class="tag yellow"><span class="label">&gt; VOID_CART_ACTIVE</span></span>
        <h1 class="h2 text-stack-sm" style="font-size: 60px; margin: 20px 0;">YOUR <span style="color: var(--a5);">CARGO.</span></h1>

        <div style="background: rgba(26,16,46,0.8); border: 4px solid var(--ink-c); padding: 40px; box-shadow: 10px 10px 0 var(--a5);">
            <!-- THE CART TABLE -->
            <table style="width: 100%; border-collapse: collapse; font-family: 'Share Tech Mono', monospace;">
                <tr style="border-bottom: 2px solid var(--a5); color: var(--a5); text-align: left;">
                    <th style="padding: 15px;">ARTIFACT</th>
                    <th style="padding: 15px;">QTY</th>
                    <th style="padding: 15px;">PRICE</th>
                    <th style="padding: 15px;">ACTION</th>
                </tr>
                <!-- MOATAZ will loop through his $cart items here -->
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <td style="padding: 15px;">Blue-Eyes White Dragon</td>
                    <td style="padding: 15px;">01</td>
                    <td style="padding: 15px; color: var(--a3);">$250.00</td>
                    <td style="padding: 15px;"><button class="mono" style="color: var(--a1); background:none; border:none; cursor:pointer;">[DROP]</button></td>
                </tr>
            </table>

            <div style="margin-top: 40px; border-top: 4px solid var(--ink-c); padding-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                <h3 class="mono">TOTAL_VALUE: <span style="color: var(--a3);">$250.00</span></h3>
                <button class="btn magenta lg"><span class="inner">Execute Checkout</span></button>
            </div>
        </div>
    </div>
</section>
@endsection