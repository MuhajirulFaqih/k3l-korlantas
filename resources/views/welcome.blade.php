<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="base-url" content="{{ url('/') }}">
        <meta name="wss-vc" content="{{ env('VC_WSS_URL') }}">
        <meta name="echo" content="{{ env('SOCKET_URL').':'.env('SOCKET_PORT') }}">
        <meta name="token" content="{{ csrf_token() }}">
        <meta name="app-name" content="{{ ENV('APP_NAME') }}">
        <meta name="instansi" content="{{ ENV('APP_INSTANSI') }}">
        <meta name="default-lat" content="{{ ENV('DEFAULT_LAT') }}">
        <meta name="default-lng" content="{{ ENV('DEFAULT_LNG') }}">
        <meta name="emergency-audio-url" content="{{ url('/audio/emergency.mp3') }}">
        <meta name="kejadian-audio-url" content="{{ url('/audio/kejadian.mp3') }}">
        <meta name="ringbacktone-audio-url" content="{{ url('/audio/ringbacktone.mp3') }}">
        <meta name="marine-url" content="{{ env('MARINE_TRAFFIC_URL') }}">
        <meta name="radar-url" content="{{ env('FLIGHT_RADAR_URL') }}">
        <meta name="dana-desa" content="{{ env('DANA_DESA') }}">
        <meta name="mastumapel" content="{{ env('MASTUMAPEL') }}">
        <meta name="socket-prefix" content="{{ env('SOCKET_PREFIX') }}">
        <meta name="has-vc" content="{{ env('HAS_VC', true) }}">
        <meta name="cid" content="{{ env('CLIENT_ID') }}">
        <meta name="csc" content="{{ env('CLIENT_SECRET') }}">
        <meta name="app-name" content="{{ env('APP_NAME') }}">
        <meta name="induk" content="{{ env('INDUK') }}">
        <meta name="dana-desa" content="{{ env('DANA_DESA') }}">
        <meta name="penerangan-satuan" content="{{ env('PENERANGAN_SATUAN', 0) }}">
        <meta name="visi-misi" content="{{ env('VISI_MISI', 0) }}">
        <meta name="kegiatan-bhabin" content="{{ env('KEGIATAN_BHABIN') }}">

        <title>{{ ENV('APP_NAME') }}</title>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Electrolize&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body>

    <div id="app"></div>

    <script src="{{ mix('js/main.js') }}"></script>
    </body>
</html>
