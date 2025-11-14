<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprueba si se recibe la solicitud POST y, de ser así, ejecuta el script
	if(isset($_POST['textBoxValue'])){
		$output = '';
		$purchaseIDString = '%' . htmlentities($_POST['textBoxValue']) . '%';
		
		// Construya la consulta SQL para obtener el nombre del elemento
		$sql = 'SELECT purchaseID FROM purchase WHERE purchaseID LIKE ?';
		$stmt = $conn->prepare($sql);
		$stmt->execute([$purchaseIDString]);
		
		// Si recibimos un resultado de la consulta anterior, entonces los mostrará en una lista
		if($stmt->rowCount() > 0){
			
			// Compra ID proporcionado está disponible en la base de datos. Por lo tanto crea una lista desplegable 
			$output = '<ul class="list-unstyled suggestionsList" id="purchaseDetailsPurchaseIDSuggestionsList">';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$output .= '<li>' . $row['purchaseID'] . '</li>';
			}
			echo '</ul>';
		} else {
			$output = '';
		}
		$stmt->closeCursor();
		echo $output;
	}
?>