<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Ejecutar el script si se envía la solicitud POST
	if(isset($_POST['saleDetailsSaleID'])){
		
		$saleID = htmlentities($_POST['saleDetailsSaleID']);
		
		$saleDetailsSql = 'SELECT * FROM sale WHERE saleID = :saleID';
		$saleDetailsStatement = $conn->prepare($saleDetailsSql);
		$saleDetailsStatement->execute(['saleID' => $saleID]);
		
		// Si se encuentran datos para el número de artículo dado, devuélvalos como un objeto json
		if($saleDetailsStatement->rowCount() > 0) {
			$row = $saleDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$saleDetailsStatement->closeCursor();
	}
?>