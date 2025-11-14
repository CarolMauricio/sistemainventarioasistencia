<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['itemImageItemNumber'])){

			$itemImageItemNumber = htmlentities($_POST['itemImageItemNumber']);
			
			$baseImageFolder = '../../data/item_images/';
			$itemImageFolder = '';
			
			if(!empty($itemImageItemNumber)){
					
				// Desinfectar numero de producto
				$itemImageItemNumber = filter_var($itemImageItemNumber, FILTER_SANITIZE_STRING);
				
				// Revisar si NumeroProducto está en la base de datos
				$itemNumberSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
				$itemNumberStatement = $conn->prepare($itemNumberSql);
				$itemNumberStatement->execute(['itemNumber' => $itemImageItemNumber]);
				
				if($itemNumberStatement->rowCount() > 0){
					// Producto es en la base de datos, Por lo tanto, proceda a los siguientes pasos
					// Actualizar la URL de la imagen en la tabla de elementos a la imagen predeterminada
					$updateImageUrlSql = "UPDATE item SET imageURL = 'imageNotAvailable.jpg' WHERE itemNumber = :itemNumber";
					$updateImageUrlStatement = $conn->prepare($updateImageUrlSql);
					$updateImageUrlStatement->execute(['itemNumber' => $itemImageItemNumber]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Imagen eliminada exitosamente.</div>';
					exit();
				}
			
			} else {
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese el número de producto</div>';
				exit();
			}

	}

?>