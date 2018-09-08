<html>

<head>
    <link rel="stylesheet" href="style.css">
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
            <snap class="instruction-bold">
                Чтобы открыть клетку поля,
            </snap>
            <span class="instruction-text">
                нажмите левую кнопку мыши
            </span>
        </div>
        <div class="instruction">
            <snap class="instruction-bold">
                Чтобы отметить клетку с бомбой,
            </snap>
            <span class="instruction-text">
                нажмите правую кнопку мыши.
            </span>
        </div>
        <div class="instruction">
            <snap class="instruction-bold">
                Чтобы отметить клетку знаком вопроса,
            </snap>
            <span class="instruction-text">
                нажмите правую кнопку мыши повторно.
            </span>
        </div>
        <div class="instruction">
            <snap class="instruction-bold">
                Для снятия отметки,
            </snap>
            <span class="instruction-text">
                нажмите правую кнопку мыши.
            </span>
        </div>
        <!--<div class="buttons">
        <input type="button" class="button" value="Начать заново" id="new-game">
        </div>-->
        </div>
    <script type="text/javascript"> 
        //количество открытых ячеек для поверки, есть ли неоткрытые не мины
        var count_cell = 0;
        //открыта ли хоть одна мина
        var lose = 0;
        //n - размерность поля m - количество мин
        //создание массива и заполнение нулями
        var field = new Array();
        var n;
        var m;
        //время игры
        var time = 0;
        var ti;
        //разница между количеством мин и минами, помеченными игроком
        var mineleft;
        //создание массива-игрового поля
        function create_field() {
            mineleft = m;
            document.getElementById("mineleft").innerText = mineleft;
            for (var i = 0; i < n; i++) {
                field[i] = new Array();
                var rowid = "row" + i;
                var newrow = document.createElement('tr');
                newrow.id = rowid;
                table.appendChild(newrow);
                for (var j = 0; j < n; j++) {
                    field[i][j] = new Object();
                    //все клетки пусты
                    field[i][j].value = 0;
                    //все клетки закрыты для пользователя
                    field[i][j].player_see = "c";
                    //все клетки не помечены пользователем
                    field[i][j].mark = "n";
                    var cellid = "cell" + i + "_" + j;
                    var newcell = document.createElement('td');
                    newcell.innerText = "?";
                    newcell.id = cellid;
                    document.getElementById(rowid).appendChild(newcell);
                    newcell.onclick = onCellClick;
                    newcell.oncontextmenu = onRightClick;
                }
            }
            //расстановка мин
            var t = 0;
            while (t < m) {
                i = Math.random() * (n - 1);
                i = Math.round(i);
                j = Math.random() * (n - 1);
                j = Math.round(j);
                if (field[i][j].value == 0) { field[i][j].value = '*'; t++; }
            }
            var r = 0;
            //подсчет количества соседних мин для каждой из клеток поля
            for (var i = 0; i < n; i++) {
                for (var j = 0; j < n; j++) {
                    if (i > 0) { var i_min = i - 1 } else { var i_min = 0 };
                    if (i < n - 1) { var i_max = i + 1 } else { var i_max = n - 1 };
                    if (j > 0) { var j_min = j - 1 } else { var j_min = 0 };
                    if (j < n - 1) { var j_max = j + 1 } else { var j_max = n - 1 };
                    r = 0;
                    for (var i1 = i_min; i1 <= i_max; i1++) {
                        for (var j1 = j_min; j1 <= j_max; j1++) {
                            if (field[i1][j1].value == '*') r++;
                            if (field[i][j].value != '*') { field[i][j].value = r };
                        }
                    }
                }
            }
            ti = setInterval("timer()", 1000);
        }
        function timer() {
            time++;
            document.getElementById("timer").innerText = time;
        }
        //функция для дооткрытия массива после окончания игры
        function showfield() {
            for (var i = 0; i < n; i++) {
                for (var j = 0; j < n; j++) {
                    cellname = "cell" + i + "_" + j;
                    //открывает знаки вопроса
                    if (document.getElementById(cellname).innerText == "?") { document.getElementById(cellname).innerText = field[i][j].value };
                    //открывает восклицательные знаки
                    if (document.getElementById(cellname).innerText == "!") {
                        document.getElementById(cellname).innerText = field[i][j].value;
                        document.getElementById(cellname).style.color = "black";
                    };
                    //открывает неправильно отмеченные бомбы
                    if ((document.getElementById(cellname).innerText == "M") &
                        (field[i][j].value != "*")) {
                        document.getElementById(cellname).innerText = field[i][j].value;
                        document.getElementById(cellname).style.color = "red"
                    };
                }
            }
        }

        //открытие нулевой области
        function openNull(i, j, ide){
            if (typeof i === "object"){
                i = i[0];
            }
            if (typeof i === "string"){
                i = parseInt(i);
            }
            if (typeof j === "object"){
                j = j[0];
            }
            if (typeof j === "string"){
                j = parseInt(j);
            }                        
            console.log(i, j, ide);
            if (field[i][j].player_see == "c")
            {
                document.getElementById(ide).innerText = field[i][j].value;
                field[i][j].player_see = "o";
                field[i][j].mark = "n";
                if (field[i][j].value == "0")
                {
                    if (i-1 >= 0){
                        hlp = i-1;
                        ide = "cell" + hlp + "_" + j;
                        openNull(hlp, j, ide);
                       }
                    if (i+1 < n) { 
                        hlp2 = parseInt(i)+1;
                        ide = "cell" + hlp2 + "_" + j;
                        openNull(hlp2, j, ide); 
                    }
                    if (j-1 >= 0) {
                        hlp3 = j-1;
                        ide = "cell" + i + "_" + hlp3;
                        openNull(i, hlp3, ide);
                    }
                    if (j+1 < n) {
                        hlp4 = parseInt(j)+1;
                        ide = "cell" + i + "_" +hlp4;
                        openNull(i, hlp4, ide);
                    }
                }
            }
        }


        //обработка нажатия на ячейку
        function onCellClick(event) {
            //проверка, что мины еще не открыты
            if (lose != 1) {
                ide = event.target.id;
                var rt = /[^cell]/
                var ide2 = ide.match(rt);
                //разбор id элемента для определения i2 (координата по x)
                var i2r = /\d+/;
                var i2 = ide.match(i2r);
                //разбор id элемента для определения j2 (координата по y)
                var j2r = /\d+$/;
                var j2 = ide.match(j2r);
                //определение соответствующего координатам значения в массиве
                var val = field[i2][j2].value;
                //проверка, что данный элемент еще не был открыт
                if (document.getElementById(ide).innerText == '?') {
                    //если открыта мина, то игрок проиграл
                    if (field[i2][j2].value == '*') {
                        //alert('БАБАХ!!! Вы проиграли!');
                        document.getElementById("finish_window").style.visibility = "visible";
                        document.getElementById("finish").innerText = "БАБАХ!!! Вы проиграли!";
                        document.getElementById(ide).innerText = field[i2][j2].value;
                        document.getElementById(ide).style.backgroundColor = "red";
                        setTimeout(function () { clearInterval(ti); }, 1000);
                        lose = 1;
                        showfield();
                    }
                    else {
                        if (field[i2][j2].value == "0") {
                            openNull(i2, j2, ide);
                        } 
                        else {
                            //если игрок не проиграл, то открывается значение количества мин вокруг ячейки
                            document.getElementById(ide).innerText = field[i2][j2].value;
                            //увеличиваем счетчик открытых ячеек
                            count_cell++;
                            //если ячейки без мин закончились, то игрок выиграл
                            if (count_cell == n * n - m) {
                                //alert('Поздравляю, Вы выиграли!'); 
                                document.getElementById("finish_window").style.visibility = "visible";
                                document.getElementById("finish").innerText = "Поздравляю, Вы выиграли!";
                                setTimeout(function () { clearInterval(ti); }, 1000);
                                lose = 1; showfield();
                        }
                        }
                    }
                }
            }
        }

        function onSubmClose(event) {
            document.getElementById("finish_window").style.visibility = "hidden";
        }

        //функция чтения значения из формы
        function onSubm(event) {
            event.stopPropagation();
            event.preventDefault();
            n = document.forms["start_setting_form"].elements["n_value"].value;
            //Задание количества бомб - автоматически или от пользователя
            //Задаем рандомное количество мин 
            if (document.getElementById('stndmine').checked) {
                min_m = n * n / 10;
                max_m = n * n / 6;
                m = Math.random() * (max_m - min_m) + min_m;
                m = Math.round(m);
            }
            else {
            //Или берем колчество мин из формы
                m = document.forms["start_setting_form"].elements["m_value"].value;
            }
            if (n != 0) {
                //if (document.getElementById("mymine").checked) {
                if (m >= n * n) {
                    document.getElementById("error_value").innerText = "Количество бомб должно быть меньше " + n * n;
                    return false;
                }//}
                else {
                    create_field();
                    document.getElementById("start_setting").style.visibility = "hidden";
                    document.getElementById("game_field").style.visibility = "visible";
                }
            }
            else {
                document.getElementById("error_value").innerText = "Укажите размер игрового поля";
                return false;
            }
        }

        function onRightClick(event) {
            if (lose != 1) {
                ide = event.target.id;
                var rt = /[^cell]/
                var ide2 = ide.match(rt);
                //разбор id элемента для определения i2 (координата по x)
                var i2r = /\d{1}/;
                var i2 = ide.match(i2r);
                //разбор id элемента для определения j2 (координата по y)
                var j2r = /\d{1}$/;
                var j2 = ide.match(j2r);
                //определение соответствующего координатам значения в массиве
                var val = field[i2][j2].value;
                //если в клетке стоит ? заменяем его на метку мины M
                if (document.getElementById(ide).innerText == '?') {
                    // помечаем клетку как мину
                    document.getElementById(ide).innerText = "M";
                    document.getElementById(ide).style.color = "red";
                    // уменьшаем количество неотмеченных мин на 1
                    mineleft = mineleft - 1;
                    document.getElementById("mineleft").innerText = mineleft;

                } else {
                    //если на клетку поставлена мина, меняем ее на предупредительный знак
                    if (document.getElementById(ide).innerText == 'M') {
                        document.getElementById(ide).innerText = "!";
                        document.getElementById(ide).style.color = "blue";
                        //увеличиваем количество неотмеченных мин
                        mineleft = mineleft + 1;
                        document.getElementById("mineleft").innerText = mineleft;
                    }
                    else {
                        document.getElementById(ide).innerText = "?";
                        document.getElementById(ide).style.color = "black";
                    }
                }

            } return false;
        }
    </script>

</body>
<?php
mysqli_close($link);
?>
</html>