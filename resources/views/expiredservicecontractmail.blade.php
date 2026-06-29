<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link href="/css/main.css" rel="stylesheet">

    <style>
        body {
            font-family: Helvetica, Sans-Serif;
        }

        .gray {
            color: #AAAAAA;
        }

        .bezeichner {
            color: #AAAAAA;
        }
        table {
            width: 100%
        }
        .img-responsive {
            display: block;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    Folgende Serviceverträge sind abgelaufen:<br>
    <br>
    <br>
    @foreach ($contracts as $contract)
        Ablaufdatum {{\Carbon\Carbon::parse($contract->expire_at)->format('d.m.Y')}}<br>
        {{$contract->customer_company}}<br>
        <br>
    @endforeach

</body>
</html>