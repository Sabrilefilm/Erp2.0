@props(['inputId' => 'password', 'label' => 'Mot de passe', 'name' => 'password', 'required' => false, 'placeholder' => ''])
<div>
    @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-300 mb-1">{{ $label }}</label>
    @endif
    <div class="relative flex items-stretch">
        <input type="password" name="{{ $name }}" id="{{ $inputId }}" {{ $required ? 'required' : '' }}
               placeholder="{{ $placeholder }}"
               class="w-full px-3 py-2 pr-12 rounded-lg bg-gray-800 border border-gray-700 text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 {{ $attributes->get('class') }}"
               {{ $attributes->except('class') }}>
        <button type="button" onclick="window.togglePassword('{{ $inputId }}', this)" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" title="Afficher le mot de passe" aria-label="Afficher le mot de passe">
            <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
        </button>
    </div>
    @error($name)<p class="mt-1 text-sm text-accent-red">{{ $message }}</p>@enderror
</div>
