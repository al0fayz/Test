@foreach (['danger', 'warning', 'success', 'info'] as $key)
@if(Session::has($key))
<p class="alert alert-{{ $key }} text-center"><i class="fas fa-exclamation-triangle"></i> {{ Session::get($key) }}</p>
@endif
@endforeach