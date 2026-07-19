<div class="min-h-screen excel-grid-bg flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white border border-[--border-color] rounded-lg overflow-hidden" style="box-shadow: 0 12px 35px rgba(16, 124, 65, 0.1);">
        {{ $slot }}
    </div>
</div>
