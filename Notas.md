# Laracast-blog

Implementación dun blog en Laravel usando como inspiración os tutoriais de Laracasts

------

<u>**Capítulo 7: Centrado de artigos na paxina con estilos css**</u>

O centrado do post na páxina realizase mediante estilos css, dando un ancho máximo e unha marxe a ambos lados repartindo o espazo sobrante. 

```css
max-width: 600px;
margin: auto;
```

Aplicamos a todos os artcle un margin-top para separar cada un deles na páxina. O signo + seguido de novo de article significa que se aplicará a todos os article que se publiquen na páxina excepto o primeiro de todos.

```css
article + article{
   margin-top: 3rem;
}
```



**<u>Capítulo 10: Cacheo de contidos para mellorar os tempos de servicio</u>**

Para evitar ter que cada usuario que entre chame ó servidor para servir un resultado (post) que moi probablemente xa non cambiará unha vez que se almacena, mellor almacenamos en *caché* ese post para que os subseguintes accesos se fagan á memoria e non o servidor. Con isto melloramos os tempos de resposta para servir a páxina e evitamos tráfico innecesario no servidor. Empleamos a función de Laravel `cache():`

```php
Route::get('posts/{post}', function ($slug) {
    
    // Buscamos o post na carpeta resources/posts onde previamente gardaramos os posts
    $path = __DIR__ . "/../resources/posts/{$slug}.html"; 

    // Se non existe o arquivo que nos proporciona $path, volver á páxina principal
    if (! file_exists($path)) {
        return redirect('/');
    }
    
	// Á variable $post pásaselle o contido do arquivo en $path que se cachea durante
    // 1 día baixo a clave posts.{slug} que será a clave que teñamos que referenciar  
    // no noso código para volcar á páxina a información gardada (cacheada) en memoria.
    $post = cache()->remember("posts.{slug}", now()->addDay(), function () use ($path) {
		
        // recuperamos o contido do arquivo na ruta $path
        return file_get_contents($path);
    });
    
    return view('post', ['post' => $post]);
})->where('post', '[a-zA-Z0-9_\-]+');

// ->where() usase para filtrar a variable post que se introduce por parte do usuario
// na barra de direccións do navegador para indicar que post quere recuperar. A expresion
// regular actua como filtro permitindo so caracteres alfanumericos, guión baixo e guión.
```



<u>**Capítulo 12: Engadir metadatos en formato `yaml` a un post gardado como arquivo `html` e acceder logo a eles dende a vista para poder mostralos**</u>

O formato yaml de metadatos exixe que estes estean encapsulados entre  tres guións --- e na cabeceira do arquivo tal que así:

