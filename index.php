<?php
//Стартуем сессии
session_start();
echo '
<html>
<head>
    <!--<meta charset="utf-8"> -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <link rel="stylesheet" href="content/styles/menu.css">
    <link rel="shortcut icon" href="content/images/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="menu">
<ul class="menu">
  <li class="menu"><a href="/geo/index.php">Поиск гео номеров</a></li>
  <li class="menu"><a href="/data_transformer/index.html">Преобразование данных</a></li>
  <li class="menu"><a href="/ali_orders/index.php">Поиск заказов алиэкспресс</a></li>
  <li class="menu"><a href="/sender/index.php">Переотправка данных</a></li>
</ul>
</div>
</body>
</html>
';
?>


