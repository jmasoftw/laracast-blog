<header class="max-w-xl mx-auto mt-20 text-center">
    <h1 class="text-4xl mb-5">
        Resuelve tus <span class="text-blue-500"> Dudas Tributarias </span>
    </h1>

    <h2 class="inline-flex mt-5"> Un blog de Álvaro Canosa <img  class='-mt-5 ml-2' src="/images/avatar.jpg" alt="logo del autor"></h2>

    <p class="text-sm mt-10">
        Bienvenid@ a mi página web. Aqui escribo sobre temas relacionados con la Ley General Tributaria.
        Si tienes dudas sobre estos temas y quieres resolverlas, tal vez podrías echar un vistazo a mis contenidos y aprender conmigo.
    </p>

    <div class="space-y-2 lg:space-y-0 lg:space-x-4 mt-8">
        <!--  Category -->
        <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">

            <x-dropdown>

                {{-- This references a non default slot defined in the dropdown component as 'trigger' and has to be explicity declared --}}
                <x-slot name='trigger'>
                    <button
                        @click="show = !show"
                        class='py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex'>
                        {{-- If a category is passed through the route, display its name otherwise show a generic 'All' categories --}}
                        {{ isset($currentCategory) ? ucwords($currentCategory->name) : 'Categorías' }}

                        <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                             height="22" viewBox="0 0 22 22">
                            <g fill="none" fill-rule="evenodd">
                                <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                                </path>
                                <path fill="#222"
                                      d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                            </g>
                        </svg>
                    </button>

                </x-slot>

                {{-- All this content matches the default slot in the dropdown component --}}
                <a href='/' class='block text-left px-3 text-sm leading-6 hover:bg-blue-500 focus:bg-blue-500 hover:text-white focus:text-white'> Todas </a>

                @foreach($categories as $category)
                    {{-- We apply conditional styles: if the category in the route matches the selected in the drop-down, then the category
                    in the current loop is the selected one and we paint it blue with white text to mark it as the selected one otherwise leave it as is. --}}
                    <a href='/?category={{ $category->slug }}' class="block text-left px-3 text-sm leading-6 hover:bg-blue-500 focus:bg-blue-500
            hover:text-white focus:text-white {{ isset($currentCategory) && $currentCategory->id === $category->id ? 'bg-blue-500 text-white' : '' }}">
                        {{ ucwords($category->name) }}
                    </a>
                @endforeach

            </x-dropdown>

        </div>

        <!-- Other Filters -->
        <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">
            <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">

                <option value="category" disabled selected>Filtros
                </option>

            </select>

            <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
                 height="22" viewBox="0 0 22 22">
                <g fill="none" fill-rule="evenodd">
                    <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                    </path>
                    <path fill="#222"
                          d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                </g>
            </svg>
        </div>

        <!-- Search -->
        <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
            <form method="GET" action="#">
                <input type="text" name="search" placeholder="Buscar en el blog ..."
                       class="bg-transparent placeholder-black font-semibold text-sm"
                        value="{{ request('search') }}">
            </form>
        </div>
    </div>
</header>
