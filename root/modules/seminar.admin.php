<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Семинар</title>
    <style>
   table { 
    width: 100%; /* Ширина таблицы */
    border: 4px double black; /* Рамка вокруг таблицы */
    border-collapse: collapse; /* Отображать только одинарные линии */
   }
   th { 
    text-align: left; /* Выравнивание по левому краю */
    padding: 5px; /* Поля вокруг содержимого ячеек */
    border: 1px solid black; /* Граница вокруг ячеек */
   }
   td { 
    padding: 5px; /* Поля вокруг содержимого ячеек */
    border: 1px solid black; /* Граница вокруг ячеек */
   }
  </style>
</head>
<body>

<?php
$db_name = 'atrienergo_event';
$db_user = 'atrienergo_event';
$db_pass = '5Q1z3X6o';
$db_host = 'localhost';

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die("Ошибка " . mysqli_error($link));	

$query ="SELECT * FROM JMY_invited";
$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));

echo '<table>';
echo '<tr>
<th>№</th>
<th>ФИО</th>
<th>Email</th>
<th>Телефон</th>
<th>Компания</th>
<th colspan="2">Мероприятия</th>
<th>Дата регистрации</th>
</tr>';
$i = 0;
while($r = mysqli_fetch_assoc($result)){
    $checked = json_decode($r["checked"], true);
    $i++;
    echo '<tr>
    <td rowspan="11">'.$i.'</td>
    <td rowspan="11">'.$r["name"].'</td>
    <td rowspan="11">'.$r["email"].'</td>
    <td rowspan="11">'.$r["phone"].'</td>
    <td rowspan="11">'.$r["company"].'</td>';
    echo '<td>1. Вводная часть</td><td>'.(($checked["check1"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td>';
    echo '<td rowspan="11">'.date("d.m.Y H:i:s", strtotime($r["date"])).'</td>
    </tr>';
    echo '<tr><td>2. Докладная часть ООО «Термотроник» - Оборудование производства Термотроник. Тепловычислители ТВ7М. Электромагнитные расходомеры Питерфлоу РС, СВ, К. Электронный регистратор АДИ</td><td>'.(($checked["check2"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>3. Докладная часть ООО «Атри-Энерго» - Проект за 30 минут</td><td>'.(($checked["check3"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>4. Обед</td><td>'.(($checked["check4"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>5. Докладная часть ООО «Атри-Энерго» - Атри-Онлайн</td><td>'.(($checked["check5"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>6. Докладная часть ООО «Вило Рус» - СП проектирования и оборудование WILO. Часть 1</td><td>'.(($checked["check6"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>7. Кофе-брейк</td><td>'.(($checked["check7"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>8. Докладная часть ООО «Вило Рус» - СП проектирования и оборудование WILO». Часть 2</td><td>'.(($checked["check8"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>9. Заключительная докладная часть ООО «Атри-Энерго»</td><td>'.(($checked["check9"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>10. Викторина. Розыгрыш призов. Награждение участников.</td><td>'.(($checked["check10"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
    echo '<tr><td>11. Фуршет</td><td>'.(($checked["check11"] == 'true') ? '<span style="color: green">Записан</span>' : '<span style="color: red">Не записан</span>').'</td></tr>';
}
echo '</table>';
?>

</body>
</html>