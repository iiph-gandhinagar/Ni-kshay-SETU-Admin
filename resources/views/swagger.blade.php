<!DOCTYPE html>
<html>

<head>
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/swagger-ui/swagger-ui.css') }}">
</head>

<body>
    <div id="swagger-ui"></div>

    <script src="{{ asset('vendor/swagger-ui/swagger-ui-bundle.js') }}"></script>
    <script src="{{ asset('vendor/swagger-ui/swagger-ui-standalone-preset.js') }}"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "{{ asset('docs/swagger.json') }}",
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: 'BaseLayout'
            });
        }
    </script>
</body>

</html>