```txt
---
title: The title of this article
excerpt: The excerpt of this article
date_published: 22/02/2019
---

Other contents of the file not being metadata are placed afterwards...

```

 Para manipular este tipo de metadatos usamos a librería [https://github.com/spatie/yaml-front-matter]

No noso código, a chamada o método  `parseFile()` da librería  `YamlFrontMatter` devolve un obxeto de tipo documento (`Spatie\YamlFrontMatter\Document`) con este formato:

```yaml

    #matter: array:3 [
        "title" => "The title of this article"
        "excerpt" => "The excerpt of this article"
        "date_published" => 1621555200
    ]
    #body: """
        Here goes the content of the file (in our case the html content of every post)
      """
```

onde os metadatos figuran baixo a clave `matter` e os contidos do arquivo baixo a clave `body`.

A nós o que nos interesará será transformar este obxeto documento de Spatie nun obxeto de tipo array e con formato Post que podamos usar nas nosas vistas. Asi pois teremos que parchear este obxeto `Spatie\YamlFrontMatter\Document`, extraer a info que nos interesa e transformala nunha instancia dun post xenerico que usa a nosa aplicación e que se axusta a clase definida en `App/Models/Post.php`.

Empezamos engadindo un constructor na nosa clase Post en `App/Models/Post.php`:

```php
// Definimos as propiedades (atributos) que conforman o noso Post
    public $title;
    public $excerpt;
    public $date;
    public $body;
    public $slug;

// Esta función constructora devolve unha instancia nova dun Post se se lle proporcionan os atributos precisos
    /**
     * @param $title
     * @param $excerpt
     * @param $date
     * @param $body
     * @param $slug
     */
    public function __construct($title, $excerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }
```

Na nosa lóxica de negocio, por exemplo no arquivo de rutas `web.php:`

```php
Route::get('/', function () {

// Localizamos a carpeta onde temos almacenados os posts e leemolos pasandoos a variable $files.
    
    $files = File::files(resource_path('posts'));
    $posts = [];

// Iteramos sobre a colección de arquivos cargados en $files...   
    foreach ($files as $file) {
        
// Extraemos os metadatos e os contidos do arquivo obtendo documentos de tipo Spatie\YamlFrontMatter\Document ...
        $document = YamlFrontMatter::parseFile($file);

// e usamos a función constructora definida en App/Models/Post.php para construir unha nova instancia dun post pasandolle os valores extraidos do documento Spatie...     
        
        $posts[] = new Post(
            $document->title,
            $document->excerpt,
            $document->date,
            $document->body(),
            $document->slug
        );
    }
        
// que inxectamos a nosa vista para poder alí visualizar en pantalla os atributos do post que nos interese mostrar.
    return view("posts", [
        "posts" => $posts,
    ]);
```

Podemos facer algo de refactorización no código de arriba. Cando iteras sobre un conxunto de valores de entrada para acabar  almacenando os datos que che interesan nun array diferente o de entrada posimente interesa recurrir a funcións `array_map`. No noso caso:

```php
$posts = array_map(function ($file) {
    $document = YamlFrontMatter::parseFile($file);

    return new Post(
        $document->title,
        $document->excerpt,
        $document->date,
        $document->body(),
        $document->slug
    );
}, $files);
```

Deste xeito desfacemonos do bucle foreach e da declaración do array xa que coa funcion `array_map` iteramos sobre cada un dos obxectos individuais `$file` contidos na colección de documentos Spatie `$files`instanciando de cada vez un novo obxeto de tipo Post que se almacenará na variable `$posts` .

Incluso podemos volver a refactorizar empregando esta vez as coleccións (*Collections*) de Laravel. Para iso empregamos a *helper function* `collect()`que nos permite iterar sobre un array e encapsulalo devolvendonos unha colección de obxectos. Con `collect()` recolectamos os valores no array `$files`, iteramos sobre os seus valores mapeando cada elemento individual `$file` a un obxeto tipo Post que construímos ó voo. O resultado da operación podemos encapsulalo dentro dun método e retornalo tal que así:

```php
public static function all()
{
    // Find all the posts in the posts directory and collect them into a collection,
    // then loop(map) over each item and for each one parse the file into a document
    // (the parseFile method returns a document of type Spatie\YamlFrontMatter\Document).
    // Then loop(map) over each document and pull the properties of interest to create
    // an object of the desired format (Post). The result, a collection of post objects, 
    // is then returned to the code when this function is called.
    
    return collect(File::files(resource_path('posts')))
        ->map(fn($file) => YamlFrontMatter::parseFile($file))
        ->map(
            fn($document) => new Post(
                $document->title,
                $document->excerpt,
                $document->date,
                $document->body(),
                $document->slug
            )
        );
}
```



<u>**Capítulo 13: Revisar o estado da cache a traves da consola de comandos con artisan**</u>

Supoñamos que temos unha funcion que recupera datos dunha BD ou de un sistema de arquivos tal como esta:

```php
public static function all()
{
        return collect(File::files(resource_path('posts')))
            ->map(fn($file) => YamlFrontMatter::parseFile($file))
            ->map(
                fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                )
            )
            ->sortByDesc('date');
}
```

Se queremos almacenar os resultados desta operación na caché para non ter que chamar o servidor en cada execución deste método, encapsulamos o método dentro de outro metodo `cache()` asignandolle un tempo de vida que poden ser segundos, horas,minutos, ... calquera fracción de tempo incluído a opción `rememberForever` que garda os datos en memoria indefinidamente. Eso sí, neste caso debemos coidarnos de limpar a cache cada vez que executemos operacións que alteren o resulset orixinal como as accións de crear, modificar ou borrar posts para que cando se volva chamar a este método se *refresque* o resultset e se reflictan os novos cambios.

```
public static function all()
{
    return cache()->rememberForever('posts.all', function () {
    
        return collect(File::files(resource_path('posts')))
            ->map(fn($file) => YamlFrontMatter::parseFile($file))
            ->map(
                fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                )
            )
            ->sortByDesc('date');
            
    });
}
```

Para consultar o estado da cache basta executar este comando na consola artisan:

`php artisan tinker`

Aparecerá un shell integrado no que poderemos interaccionar co a nosa aplicación Laravel, por exemplo consultando que contidos almacena a nosa caché, escribindo:

`cache('posts.all')`

outra maneira de facer o mesmo:

`cache()->get('posts.all)');`

Se o facemos antes de encapsular o método o resultado será null xa que nada hai cacheado na variable `posts.all` cando ainda non se encapsulou o método.

```
cache('posts.all))
=> null
```

Pero se repetimos a operación logo de cachear o resultset, veremos aparecer a colección de posts que xenera a operación:

```php+HTML
=> Illuminate\Support\Collection {#3579
     all: [
       1 => App\Models\Post {#3576
         +title: "O meu quinto post",
         +excerpt: "Extracto que resume brevemente o contido deste post número 5",
         +date: 1627430400,
         +body: """
           \n
           <p>\n
               Incidunt ea eveniet rerum, nam voluptas doloribus voluptatibus nihil saepe impedit, nostrum expedita sequi ab dolorem error aut autem molestias minima. Rerum quia perspiciatis voluptatem earum fuga pariatur, nam minima consectetur! Natus provident iure neque nisi officiis esse velit accusantium ex a autem, amet nihil inventore consequuntur sunt doloremque eius praesentium porro. Iste vel aperiam optio natus fugiat fuga, omnis repellendus sunt voluptatem debitis, quaerat quia ipsam dicta\n
           </p>\n
           """,
         +slug: "o-meu-quinto-post",
       },
       0 => App\Models\Post {#3571
         +title: "O meu cuarto post",
         +excerpt: "Extracto que resume brevemente o contido deste post número 4",
         +date: 1625702400,
         +body: """
           \n
           <p>\n
               Lorem ipsum dolor sit amet, consectetur adipisicing elit. Explicabo ducimus enim eligendi nemo nesciunt dolorum, voluptates ea deleniti labore molestiae minima saepe dolorem maiores porro in ab debitis quaerat laborum numquam? Fuga consequuntur tempor
a laboriosam libero enim necessitatibus esse debitis deleniti! Delectus odio voluptatem rem alias, iusto ipsam sed possimus reiciendis obcaecati libero inventore nesciunt non eos molestiae nisi aperiam distinctio. Minus quod eum dolorem, aliquam, quam quas laboriosa
m facilis repudiandae, laborum enim voluptatibus commodi ut impedit ad dolor! Aliquid, et unde quisquam possimus animi a placeat. At optio sapiente cumque quis nostrum doloremque illo quidem eaque magnam, expedita neque cupiditate delectus ad a quos nemo eius sequi 
provident porro quibusdam. Vel perferendis perspiciatis, sed nesciunt aut voluptas quisquam dolorem consequatur incidunt, debitis quis, voluptatibus reiciendis nam molestias mollitia eos consequuntur ex harum repellendus expedita architecto excepturi sit. Esse aperi
am natus provident quisquam. Laboriosam repellat tempora quibusdam molestias, saepe aut quasi ipsa libero doloremque sit odit tenetur ex illo quo totam pariatur inventore eveniet eius cum! Velit error quisquam aspernatur ad mollitia explicabo vero beatae similique voluptatum aperiam in porro tempore amet maiores sunt, alias minima nam atque esse obcaecati tenetur optio temporibus. Quis commodi iure, illum excepturi alias quibusdam?\n
           </p>\n
           """,
         +slug: "o-meu-cuarto-post",
       },
     ],
   }
```

Se quisexemos limpar a cache dende a consola de tinker (o comando devolve `true` cando se executa con éxito). Estes cambios reflexarianse na web e cando refrescasemos o navegador e amosaría calquera cambio novo que se fixera no resultset dende a ultima operación de cacheo.

```
cache()->forget('posts.all');

=> true
```

Outros métodos son `put` para colocar elementos na *cache*:

```
cache()->put('foo', 'bar');

=>true
```

outra maneira de escribilo, neste caso preservando o valor só 3 segundos:

```
cache(['foo' => 'buzz'], now()->addSeconds(3));

=> tr
```



<u>**Capítulo 14: Entendendo as plantillas blade**</u>

Dado un div dentro dunha vista no que se mostra unha variable `$post` cuxa propiedade `body` engloba contido a insertar dentro de etiquetas html

```php+HTML
<div>
<?= $post->body; ?>
</div>
```

Se o sustituisemos por:

```php+HTML
<div>
{{ $post->body }}
</div>
```

o motor de pantillas de *Blade* escaparía as etiquetas html e amosaría todo o contido como texto plano incluidas as etiquetas. Para solucionar ese problema podemos recurrir á seguinte sintaxe:

```php+HTML
<div>
{!! $post->body !!}
</div>
```

Con esto estamoslle indicando a *Blade* de que conscientemente queremos que interprete todas as etiquetas html que conteña a variable a insertar ( o cal , por outro lado supón un risco potencial de seguridade pq permite executar contido de scripts) razón pola cal só se empleará cando nós teñamos o control do que se está a insertar.

Un truco interesante: a directiva *Blade* `@foreach` contén propiedades útiles como a propiedade `loop` que podemos usar para, por exemplo, aplicar estilos diferenciados as iteracións pares ou impares por exemplo engadindo clases que apliquen unha cor de fondo distinta as celdas impares dunha táboa:

```php+HTML
<tr><td class="{{ $loop->even ? 'fondo_verde' : '' }}"></td></tr>
```

para o cal teñamos previamente definida unha clase fondo_verde nalgún arquivo css:

```css
.fondo_verde {background-color: lightgreen;}
```



<u>**Capítulo 15: Separando estilos e contido dentro das vistas a traves de layout.blade.php**</u>

Na vistas de Laravel normalmente teremos 2 capas: unha primeira encargada de aplicar os estilos e clases css para dar formato ó texto e unha segunda capa de contenido na que se inserta a info que se quere mostrar en forma de etiquetas html e blade/php. Por reusabilidade e lexibilidade do código, debemos encapsular os estilos e a maquetación da páxina nun arquivo separado (por convención soe usarse `layout.blade.php`) mentras que o contido propiamente dito se mantén no arquivo da vista. Para esta separación podemos usar 2 enfoques igualmente válidos, é cuestion de preferencias ou gustos personais:

- <u>***Enfoque bottom-up***:</u> Usando directivas @extend, @yield e @section

Creamos o arquivo `layout.blade.php` dentro da carpeta `views` e incorporamos a el todo o código relacionado coa maquetación e os estilos:

```php+HTML
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>O meu blog</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
    </style>

    <link rel="stylesheet" href="/app.css">

<body class="antialiased">

    @yield('content')

</body>
</html>
```

Basicamente  empregamos a directiva `@yield` para declarar onde imos insertar contido e que nome terá a `section` que vai ser cargada dende o arquivo externo de vistas (neste caso é `posts.blade.php`). En posts.blade.php existira unha section que terá o nome que se referencia en layout.blade.php contendo o codigo html+php que se insertará no layout:

```php+HTML
@extends('layout')

@section('content')
        @foreach ($posts as $post)
        <article>
            <h1>
                <a href='/posts/{{ $post->slug }}'>
                {{$post->title}}
                </a>
            </h1>

            <div>
                {{$post->excerpt}}
            </div>
        </article>
        @endforeach
@endsection
```

Pode haber varias directivas `@yield` no arquivo `layout.blade.php` que apunten a distintas `section` emplazadas no arquivo de vistas `posts.blade.php` e que pasarán a ser insertadas nas distintas partes do layout onde se referencien.



- ***<u>Enfoque top-bottom:</u>*** Usando `components` e etiquetas `x-layout`

Empezamos engadindo unha carpeta `components` dentro da carpeta `views`. Todo arquivo nesa carpeta será entendido por *Laravel* como un compoñente de *Blade*. Trasladamos o arquivo `layout.blade.php` a esa carpeta cambiando as directivas:

```php+HTML
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>O meu blog</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
    </style>

    <link rel="stylesheet" href="/app.css">

<body class="antialiased">

    {{ $content }}

</body>
</html>
```

Vemos que cambiamos a sintaxis e agora facemos referencia ó contido usando `{{ $content }}`. No arquivo de vistas tan só teríamos que indicar onde esta ese contido. Definimos o compoñente usando a etiqueta `x-layout`e logo definimos o lugar onde radica o contido a insertar coa etiqueta `x-slot`

```php+HTML
<x-layout>

       <x-slot name="content">
           @foreach ($posts as $post)
               <article>
                   <h1>
                       <a href='/posts/{{ $post->slug }}'>
                           {{$post->title}}
                       </a>
                   </h1>

                   <div>
                       {{$post->excerpt}}
                   </div>
               </article>
           @endforeach
       </x-slot>

</x-layout>
```

Alternativamente podemos indicar un slot por defecto no arquivo `layout.blade.php` usando a variable especial `$slot`:

```
<body class="antialiased">

    {{ $slot }}

</body>
</html>
```

e deste xeito no arquivo da vista `posts.blade.php` podemos 'saltarnos' as etiquetas `x-slot` así:

    <x-layout>   
       
           @foreach ($posts as $post)
               <article>
                   <h1>
                       <a href='/posts/{{ $post->slug }}'>
                           {{$post->title}}
                       </a>
                   </h1>
    
                   <div>
                       {{$post->excerpt}}
                   </div>
               </article>
           @endforeach
       
    </x-layout>



<u>**Capítulo 19: Uso da consola de Tinker para crear novos usuarios para as nosas tablas**</u>

Entramos este comando na consola artisan:

`php artisan tinker`

e preparamonos para introducir os datos que queremos engadir á BD:

```bash
> php artisan tinker
Psy Shell v0.11.2 (PHP 7.4.0 — cli) by Justin Hileman
>>> $user = new User;                                                                                                                       
[!] Aliasing 'User' to 'App\Models\User' for this Tinker session.
=> App\Models\User {#3578}
>>> $user->name = 'Ju@nm@';                                                                                                                 
=> "Ju@nm@"
>>> $user->email = 'juanmacc@gmail.com';                                                                                                    
=> "juanmacc@gmail.com"
>>> $user->password = bcrypt('this_is_my_password');                                                                                        
=> "$2y$10$z2wlWDBQTlcU2bYTWScEgu5RGLg1QOt0RsMQ19ZRRxoxRxTUScLWO"
>>> $user->save();      
```

Unha vez executado, veremos como se engadíu un novo rexistro á BD cos datos subministrados.

Entre outros comandos en Tinker podemos obter todos os usuarios da base de datos, o cal nos devolverá unha colección de obxetos user (reflexando Model/User.php):

```bash
>>> User::all();        
```

Devolverianos algo como esto, unha colección de usuarios:

```bash
>>> User::all();                                                                                                                            
=> Illuminate\Database\Eloquent\Collection {#4523
     all: [
       App\Models\User {#4524
         id: 1,
         name: "Admin",
         email: "admin@admin.com",
         email_verified_at: null,
         #password: "$2y$10$vUIzDlvfpu2yOATsPYcPaOTY/zgbgwViLIWSfZxSlmRBFV.g/fmOW",
         #remember_token: null,
         created_at: null,
         updated_at: null,
         deleted_at: null,
       },
       App\Models\User {#4525
         id: 2,
         name: "Karen Okuneva Sr.",
         email: "user1@user1.com",
         email_verified_at: null,
         #password: "$2y$10$AjE6mXWopGci/VSK4ZXWu.2RSK70/0luwrfX4u2NNn7djyAdlhY/K",
         #remember_token: null,
         created_at: "2021-11-23 15:09:49",
         updated_at: "2021-11-23 15:09:49",
         deleted_at: null,
       },
       App\Models\User {#4526
         id: 3,
         name: "Miss Danika Okuneva III",
         email: "user2@user2.com",
         email_verified_at: null,
         #password: "$2y$10$Vrg8y/ktIlsfXbGRoLZpy.gT9rKGnPZ85u.1nRHkGI3mbx6Bqj8ye",
         #remember_token: null,
         created_at: "2021-11-23 15:09:49",
         updated_at: "2021-11-23 15:09:49",
         deleted_at: null,
       },
       App\Models\User {#4527
         id: 4,
         name: "Darren Huels DVM",
         email: "user3@user3.com",
         email_verified_at: null,
         #password: "$2y$10$sozpaXCjHEhGiLHn1JwLPuxyCknzsXmNS6.AX4rIWMQTKmPrQQO/i",
         #remember_token: null,
         created_at: "2021-11-23 15:09:49",
         updated_at: "2021-11-23 15:09:49",
         deleted_at: null,
           },
     ],
   }
>>>     

```

Podemos usar o método `pluck` indicando o nome dunha clave para extraer desa colección os valores que se correspondan con esa clave (campo ed BD), por exemplo o nome de todos os usuarios da colección:

```bash
>>> $users->pluck('name');                                                                                                                  
=> Illuminate\Support\Collection {#4515
     all: [
       "Admin",
       "Karen Okuneva Sr.",
       "Miss Danika Okuneva III",
       "Darren Huels DVM",
       "Nola Kreiger II",
       "Yasmin Sipes",
       "Garnett Bosco",
       "Samantha Luettgen",
       "Monique Buckridge III",
       "Mr. Demarcus Dare DDS",
       "Ethyl Gutmann",
     ],
   }

```

Asi pois o metodo `pluck` é o equivalente a esto:

```php
$users->map(function($user) { return $user->name;});
```

