<?php

echo '
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="script.js"></script>
        <link rel="stylesheet" href="../content/styles/ali_orders.css">
        <link rel="shortcut icon" href="../content/images/favicon.ico" type="image/x-icon">
    </head>
    <body>


    <div class=form>
        <p>Введите order_id:</p>
        <textarea placeholder="Введите orders" name="data" id="order_input"
                  style="margin: 0px; height: 300px; width: 200px;" class="text"></textarea>
        <input type="submit" onclick="$.order_submit()" value="Запросить" name="send">
    </div>
<p><a href="../index.php">Назад</a></p>
    </body>
</html>
';
?>