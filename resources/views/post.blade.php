<x-layout>

    <article>

            <h1>
                {!! $post->title !!}
            </h1>

            <h5>
                Publicado por <a href='/authors/{{ $post->author->slug }}'> {!! $post->author->name !!}  </a>  el {!! $post->published_at !!} en la categoría  <a href='/categories/{{$post->category->slug}}'> {{ $post->category->name  }} </a>
            </h5>

            <div>
                <p> {!! $post->body !!} </p>
            </div>

    </article>

    <a href="/"> Volver </a>

</x-layout>
