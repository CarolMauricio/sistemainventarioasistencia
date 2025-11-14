<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$initialStock = 0;
	$baseImageFolder = '../../data/item_images/';
	$itemImageFolder = '';
	
	if(isset($_POST['itemDetailsItemNumber'])){
		
		$itemNumber = htmlentities($_POST['itemDetailsItemNumber']);
		$itemName = htmlentities($_POST['itemDetailsItemName']);
		$discount = htmlentities($_POST['itemDetailsDiscount']);
		$quantity = htmlentities($_POST['itemDetailsQuantity']);
		$unitPrice = htmlentities($_POST['itemDetailsUnitPrice']);
		$status = htmlentities($_POST['itemDetailsStatus']);
		$description = htmlentities($_POST['itemDetailsDescription']);
		
		// Compruebe si los campos obligatorios no están vacíos
		if(!empty($itemNumber) && !empty($itemName) && isset($quantity) && isset($unitPrice)){
			
			// Desinfectar el número de elemento
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número.
			if(filter_var($quantity, FILTER_VALIDATE_INT) === 0 || filter_var($quantity, FILTER_VALIDATE_INT)){
				// Cantidad válida
			} else {
				// La cantidad no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número válido para cantidad</div>';
				exit();
			}
			
			// Validar precio unitario. Debe ser un número o un valor de punto flotante.
			if(filter_var($unitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($unitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// Precio unitario no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número válido par precio unitario</div>';
				exit();
			}
			
			// Validar el descuento solo si se proporciona
			if(!empty($discount)){
				if(filter_var($discount, FILTER_VALIDATE_FLOAT) === false){
					// El descuento no es un número de punto flotante válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un monto de descuento válido</div>';
					exit();
				}
			}
			
			// Crear carpeta de imágenes para subir imágenes
			$itemImageFolder = $baseImageFolder . $itemNumber;
			if(is_dir($itemImageFolder)){
				// La carpeta ya existe. Por lo tanto, no se realiza ninguna acción.
			} else {
				// La carpeta no existe. Por lo tanto, se crea.
				mkdir($itemImageFolder);
			}
			
			// Calcular los valores de existencia.
			$stockSql = 'SELECT stock FROM item WHERE itemNumber=:itemNumber';
			$stockStatement = $conn->prepare($stockSql);
			$stockStatement->execute(['itemNumber' => $itemNumber]);
			if($stockStatement->rowCount() > 0){
				//$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
				//$quantity = $quantity + $row['stock'];
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El artículo ya existe en la base de datos. Haga clic en el botón <strong>Actualizar</strong>. Para actualizar los detalles o utilizar un número de producto diferente.</div>';
				exit();
			} else {
				// El elemento no existe, por lo tanto, puede agregarlo a la base de datos como un elemento nuevo.
				// Iniciar el proceso de inserción.
				$insertItemSql = 'INSERT INTO item(itemNumber, itemName, discount, stock, unitPrice, status, description) VALUES(:itemNumber, :itemName, :discount, :stock, :unitPrice, :status, :description)';
				$insertItemStatement = $conn->prepare($insertItemSql);
				$insertItemStatement->execute(['itemNumber' => $itemNumber, 'itemName' => $itemName, 'discount' => $discount, 'stock' => $quantity, 'unitPrice' => $unitPrice, 'status' => $status, 'description' => $description]);
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Producto añadido a la base de datos.</div>';
				exit();
			}

		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se muestra un mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>