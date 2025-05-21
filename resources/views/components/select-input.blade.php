@props(['options' => [], 'value' => null])

<select {{ $attributes->merge(['class' => 'block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500']) }}>
    @foreach($options as $optionValue => $label)
        <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select> 