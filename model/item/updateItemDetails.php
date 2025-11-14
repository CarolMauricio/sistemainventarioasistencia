<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprobar si se recibe la consulta POST
	if(isset($_POST['itemNumber'])) {
		
		$itemNumber = htmlentities($_POST['itemNumber']);
		$itemName = htmlentities($_POST['itemDetailsItemName']);
		$discount = htmlentities($_POST['itemDetailsDiscount']);
		$itemDetailsQuantity = htmlentities($_POST['itemDetailsQuantity']);
		$itemDetailsUnitPrice = htmlentities($_POST['itemDetailsUnitPrice']);
		$status = htmlentities($_POST['itemDetailsStatus']);
		$description = htmlentities($_POST['itemDetailsDescription']);
		
		$initialStock = 0;
		$newStock = 0;
		
		// Compruebe si los campos obligatorios no están vacíos
		if(!empty($itemNumber) && !empty($itemName) && isset($itemDetailsQuantity) && isset($itemDetailsUnitPrice)){
			
			// Desinfecta el numero de elemento
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número.
			if(filter_var($itemDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($itemDetailsQuantity, FILTER_VALIDATE_INT)){
				// Cantidad válida
			} else {
				// Cantidad no es un número válido
				$errorAlert = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un numero válido para cantidad</div>';
				$data = ['alertMessage' => $errorAlert];
				echo json_encode($data);
				exit();
			}
			
			// Validar precio unitario. Debe ser un número o un valor de punto flotante.
			if(filter_var($itemDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($itemDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// Precio unitario no es un número válido
				$errorAlert = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número válido para precio unitario</div>';
				$data = ['alertMessage' => $errorAlert];
				echo json_encode($data);
				exit();
			}
			
			// Validar el descuento solo si se proporciona
			if(!empty($discount)){
				if(filter_var($discount, FILTER_VALIDATE_FLOAT) === false){
					// El descuento no es un número de punto flotante válido
					$errorAlert = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un monto de descuento válido</div>';
					$data = ['alertMessage' => $errorAlert];
					echo json_encode($data);
					exit();
				}
			}
			
			// Calcular las existencias
			$stockSelectSql = 'SELECT stock FROM item WHERE itemNumber = :itemNumber';
			$stockSelectStatement = $conn->prepare($stockSelectSql);
			$stockSelectStatement->execute(['itemNumber' => $itemNumber]);
			if($stockSelectStatement->rowCount() > 0) {
				$row = $stockSelectStatement->fetch(PDO::FETCH_ASSOC);
				$initialStock = $row['stock'];
				$newStock = $initialStock + $itemDetailsQuantity;
			} else {
				// El elemento no está en la base de datos. Por lo tanto, detenga la actualización y salga.
				$errorAlert = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El número de producto no existe en la base de datos. Por lo tanto, no es posible actualizarlo.</div>';
				$data = ['alertMessage' => $errorAlert];
				echo json_encode($data);
				exit();
			}
		
			// Construir la consulta UPDATE
			$updateItemDetailsSql = 'UPDATE item SET itemName = :itemName, discount = :discount, stock = :stock, unitPrice = :unitPrice, status = :status, description = :description WHERE itemNumber = :itemNumber';
			$updateItemDetailsStatement = $conn->prepare($updateItemDetailsSql);
			$updateItemDetailsStatement->execute(['itemName' => $itemName, 'discount' => $discount, 'stock' => $newStock, 'unitPrice' => $itemDetailsUnitPrice, 'status' => $status, 'description' => $description, 'itemNumber' => $itemNumber]);
			
			// UPDATE nombre de producto en la tabla de ventas
			$updateItemInSaleTableSql = 'UPDATE sale SET itemName = :itemName WHERE itemNumber = :itemNumber';
			$updateItemInSaleTableSstatement = $conn->prepare($updateItemInSaleTableSql);
			$updateItemInSaleTableSstatement->execute(['itemName' => $itemName, 'itemNumber' => $itemNumber]);
			
			// UPDATE nombre de producto en la tabla de compras
			$updateItemInPurchaseTableSql = 'UPDATE purchase SET itemName = :itemName WHERE itemNumber = :itemNumber';
			$updateItemInPurchaseTableSstatement = $conn->prepare($updateItemInPurchaseTableSql);
			$updateItemInPurchaseTableSstatement->execute(['itemName' => $itemName, 'itemNumber' => $itemNumber]);
			
			$successAlert = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalle de producto actualizado.</div>';
			$data = ['alertMessage' => $successAlert, 'newStock' => $newStock];
			echo json_encode($data);
			exit();
			
		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, muestre el mensaje de error.
			$errorAlert = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese todos los campos marcados con un (*)</div>';
			$data = ['alertMessage' => $errorAlert];
			echo json_encode($data);
			exit();
		}
	}
?>