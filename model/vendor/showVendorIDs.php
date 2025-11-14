<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprueba si se recibe la solicitud POST y, de ser así, ejecuta el script
	if(isset($_POST['textBoxValue'])){
		$output = '';
		$vendorIDString = '%' . htmlentities($_POST['textBoxValue']) . '%';
		
		// Construya la consulta SQL para obtener el nombre del elemento
		$sql = 'SELECT vendorID FROM vendor WHERE vendorID LIKE ?';
		$stmt = $conn->prepare($sql);
		$stmt->execute([$vendorIDString]);
		
		// Si recibimos algún resultado de la consulta anterior, lo mostramos en una lista.
		if($stmt->rowCount() > 0){
			
			// El ID del proveedor proporcionado está disponible en la base de datos. Por lo tanto, cree la lista desplegable.
			$output = '<ul class="list-unstyled suggestionsList" id="vendorDetailsVendorIDSuggestionsList">';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$output .= '<li>' . $row['vendorID'] . '</li>';
			}
			echo '</ul>';
		} else {
			$output = '';
		}
		$stmt->closeCursor();
		echo $output;
	}
?>