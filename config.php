<?php
	$servername = "localhost";
	$dbname = "1clickdispute";

	if ($_SERVER["SERVER_NAME"] == "localhost" || strpos($_SERVER['SERVER_NAME'], 'ngrok.io') !== false) {
		$dbusername = "root";
		$dbpassword = "";
	} else {
		$dbusername = "senaid";
		$dbpassword = "S32nW7Sy^A_yygQ6";
	}
?>
