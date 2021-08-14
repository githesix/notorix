<div>
            <div class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150">
                @if(isset($title))
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                @endif
            </div>
            <div class="bg-white px-4 sm:p-6">
                <div class="space-y-6">
                    {{ $body }}
                </div>
            </div>

            <div class="mt-10 mb-6 flex justify-center">
                <span class="mr-2">
                    <button wire:click="$emit('closeModal')" class="w-32 shadow-sm inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:border-gray-700 focus:shadow-outline-teal active:bg-gray-700 transition ease-in-out duration-150">
                        {{ __('No')}}
                    </button>
                </span>
                <span>
                    <button wire:click="confirm" class="w-32 shadow-sm inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-teal active:bg-red-700 transition ease-in-out duration-150">
                        {{ __('Yes')}}
                    </button>
                </span>
            </div>
</div>
