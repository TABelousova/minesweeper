<html>
<head>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <table class="result_field">
    <tr><th>Игрок</th><th>Время</th><th>Размер поля</th><th>Количество мин</th></tr>
<?php
require_once 'connection.php';
$link = mysqli_connect($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
$query = "INSERT INTO records (name, game_time, field_size, mines) VALUES ('gamer_1', 112, 12, 3);";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
$query = "SELECT name, game_time, field_size, mines FROM records;";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
if($result)
{
    $rows = mysqli_num_rows($result); // количество полученных строк
    for ($i = 0 ; $i < $rows ; ++$i)
    {
        $row = mysqli_fetch_row($result);
        echo "<tr>";
            for ($j = 0 ; $j < 4 ; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
     
    // очищаем результат
    mysqli_free_result($result);
}
?>
</table>
</body>
</html>