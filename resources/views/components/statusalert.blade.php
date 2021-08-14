@if (session('status'))
        <div x-data="{ show: true }">
            <div class="w-full items-center fixed top-0 mt-12 z-20 animate-zoom" x-show="show">
                <div class="md:w-1/2 md:mx-auto relative">
                    <p class="p-2 hover:bg-primary-300 rounded absolute right-2 top-2" x-on:click="show = false"><x-icons.close class="h-3 w-3" /></p>
                    <div {{ $attributes->merge(['class' => 'text-sm border border-t-8 rounded text-shamrock border-green-600 bg-thevert px-3 py-4 my-4']) }}
                        role="alert">
                        {{ session('status') }}
                    </div>
                </div>
            </div>
        </div>
    @endif