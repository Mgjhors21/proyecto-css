<!DOCTYPE html>
<html>

<head>
    <title>{{ $data['asunto'] }}</title>
</head>

<body>
    <p><strong>Recibidos</strong></p>
    <p>{{ $data['descripcion'] }}</p>
    <p>Fecha: {{ now()->format('D, d M, H:i') }} (hora local)</p>
    <p>Agradezco su atención y quedo atento a sus comentarios.</p>
</body>

</html>
