<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        @if(request()->routeIs('login') && config('captcha.enabled'))
            @if(mb_strtoupper(config('captcha.type')) === 'GEETEST')
                @vite('resources/js/plugins/gt4.js')
            @elseif(mb_strtoupper(config('captcha.type')) === 'CLOUDFLARE')
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js"></script>
            @endif
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
