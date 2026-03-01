@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-primary-200 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 rounded-xl text-sm transition-colors duration-150']) !!}>




