<div {!! isset($attributes) ? $attributes->merge(['class' => 'relative']) : 'class="relative"' !!}>
    <div
      x-data="{
        open: @entangle('showDropdown'),
        search: @entangle('search'),
        selected: @entangle('selected'),
        highlightedIndex: 0,
        highlightPrevious() {
          if (this.highlightedIndex > 0) {
            this.highlightedIndex = this.highlightedIndex - 1;
            this.scrollIntoView();
          }
        },
        highlightNext() {
          if (this.highlightedIndex < this.$refs.results.children.length - 1) {
            this.highlightedIndex = this.highlightedIndex + 1;
            this.scrollIntoView();
          }
        },
        scrollIntoView() {
          this.$refs.results.children[this.highlightedIndex].scrollIntoView({
            block: 'nearest',
            behavior: 'smooth'
          });
        },
        {{-- updateSelected(id) {
          this.selected = id;
          this.open = false;
          this.highlightedIndex = 0;
        }, --}}
        updateSelected(id, label) {
          this.selected = id;
          this.search = label;
          this.open = false;
          this.highlightedIndex = 0;
        },
    }">
    <div
      x-on:value-selected="updateSelected($event.detail.id, $event.detail.label)">
      <span>
        <div>
          <input
            wire:model.debounce.300ms="search"
            x-on:keydown.escape.stop.prevent="open = false"
            x-on:keydown.arrow-down.stop.prevent="highlightNext()"
            x-on:keydown.arrow-up.stop.prevent="highlightPrevious()"
            x-on:keydown.enter.stop.prevent="$dispatch('value-selected', {
              id: $refs.results.children[highlightedIndex].getAttribute('data-result-id'),
              label: $refs.results.children[highlightedIndex].getAttribute('data-result-label')
            })"
            class="w-full form-input rounded-md shadow-sm">
        </div>
      </span>
  
      <div
        x-show="open"
        x-on:click.away="open = false"
        class="absolute right-0 w-full bg-white h-36 overflow-y-auto">
          <ul x-ref="results">
            @forelse($results as $index => $result)
              <li
                wire:key="{{ $result->id }}"
                x-on:click.stop="$dispatch('value-selected', {
                  id: '{{ $result->id }}',
                  label: '{{ $result->label }}',
                })"
                :class="{
                  'bg-bleuis': {{ $index }} === highlightedIndex,
                  'text-white': {{ $index }} === highlightedIndex
                }"
                class="p-1 hover:bg-bleuis hover:text-white cursor-pointer"
                data-result-id="{{ $result->id }}"
                data-result-label="{{ $result->label }}">
                  <span>
                    {{ $result->label }}
                  </span>
              </li>
            @empty
              <li class="p-1">{{__("No results found")}}</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
  
  