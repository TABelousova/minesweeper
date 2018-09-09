<html>

<head>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>

</head>

<?php
require_once 'connection.php'; // подключаем скрипт
// подключаемся к серверу
$link = mysqli_connect($host, $user, $password, $database) 
    or die("Ошибка " . mysqli_error($link));
    $query ="SELECT * FROM records";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
if($result)
{
    echo "Выполнение запроса прошло успешно";
}
?>
<body id="wer">
    <div class="popup_window" id="start_setting">
        <form action="index.html" onsubmit="onSubm(event);" id="start_setting_form" name="start_setting_form">
            <p>Ширина игрового поля</p>
            <input type="number" min="2" max="99" step="1" size="20" name="n_value" id="n_value">
            <p>Количество бомб</p>
            <p>
                <input name="bombcount" type="radio" value="automine" id="stndmine" checked>Рассчитать автоматически</p>
            <p>
                <input name="bombcount" type="radio" value="mymine" id="mymine">Задать самостоятельно</p>
            <input type="number" min="1" max="99" step="1" size="20" name="m_value" id="m_value">
            <br>
            <p class="error_value" id="error_value"></p>
            <p>
                <input type="submit" class="button" value="Начать игру">
            </p>
        </form>
    </div>
    <div class="popup_window" id="finish_window">
        <p id="finish"></p>
        <!-- <input type="button" class="button" value="Закрыть"><input type="button" class="button" value="Лучшие результаты">
       -->
       <input type="submit" class="button" value="Закрыть" id="close_form" onclick="onSubmClose(event);">
    </div>
    <!--игровое поле 5*5-->
    <div class="game_field" id="game_field">
        <span class="header">
            <p>
                <span class="header-text">
                    Осталось мин:
                </span>
                <span id="mineleft" class="status">
                    3
                </span>
            </p>
            <p>
                <span class="header-text">
                    Время игры:
                </span>
                <span id="timer" class="status">
                    0
                </span>
            </p>
        </span>
        <table id="table">
        </table>
        <input type="submit" class="button field_button" value="Новая игра" id="new_game_start" onclick="onNewGame(event);">
        <input type="submit" class="button field_button" value="Рекорды" id="record_table" onclick="onRecord(event);">         
        <div class="instruction">
            <span class="instruction-bold">
                Чтобы открыть клетку поля,
            </span>
            <span class="instruction-text">
                нажмите левую кнопку мыши
            </span>
        </div>
        <div class="instruction">
            <span class="instruction-bold">
                Чтобы отметить клетку с бомбой,
            </span>
            <span class="instruction-text">
                нажмите правую кнопку мыши.
            </span>
        </div>
        <div class="instruction">
            <span class="instruction-bold">
                Чтобы отметить клетку знаком вопроса,
            </span>
            <span class="instruction-text">
                нажмите правую кнопку мыши повторно.
            </span>
        </div>
        <div class="instruction">
            <span class="instruction-bold">
                Для снятия отметки,
            </span>
            <span class="instruction-text">
                нажмите правую кнопку мыши.
            </span>
        </div>
        <!--<div class="buttons">
        <input type="button" class="button" value="Начать заново" id="new-game">
        </div>-->
        </div>

</body>
<?php
mysqli_close($link);
?>
</html>