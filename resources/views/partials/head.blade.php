<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'FIDELISK'}}</title>

<!-- Favicon - reemplaza el emoji por los archivos reales -->


<link rel="icon" type="image/png" sizes="80000x5000" content="rounded-full object-cover" href="/images/logo.png">


<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
