<?php if (isset ($_POST['login']) and isset ($_POST['password']) ){
$login =$_POST['login'];
$password = $_POST['password'];
echo $login;
echo $password;
//Подключаемся к базе данных.
$new_connect= mysqli_connect("localhost", "root", "", "my_db");
// check connection
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $query = mysqli_query( $new_connect, "SELECT * FROM auth WHERE login= '$login' and password= '$password'");

    if (mysqli_num_rows($query) == 0) {
        $_SESSION["success_login"] = "no";
    } else {
        $_SESSION["success_login"] = "yes";
    }
    mysqli_close($new_connect);

    exit('<meta http-equiv="refresh" content="0; url=http://test/menu/index.php" />');
 } else {
       ?>
    <div class="auth">
    <form action="auth_form.php" method="post">
        <br/>
        Часик в радость!<br/>
        <label>логин:</label><br/>
        <input name="login" type="text" size="10" maxlength="10"><br/>
        <label>пароль:</label><br/>
        <input name="password" type="password" size="10" maxlength="20"><br/><br/>
        <input type="submit" value="Входи"><br/><br/>
        или
    </form>
    <input type="submit" value="Зарегайся" onclick="registration()">
    <br/>
    <br/>

<?php }?>