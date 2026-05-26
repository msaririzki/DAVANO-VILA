@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => 'Pilih opsi',
    'required' => false,
])

<div x-data="{
        open: false,
        value: '{{ $selected }}',
        options: {!! json_encode($options) !!},
        get displayValue() {
            if (this.value === '') return '{{ $placeholder }}';
            return this.options[this.value] || '{{ $placeholder }}';
        }
    }"
    class="relative w-full text-left"
    @click.away="open = false"
>
    <!-- Hidden Select untuk Form Submission -->
    <select name="{{ $name }}" x-model="value" class="hidden" {{ $required ? 'required' : '' }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $val => $label)
            <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>

    <!-- Tombol Pemicu -->
    <button type="button" 
            @click="open = !open"
            class="w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm flex items-center justify-between shadow-sm transition-all focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 hover:shadow-md"
            :class="{ 'ring-1 ring-emerald-500 border-emerald-500': open, 'text-neutral-900 font-bold': value !== '', 'text-neutral-500': value === '' }"
    >
        <span x-text="displayValue" class="truncate"></span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400 transition-transform duration-300 ease-out" :class="{'rotate-180 text-emerald-500': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Panel Pilihan (Dropdown Menu) -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-[60] w-full mt-2 bg-white rounded-xl shadow-xl shadow-black/5 border border-neutral-100 overflow-hidden py-1.5"
         style="display: none;"
    >
        <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-emerald-200 scrollbar-track-transparent">
            <!-- Pilihan Kosong (Placeholder) -->
            <button type="button" @click="value = ''; open = false" 
                    class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-neutral-50 focus:outline-none"
                    :class="{'bg-emerald-50/50 text-emerald-700 font-bold': value === '', 'text-neutral-600': value !== ''}">
                {{ $placeholder }}
            </button>
            
            <!-- Daftar Pilihan -->
            <template x-for="key in Object.keys(options)" :key="key">
                <button type="button" @click="value = key; open = false" 
                        class="w-full text-left px-4 py-2.5 text-sm transition-colors hover:bg-emerald-50 focus:outline-none flex items-center justify-between"
                        :class="{'bg-emerald-50 text-emerald-800 font-bold': value === key, 'text-neutral-700 font-medium': value !== key}">
                    <span x-text="options[key]"></span>
                    <svg x-show="value === key" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </template>
        </div>
    </div>
</div>
