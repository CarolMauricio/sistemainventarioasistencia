<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Ejecutar el script si se envía la solicitud POST
	if(isset($_POST['customerID'])){
		
		$customerID = htmlentities($_POST['customerID']);
		
		$customerDetailsSql = 'SELECT * FROM customer WHERE customerID = :customerID';
		$customerDetailsStatement = $conn->prepare($customerDetailsSql);
		$customerDetailsStatement->execute(['customerID' => $customerID]);
		
		// Si se encuentran datos para el número de artículo dado, devuélvalos como un objeto json
		if($customerDetailsStatement->rowCount() > 0) {
			$row = $customerDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$customerDetailsStatement->closeCursor();
	}
?>