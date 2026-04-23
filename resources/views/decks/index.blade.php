@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container-tight">
        <h2 class="h2 text-stack-sm">My <span class="accent-y">Decks.</span></h2>

        <!-- Create New Deck -->
        <form action="{{ route('deck.store') }}" method="POST" class="tcard" style="padding: 20px; margin-bottom: 40px; background: hsl(var(--card)/.9);">
            @csrf
            <input type="text" name="name" placeholder="New Deck Name..." required style="background:var(--bg); border:2px solid var(--a3); padding:10px; color:white; width: 100%; margin-bottom: 10px;">
            <button type="submit" class="btn yellow sm"><span class="inner">Create Deck</span></button>
        </form>

       <!-- List Decks -->
@foreach($decks as $d)
    <div class="tcard" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        
        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- THE PREVIEW IMAGE -->
            <div style="width: 50px; height: 70px; border: 2px solid var(--a3); overflow: hidden;">
                @if($d->previewCard)
                    <img src="{{ $d->previewCard->image_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; background: var(--ink-c);"></div>
                @endif
            </div>
            <span class="mono" style="font-size: 1.2rem;">{{ $d->name }}</span>
        </div>

        <a href="{{ route('deck.builder', $d->id) }}" class="btn cyan sm">
            <span class="inner">Edit Deck</span>
        </a>
    </div>
@endforeach
    </div>
</section>
@endsection