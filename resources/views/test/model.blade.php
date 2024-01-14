

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Display All Model Data</h1>
    {{ $model }}

    <br>
    <br>
    <hr>
    <br>
    <br>

    <h1>Display Model Data</h1>
    
    <ul>
        @foreach ($model as $item)
            <li>{{ $item}}</li>
        @endforeach
    </ul>
</body>
</html>