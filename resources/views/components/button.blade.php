<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-excel-dark border border-transparent rounded-excel font-semibold text-xs text-white uppercase tracking-widest hover:bg-excel-light focus:bg-excel-light active:bg-excel-dark focus:outline-none focus:ring-2 focus:ring-excel-light focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
