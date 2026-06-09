@props(['route', 'label', 'active' => false])
<a href="{{ route($route) }}"
   class="block rounded-lg px-3 py-2 {{ $active || request()->routeIs($route.'*') ? 'bg-emerald-600/20 text-emerald-400' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
    {{ $label }}
</a>
