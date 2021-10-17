<!doctype html>
<html lang="en">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Collect">
        <meta name="author" content="Fitsys">

        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">

        <title>Collect</title>
    </head>

    <body class="antilaliased font-sans bg-gray-200">

        <main id="app"></main>

        <!-- <script src="/js/manifest.js"></script> -->
        <!-- <script src="/js/vendor.js"></script> -->
        <script>
            window.isDevMode = {{ env('APP_DEBUG') ? 'true' : 'false' }}
        </script>
        <script src="{{ mix('/js/app.js') }}"></script>

        <!-- fix for old browsers -->
        <script type="text/javascript">
            if (typeof Object.values != 'function') {
                Object.values = function(target) {
                    'use strict';
                    if (target == null) {
                        throw new TypeError('Cannot convert undefined or null to object');
                    }

                    return Object.keys(target).map(function(key) {
                        return target[key];
                    });
                };
            }
        </script>
    </body>
</html>