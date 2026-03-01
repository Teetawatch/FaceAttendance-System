<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-primary-200 rounded-xl font-semibold text-sm text-primary-700 hover:bg-primary-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 disabled:opacity-25 transition-colors duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>




