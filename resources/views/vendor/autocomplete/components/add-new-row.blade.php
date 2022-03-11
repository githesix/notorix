@props(['inputText'])

<div {{ $attributes }}>
    <div class="px-3 py-2">
        {{ __("Add") }} "{{ $inputText }}"
    </div>
</div>
