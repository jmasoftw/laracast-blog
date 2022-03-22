{{-- We declare (expose) the properties that we exchange with the parent component to allow data passing through them. In the parent we pass the $posts collection --}}
{{-- and here we extract every single post of that collection to be able to access to their properties and display the ones we are interested in showing. --}}
@props(['post'])

{{-- $atributes is a native variable that includes all the html attributes inherited from the parent. We'll merge the parent's html classes with this ones here to apply them combined to this article --}}
{{-- a variable col-span from the parent. The parent has 6 columns and we'll apply a conditional col-span-3 (50% of the space) for the second and third posts and a --}}
{{-- col-span-2 for the rest of the posts allowing a grid with 2 posts for the post two and three and a grid of 3 posts for the fourth and following --}}
<article {{ $attributes->merge(['class' => "transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl"]) }} >
    <div class="py-6 px-5">
        <div>
            <img src="./images/illustration-3.png" alt="Blog Post illustration" class="rounded-xl">
        </div>

        <div class="mt-8 flex flex-col justify-between">
            <header>
                <div class="space-x-2">
                    <x-category-button :category='$post->category'/>
                </div>

                <div class="mt-4">
                    <h1 class="text-3xl">
                        <a href="/posts/{{ $post->slug }}">
                            {!! $post->title !!}
                        </a>
                    </h1>

                    <span class="mt-2 block text-gray-400 text-xs">
                        Publicado <time> {{ \Carbon\Carbon::parse($post->published_at)->diffForHumans() }} </time>
                    </span>
                </div>
            </header>

            <div class="text-sm mt-4">
                <p>
                    {!! $post->excerpt !!}
                </p>

                <p class="mt-4">
                    {!! $post->body !!}
                </p>
            </div>

            <footer class="flex justify-between items-center mt-8">
                <div class="flex items-center text-sm">
                    <img src="/images/lary-avatar.svg" alt="Lary avatar">
                    <div class="ml-3">
                        <h5 class="font-bold"> {{ $post->author->name }} </h5>
{{--                        <h6> Academic Qualifications </h6>--}}
                    </div>
                </div>

                <div>
                    <a href="/posts/{{ $post->slug }}"
                       class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                    > Leer Más  </a>
                </div>
            </footer>
        </div>
    </div>
</article>
