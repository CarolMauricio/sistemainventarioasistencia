<?php
	// Conexión a la base de datos
	try{
		$conn = new PDO(DSN, DB_USER, DB_PASSWORD);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e){
		$errorMessage = $e->getMessage();
		echo $errorMessage;
		exit();
	}
?>