<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Sorveteria • Pedidos</title><link rel="stylesheet" href="{{ asset('shop.css') }}"></head>
<body><header><a class="brand" href="/">🍦 Sorveteria<span>Um momento mais doce</span></a><nav><a href="/">Cardápio</a><a href="/carrinho">Sacola ({{ array_sum(session('cart',[])) }})</a><a href="/admin">Administração</a></nav></header>
<main>@if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@yield('content')</main><footer>Feito para adoçar seu dia. • Retirada no balcão • Pagamento no local</footer></body></html>
