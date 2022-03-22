{{-- We declare (expose) the properties that we exchange with the parent component to allow data passing through them. In the parent we pass the $posts collection --}}
{{-- and here we extract every single post of that collection to be able to access to their properties and display the ones we are interested in showing. --}}
@props(['post'])

{{-- We render the grid only if there's at least one post. We skip the first post because is showed in the post-featured-card component above in this ccomponent's parent --}}
{{-- We render an adaptable grid of 6 columns. Using col-span classes that are different for the first 2 posts: spanning 3 of 6 cols each results in 2 post per row for these two ones --}}
{{-- As for the rest, being out of the third iteration, they fill 2 of the 6 columns available each one resulting in rows with 3 post for every other space in the rest of the page.--}}
@if ($posts->count() > 1)
    <div class="lg:grid lg:grid-cols-6">
        @foreach($posts->skip(1) as $post)
            {{-- 'Cos we excluded the first post,an iteration < 3 comprises post 2 and three applying to them a span of 3 colums of the 6  and filling 50% of the space each --}}
            {{--  The rest of the posts, being out of the third iteration are applyied a col-span of 2 filling 2 of the 6 columns available each and creating a row with 3 posts --}}
            <x-post-card :post='$post' class="{{ $loop->iteration < 3 ? 'col-span-3' : 'col-span-2' }}" />
        @endforeach
    </div>
@endif
