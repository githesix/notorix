@props(['title' => 'Title', 'bg' => 'bg-white', 'colors' => 'bg-primary text-white border-primary'])
<div class="w-full md:w-1/2 xl:w-1/3 p-3">
    <div class="{{ $bg }} border-transparent rounded-lg shadow-lg">
        <div {{ $attributes->merge(['class' => 'uppercase border-b-2 rounded-tl-lg rounded-tr-lg p-2 ' . $colors]) }}>
            <h5 class="font-bold uppercase text-center">{{$title}}</h5>
        </div>
        <div class="p-5">
            {{$slot}}
        </div>
    </div>
</div>