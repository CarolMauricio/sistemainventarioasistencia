<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprueba si se recibe la solicitud POST y, de ser así, ejecuta el script
	if(isset($_POST['textBoxValue'])){
		$output = '';
		$customerIDString = '%' . htmlentities($_POST['textBoxValue']) . '%';
		
		// Construya la consulta SQL para obtener el ID del cliente
		$sql = 'SELECT customerID FROM customer WHERE customerID LIKE ?';
		$stmt = $conn->prepare($sql);
		$stmt->execute([$customerIDString]);
		
		// Si recibimos algún resultado de la consulta anterior, lo mostramos en una lista.
		if($stmt->rowCount() > 0){
			
			// El ID del cliente está disponible en la base de datos. Por lo tanto, cree la lista desplegable.
			$output = '<ul class="list-unstyled suggestionsList" id="customerDetailsCustomerIDSuggestionsList">';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$output .= '<li>' . $row['customerID'] . '</li>';
			}
			echo '</ul>';
		} else {
			$output = '';
		}
		$stmt->closeCursor();
		echo $output;
	}
?>