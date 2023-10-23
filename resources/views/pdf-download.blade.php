<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">

        .centered {
            position: absolute;
            top: {{ $top }};
            left: {{ $left }};
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body class="bg-image" style="background-image: url({{ $certificate_url }});background-image-resize: 6; position: absolute;background-repeat: no-repeat;">
    <div style="text-align:center;" class="centered">
        {{-- {{$top}}{{$left}}{{$certificate_url}} --}}
        {{-- <span style="font-size:35px;color:#444444;"><i>This certificate is proudly presented to</i></span><br> --}}
        <span style="font-size:50px;color:#f78809;font-family:'allura-regular' ">{{ $full_name }}</span><br><br>{{--  --}}
        <span style="font-size:30px;color:#444444;">In recognition of your completion of the </span>
        <span
            style="font-size:25px;color:#444444;font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif"><b>{{ $assessment_name }}</b></span><br>
        <span style="font-size:30px;color:#444444;">in Ni-kshay Setu Application.</b></span>
        <span style="font-size:30px;color:#444444;">You have achieved &nbsp;</b></span>
        <span style="font-size:25px;color:#444444;font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif"><b>{{ $percentage }} points</b></span>
        <span style="font-size:30px;color:#444444;">in the </span><br>
        <span style="font-size:30px;color:#444444;">assessment. We congratulate you and present you this certificate of
        </span><br>
        <span style="font-size:30px;color:#444444;">completion. </span>
    </div>
</body>

</html>
