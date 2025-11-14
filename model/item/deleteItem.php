<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$itemNumber = htmlentities($_POST['itemDetailsItemNumber']);
	
	if(isset($_POST['itemDetailsItemNumber'])){
		
		// Compruebe si los campos obligatorios no están vacíos
		if(!empty($itemNumber)){
			
			// Desinfectar el número de artículo
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);

			// Comprobar si el artículo está en la base de datos
			$itemSql = 'SELECT itemNumber FROM item WHERE itemNumber=:itemNumber';
			$itemStatement = $conn->prepare($itemSql);
			$itemStatement->execute(['itemNumber' => $itemNumber]);
			
			if($itemStatement->rowCount() > 0){
				
				// El elemento existe en la base de datos. Por lo tanto, se inicia el proceso DELETE.
				$deleteItemSql = 'DELETE FROM item WHERE itemNumber=:itemNumber';
				$deleteItemStatement = $conn->prepare($deleteItemSql);
				$deleteItemStatement->execute(['itemNumber' => $itemNumber]);

				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Producto eliminado.</div>';
				exit();
				
			} else {
				// El elemento no existe, por lo tanto, dígale al usuario que no puede eliminar ese elemento
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El elemento no existe en la base de datos. Por lo tanto, no se puede eliminar.</div>';
				exit();
			}
			
		} else {
			// El número de artículo está vacío. Por lo tanto, se muestra el mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese el número de producto</div>';
			exit();
		}
	}
?>