<?php
header('Content-Type: text/html; charset=utf-8');
include_once '../db_connect/db.php';
$Tables_in_my_db = $db->getAll("SHOW TABLES");
for ($i= 0; $i < count($Tables_in_my_db); $i++){
    foreach ($Tables_in_my_db[$i] as $key => $value) {
        if ($value == 'geo_table') $geo_table_exist = 1;
    }
}
if ($geo_table_exist == 1) include_once 'get_data.php';
else include_once 'insert_table.php';
echo '<script>var city_table ='. json_encode($table) .'</script>';
echo '
<html>
	<head>
		<title>Geo checker</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="array.js"></script>
		<script src="script.js"></script>
		<link rel="stylesheet" href="../content/styles/geo.css">
			<link rel="shortcut icon" href="../content/images/favicon.ico" type="image/x-icon">
			</head>
			<body>
				<div class=geo_menu>
					<table>
						<tr>
							<th>Введите гео:</th>
							<th>Двухбуквенные коды через запятую</th>
							<div class=input>
								<th>
									<input id="country_id" type="text" size="40">
									</th>
									<th>
										<input type="button" onclick="geo_submit()" value="Отправить">
											<input type="reset" onclick="geo_delete()" value="Очистить">
											</div>
										</th>
									</tr>
									<tr>
										<th></th>
										<th>или в формате англ. слов</th>
										<div class=input>
											<th>
												<input type="text" size="40"  id="country_name">
												</th>
												<th>
													<input type="button" onclick="geo_submit2()" value="Отправить">
														<input type="reset" onclick="geo_delete2()" value="Очистить">
														</div>
													</th>
												</tr>
												<th></th>
												<th>
													<span style="color: #aaffaa">NEW!!!</span> Города РФ:
												</th>
												<div class=input>
													<th>
														<input type="text" size="40"  id="city_name">
														</th>
														<th>
															<input type="button" onclick="geo_submit3()" value="Отправить">
																<input type="reset" onclick="geo_delete3()" value="Очистить">
																</div>
															</th>
														</tr>
													</table>
												</div>
												<div class=geo_menu>
													<form>
														<div id="finish">
															<p>Тут будут номера ГЕО</p>
														</div>
														<div id="error">
															<p>
																<b>А тут будет то что мы не смогли найти</b>
																<br>
																</p>
															</div>
														</form>
													</div>
													<p>
														<a href="../index.php">Назад</a>
													</p>
												</body>
											</html>
';
?>