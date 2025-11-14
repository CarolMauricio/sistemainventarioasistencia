<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['itemImageItemNumber'])){
		
		$itemImageItemNumber = htmlentities($_POST['itemImageItemNumber']);
		
		$baseImageFolder = '../../data/item_images/';
		$itemImageFolder = '';
		
		if(!empty($itemImageItemNumber)){
			
			// Compruebe si el usuario ha seleccionado una imagen.
			if($_FILES['itemImageFile']['name'] != ''){
				// Se proporcionan el número de artículo y el archivo de imagen. Continúe con los siguientes pasos.
				
				// Desinfectar número de producto
				$itemImageItemNumber = filter_var($itemImageItemNumber, FILTER_SANITIZE_STRING);
				
				// Revisar si número de producto está en la base de datos
				$itemNumberSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
				$itemNumberStatement = $conn->prepare($itemNumberSql);
				$itemNumberStatement->execute(['itemNumber' => $itemImageItemNumber]);
				
				if($itemNumberStatement->rowCount() > 0){
					// El elemento está en la base de datos, por lo que se debe continuar con los siguientes pasos.
					// Verificar la extensión del archivo.
					$arr = explode('.', $_FILES['itemImageFile']['name']);
					$extension = strtolower(end($arr));
					$allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
					
					if(in_array($extension, $allowedTypes)){
						// Todo bien hasta ahora...
						
						$baseImageFolder = '../../data/item_images/';
						$itemImageFolder = '';
						$fileName = time() . '_' . basename($_FILES['itemImageFile']['name']);
						
						// Crear una carpeta de imágenes para cargar imágenes
						$itemImageFolder = $baseImageFolder . $itemImageItemNumber . '/';
						if(is_dir($itemImageFolder)){
							// La carpeta ya existe. Por lo tanto, no haga nada
						} else {
							// La carpeta no existe, por lo tanto, créela
							mkdir($itemImageFolder);
						}
						
						$targetPath = $itemImageFolder . $fileName;
						//echo $targetPath;
						//exit();
						
						// Cargar archivo al servidor
						if(move_uploaded_file($_FILES['itemImageFile']['tmp_name'], $targetPath)){
							
							// Actualizar la URL de la imagen en la tabla de elementos
							$updateImageUrlSql = 'UPDATE item SET imageURL = :imageURL WHERE itemNumber = :itemNumber';
							$updateImageUrlStatement = $conn->prepare($updateImageUrlSql);
							$updateImageUrlStatement->execute(['imageURL' => $fileName, 'itemNumber' => $itemImageItemNumber]);
							
							echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Imagen cargada exitosamente.</div>';
							exit();
							
						} else {
							echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No se pudo cargar la imagen.</div>';
							exit();
						}
						
					} else {
					// El tipo de imagen no está permitido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El tipo de imagen no está permitido. Por favor seleccione una imagen válida.</div>';
					exit();
					}
				}
				
			} else {
				// Archivo de imagen no proporcionado
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor seleccione una imagen</div>';
				exit();
			}
		
		} else {
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese el número de producto</div>';
			exit();
		}

	}

?>