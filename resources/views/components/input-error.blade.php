@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-xs text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3 flex-shrink-0"></i>{{ $message }}</li>
        @endforeach
    </ul>
@endif




