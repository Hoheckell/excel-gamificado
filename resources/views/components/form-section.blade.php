@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-5 py-5 bg-white sm:p-6 shadow-sm border border-[--border-light] rounded-tl-lg rounded-tr-lg {{ isset($actions) ? '' : 'rounded-b-lg' }}">
                <div class="grid grid-cols-6 gap-5">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-5 py-3 bg-[#f8faf8] border border-t-0 border-[--border-light] sm:px-6 shadow-sm rounded-bl-lg rounded-br-lg">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
