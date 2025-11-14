<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprueba si se recibe la solicitud POST y, de ser así, ejecuta el script
	if(isset($_POST['textBoxValue'])){
		$output = '';
		$itemNumberString = '%' . htmlentities($_POST['textBoxValue']) . '%';
		
		// Construya la consulta SQL para obtener el nombre del elemento
		$sql = 'SELECT itemNumber FROM item WHERE itemNumber LIKE ?';
		$stmt = $conn->prepare($sql);
		$stmt->execute([$itemNumberString]);
		
		// Si recibimos algún resultado de la consulta anterior, lo mostramos en una lista.
		if($stmt->rowCount() > 0){
			$output = '<ul class="list-unstyled suggestionsList" id="itemImageItemNumberSuggestionsList">';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$output .= '<li>' . $row['itemNumber'] . '</li>';
			}
			echo '</ul>';
		} else {
			$output = '';
		}
		$stmt->closeCursor();
		echo $output;
	}
?>