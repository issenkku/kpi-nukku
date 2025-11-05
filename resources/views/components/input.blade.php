@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => null, 'autocomplete' => null])

<label for="{{ $name }}" class="block">
    @if ($label)
        <span class="text-sm font-medium text-slate-700">{{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </span>
    @endif
    <input type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}" 
        autocomplete="{{ $autocomplete ?? ($type === 'email' ? 'email' : ($type === 'password' ? 'current-password' : 'off')) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->whereStartsWith('x-') }}
        {{ $attributes->whereStartsWith('@') }}
        {{ $attributes->whereStartsWith(':') }}
        @class([
            'p-2 mt-1 w-full bg-white rounded-xl border placeholder-slate-400 text-sm md:text-base transition',
            'border-red-500 focus:border-red-500 focus:ring-red-200' => $errors->has($name),
            'border-slate-300 hover:shadow-md hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500' => !$errors->has($name),
        ])>
    <x-input-error :name="$name" />
</label>
