<!DOCTYPE html>
<html>
    <head>
        <title>Starter App</title>
        <link href="/css/app.css" rel="stylesheet">
    </head>
    <body>
        <header>
            @include('partials.navigation')
        </header>
        
        @yield('content')

    </body>
</html>
