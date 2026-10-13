@extends('layouts.app')

@section('title', 'Prestations | Jorsid Beauty by Kessy')

@section('content')
<section class="pagehero">
    <span class="eyebrow">VOTRE BEAUTÉ, NOTRE SAVOIR-FAIRE</span>
    <h1>Des coiffures qui vous <em>ressemblent.</em></h1>
    <p>Découvrez nos prestations, leurs durées et leurs tarifs indicatifs. Chaque rendez-vous est préparé avec soin.</p>
    <a class="btn" href="{{ route('booking.create') }}">Réserver une prestation</a>
</section>
<section class="section service-list">
    <div class="section-heading">
        <span class="eyebrow">LE CATALOGUE</span>
        <h2>Nos prestations</h2>
        <p class="intro">Des styles pour le quotidien, les occasions spéciales et vos envies du moment.</p>
    </div>
    @if($services->isEmpty())
        <div class="empty-state"><h3>Le catalogue se prépare</h3><p>Les prestations seront bientôt disponibles. Vous pouvez aussi nous contacter.</p><a class="btn" href="{{ route('contact') }}">Nous contacter</a></div>
    @else
        <div class="cards service-cards">
            @foreach($services as $service)
                <article class="service-card">
                    <div class="service-art">@if($service->image_path)<img src="{{ asset('storage/'.$service->image_path) }}" alt="{{ $service->name_de ?: $service->name }}" loading="lazy">@else<img src="{{ asset('images/hairstyles/'.['knotless-braids','cornrows','curly-volume','braided-updo','curly-braids','twists'][($loop->index)%6].'.jpg') }}" alt="{{ $service->name }}" loading="lazy">@endif</div>
                    <div class="service-meta"><span>{{ $service->category }}</span><span>{{ $service->duration_minutes }} min</span></div>
                    <h3 data-service-name data-name-de="{{ $service->name_de ?: $service->name }}" data-name-fr="{{ $service->name_fr ?: $service->name }}" data-name-en="{{ $service->name_en ?: $service->name }}">{{ $service->name }}</h3>
                    <p data-service-description data-description-de="{{ $service->description_de ?: $service->description }}" data-description-fr="{{ $service->description_fr ?: $service->description }}" data-description-en="{{ $service->description_en ?: $service->description }}">{{ $service->description }}</p>
                    <div class="service-bottom">
                        <strong>{{ $service->price_from ? 'À partir de ' : '' }}{{ number_format((float) $service->price, 2, ',', ' ') }} €</strong>
                        <a class="text-link" href="{{ route('booking.create', ['service' => $service->id]) }}">Choisir <span aria-hidden="true">↗</span></a>
                    </div>
                    <div class="service-modes">
                        @if($service->at_salon)<span>Au salon</span>@endif
                        @if($service->at_home)<span>À domicile</span>@endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
<section class="cta"><div><span class="eyebrow eyebrow-light">VOTRE PROCHAIN LOOK</span><h2>Prête pour votre prochain rendez-vous ?</h2></div><a class="btn light" href="{{ route('booking.create') }}">Réserver maintenant</a></section>
@push('scripts')
<script>
(() => { const apply = () => { const lang = ['de','fr','en'].includes(document.documentElement.lang) ? document.documentElement.lang : 'de'; document.querySelectorAll('[data-service-name]').forEach(el => { el.textContent = el.dataset['name' + lang.charAt(0).toUpperCase() + lang.slice(1)] || el.textContent; }); document.querySelectorAll('[data-service-description]').forEach(el => { el.textContent = el.dataset['description' + lang.charAt(0).toUpperCase() + lang.slice(1)] || el.textContent; }); }; document.addEventListener('jorsid:language-changed', apply); apply(); })();
</script>
@endpush
@endsection
