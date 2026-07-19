@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-excel-dark focus:ring-excel-light rounded-md shadow-sm']) !!}>
