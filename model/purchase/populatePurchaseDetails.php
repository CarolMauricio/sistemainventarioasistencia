<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Ejecutar el script si se envía la solicitud POST
	if(isset($_POST['purchaseDetailsPurchaseID'])){
		
		$purchaseID = htmlentities($_POST['purchaseDetailsPurchaseID']);
		
		$purchaseDetailsSql = 'SELECT * FROM purchase WHERE purchaseID = :purchaseID';
		$purchaseDetailsStatement = $conn->prepare($purchaseDetailsSql);
		$purchaseDetailsStatement->execute(['purchaseID' => $purchaseID]);
		
		// Si se encuentran datos para el número de artículo dado, devuélvalos como un objeto json
		if($purchaseDetailsStatement->rowCount() > 0) {
			$row = $purchaseDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$purchaseDetailsStatement->closeCursor();
	}
?>