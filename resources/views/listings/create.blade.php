@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h1 class="h2 text-stack-sm" style="font-size: 40px;">LIST_NEW_<span style="color: var(--a3);">ARTIFACT.</span></h1>
        
        <form style="background: var(--card); border: 4px solid var(--ink-c); padding: 40px; margin-top: 40px; display: flex; flex-direction: column; gap: 20px;">
            <div>
                <label class="mono" style="color: var(--a5); display: block; margin-bottom: 10px;">&gt; SELECT CARD FROM ARCHIVES</label>
                <input type="text" placeholder="Search Database..." style="width: 100%; background: #000; border: 2px solid var(--a5); color: #fff; padding: 15px; font-family: 'Share Tech Mono';">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label class="mono" style="color: var(--a3); display: block; margin-bottom: 10px;">&gt; SET PRICE ($)</label>
                    <input type="number" style="width: 100%; background: #000; border: 2px solid var(--a3); color: #fff; padding: 15px;">
                </div>
                <div>
                    <label class="mono" style="color: var(--a4); display: block; margin-bottom: 10px;">&gt; CONDITION</label>
                    <select style="width: 100%; background: #000; border: 2px solid var(--a4); color: #fff; padding: 15px;">
                        <option>MINT</option>
                        <option>NEAR MINT</option>
                        <option>PLAYED</option>
                    </select>
                </div>
            </div>

            <button class="btn cyan lg" style="margin-top: 20px;"><span class="inner">Transmit to Market</span></button>
        </form>
    </div>
</section>
@endsection