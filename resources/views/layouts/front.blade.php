<!DOCTYPE html>
<html>
    <head>
        <title>Starter App</title>
        <link href="/css/app.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <nav class="navigation">
                <ul class="navigation__list">
                    <li class="navigation__item">
                        <a class="navigation__link --cardinal" href="/">
                            <svg viewBox="0 0 136 136" class="cardinal-solutions">
                                <g>
                                    <path class="swurve" d="M75.815 9.336l-14.08-8.143s-3.705-2.267-9.13 2.31L6.61 44.753s-8.215 6.182-6.198 11.59L21.89 118.92s3.3 13.294 14.098 6.997L62.7 103.04s9.36-6.484 5.06-17.396c0 0-.673-1.603-1.3-2.414L55.485 68.393s-7.077-10.005-5.543-19.088c0 0 .598-7.692 10.647-18.152l12.47-11.177s4.456-3.472 4.39-7.24c0 0 .382-1.988-1.635-3.4"/>
                                    <path class="square" d="M123.194 36.735l-17.143-9.912-8.29-4.774-.058-.03c-4.325-2.5-11.17-1.28-15.545 2.593L61.865 42.555c-4.313 3.815-5.47 9.634-2.423 13.2L76.73 75.998c.896 1.052 1.95 1.918 3.118 2.59 4.745 2.745 11.285 2.314 15.997-1.728l27.693-23.788c5.847-5.017 5.584-12.407-.344-16.337"/>
                                </g>
                            </svg> 
                        </a>
                    </li>
                    <li class="navigation__item"><a class="navigation__link --normal" href="/login">Login</a></li>
                </ul>
            </nav>
        </header>
        
        @yield('content')

    </body>
</html>
