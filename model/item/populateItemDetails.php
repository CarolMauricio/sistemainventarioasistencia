<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Ejecutar el script si se envía la solicitud POST
	if(isset($_POST['itemNumber'])){
		
		$itemNumber = htmlentities($_POST['itemNumber']);
		
		$itemDetailsSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
		$itemDetailsStatement = $conn->prepare($itemDetailsSql);
		$itemDetailsStatement->execute(['itemNumber' => $itemNumber]);
		
		// Si se encuentran datos para el número de artículo dado, devuélvalos como un objeto json
		if($itemDetailsStatement->rowCount() > 0) {
			$row = $itemDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$itemDetailsStatement->closeCursor();
	}
?>