@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm text-rougis']) }}>{{ $message }}</p>
@enderror