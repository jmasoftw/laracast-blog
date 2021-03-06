{{-- We declare (expose) the properties that we exchange with the parent component to allow data passing through them. In the parent we pass the $posts collection --}}
{{-- and here we extract every single post of that collection to be able to access to their properties and display the ones we are interested in showing. --}}
@props(['post'])

<article
    class="transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl">
    <div class="py-6 px-5 lg:flex">
        <div class="flex-1 lg:mr-8">
            {{--TODO --}}
            <img src="/images/illustration-1.png" alt="Blog Post illustration" class="rounded-xl">
        </div>

        <div class="flex-1 flex flex-col justify-between">
            <header class="mt-8 lg:mt-0">
                <div class="space-x-2">
                    <a href="/categories/{{ $post->category->slug }}"
                       class="px-3 py-1 border border-blue-300 rounded-full text-blue-300 text-xs uppercase font-semibold"
                       style="font-size: 10px"> {{$post->category->name}} </a>
                </div>

                <div class="mt-4">
                    <h1 class="text-3xl">
                        <a href='/posts/{{ $post->slug }}'>
                        {!! $post->title  !!}
                        </a>
                    </h1>

                    <span class="mt-2 block text-gray-400 text-xs">
                                        Publicado <time> {{ \Carbon\Carbon::parse($post->published_at)->diffForHumans() }} </time>
                                    </span>
                </div>
            </header>

            <div class="text-sm mt-2">
                <p>
                    {!!  $post->excerpt !!}
                </p>
            </div>

            <footer class="flex justify-between items-center mt-8">
                <div class="flex items-center text-sm">
                    <img src="/images/lary-avatar.svg" alt="Lary avatar">
                    <div class="ml-3">
                        <h5 class="font-bold">{{ $post->author->name }}</h5>
{{--                        <h6> Academic Qualifications </h6>--}}
                    </div>
                </div>

                <div class="hidden lg:block">
                    <a href="/posts/{{ $post->slug  }}"
                       class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                    > Leer M??s </a>
                </div>
            </footer>
        </div>
    </div>
</article>
