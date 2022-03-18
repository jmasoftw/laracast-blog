<x-layout>

           @foreach ($posts as $post)
               <article>
                   <h1>
                       <a href='/posts/{{ $post->id }}'>
                           {!! $post->title !!}
                       </a>
                   </h1>

                   <p>
                       <a href='/categories/{{ $post->category->slug }}'> {!! $post->category->name !!} </a>
                   </p>

                   <h5>
                       Publicado por <a href='/authors/{{ $post->author->slug }}'> {!! $post->author->name !!}  </a>
                   </h5>

                   <div>
                       {!! $post->excerpt !!}
                   </div>
               </article>
           @endforeach

</x-layout>



