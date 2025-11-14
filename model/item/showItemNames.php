<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprueba si se recibe la solicitud POST y, de ser así, ejecuta el script
	if(isset($_POST['textBoxValue'])){
		$output = '';
		$itemNameString = '%' . htmlentities($_POST['textBoxValue']) . '%';
		
		// Construya la consulta SQL para obtener el nombre del elemento
		$sql = 'SELECT itemName FROM item WHERE itemName LIKE ?';
		$stmt = $conn->prepare($sql);
		$stmt->execute([$itemNameString]);
		
		// Si recibimos algún resultado de la consulta anterior, lo mostramos en una lista
		if($stmt->rowCount() > 0){
			$output = '<ul class="list-unstyled suggestionsList" id="itemDetailsItemNamesSuggestionsList">';
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$output .= '<li>' . $row['itemName'] . '</li>';
			}
			echo '</ul>';
		} else {
			$output = '';
		}
		$stmt->closeCursor();
		echo $output;
	}
?>