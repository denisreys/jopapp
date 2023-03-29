<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" sizes="180x180" href="./images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="./images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon/favicon-16x16.png">
        <link rel="manifest" href="./images/favicon/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <title>Jopapp</title>
        @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app" v-cloak></div>
    </body>
</html>
