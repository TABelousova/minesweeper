<html>
<head>
</head>
<body>
<table border="1px">
<tr>
    <th>Имя игрока</th>
    <th>Время</th>
    <th>Размер поля</th>
    <th>Количество мин</th>
</tr>
</table>
<?php
require_once 'connection.php';
$link = mysqli_connect($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
$query = "SELECT 'name', 'game_time', 'field_size', 'mines'  FROM records";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if ($result) {
    echo "Выполнение запроса прошло успешно";
}
?>
</body>
</html>