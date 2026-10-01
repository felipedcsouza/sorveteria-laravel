@extends('shop.layout')
@section('content')
<section class="hero"><div><span class="eyebrow">SEU PRÓXIMO MOMENTO FAVORITO</span><h1>Escolha seu sabor.<br>Deixe o resto com a gente.</h1><p>Monte sua sacola e envie seu pedido para a sorveteria.</p><a class="button" href="/carrinho">Ver minha sacola →</a></div><div class="hero-icon" aria-hidden="true">🍨</div></section>
<h2>Nosso cardápio</h2>
@forelse($products->groupBy('category') as $category=>$group)<h3>{{ $category }}</h3><div class="grid">
@foreach($group as $p)<article class="card">@if($p->image)<img class="product-image" src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}">@else<div class="placeholder" aria-hidden="true">🍦</div>@endif<div class="card-body"><span class="tag">{{ $p->category }}</span><h3>{{ $p->name }}</h3><p>{{ $p->description }}</p><div class="row"><strong>R$ {{ number_format($p->price_cents/100,2,',','.') }}</strong><form method="post" action="/carrinho/{{ $p->id }}">@csrf<button>Adicionar +</button></form></div></div></article>@endforeach</div>
@empty<div class="card card-body">O cardápio está sendo preparado. Volte em breve!</div>@endforelse
@endsection
