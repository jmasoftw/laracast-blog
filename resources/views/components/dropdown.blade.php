{{-- The non default trigger slot has to be passed to the parent via a shared property and has to be declared here --}}
@props(['trigger'])

{{-- This file only encapsules the specific Alpine logic for the dropdown leaving the view aspects to other files --}}
{{-- If the user clicks away the dropdown, close it --}}
<div x-data="{ show: false }" @click.away="show = false" class="relative">

    {{-- Trigger --}}
    <div @click="show = ! show">
        {{ $trigger }}
    </div>

    {{-- Links --}}
    <div x-show="show" class="py-2 absolute bg-gray-100 mt-2 rounded-xl w-full z-50 overflow-auto max-h-52" style="display: none">

        {{-- Here inserts  the content between <x-dropdown> tags in the partial _posts-header.blade.php --}}
        {{ $slot }}
    </div>
</div>

