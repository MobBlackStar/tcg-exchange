@extends('layouts.master')

@section('content')
<section class="section">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <span class="tag magenta"><span class="label">&gt; USER_HEART_ARCHIVE</span></span>
            <h1 class="h2 text-stack-sm" style="font-size: 80px; margin: 20px 0;">FAVORITE<br><span style="color: var(--a5);">ARTIFACTS.</span></h1>
        </div>

        <div class="features-grid">
            @forelse($favorites as $fav)
                <!-- We reuse your beautiful tcard design here -->
                <div class="tcard shadow-brick-magenta wishlisted">
                    <div class="titlebar">
                        <span class="mono" style="color:var(--a4)">LOCKED_SIGNAL</span>
                    </div>
                    <div class="body">
                        <img src="{{ $fav->card->image_url }}" style="width: 180px; margin-bottom: 20px;">
                        <h3>{{ $fav->card->name }}</h3>
                        <a href="{{ route('card.show', $fav->card->id) }}" class="btn outline sm full"><span class="inner">View Data</span></a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 100px; border: 2px dashed var(--a4);">
                    <p class="lede" style="color: var(--a4);">&gt; NO SIGNALS DETECTED IN THE HEART VAULT.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection