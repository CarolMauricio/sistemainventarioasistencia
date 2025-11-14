<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['purchaseDetailsItemNumber'])){

		$purchaseDetailsItemNumber = htmlentities($_POST['purchaseDetailsItemNumber']);
		$purchaseDetailsPurchaseDate = htmlentities($_POST['purchaseDetailsPurchaseDate']);
		$purchaseDetailsItemName = htmlentities($_POST['purchaseDetailsItemName']);
		$purchaseDetailsQuantity = htmlentities($_POST['purchaseDetailsQuantity']);
		$purchaseDetailsUnitPrice = htmlentities($_POST['purchaseDetailsUnitPrice']);
		$purchaseDetailsVendorName = htmlentities($_POST['purchaseDetailsVendorName']);
		
		$initialStock = 0;
		$newStock = 0;
		
		// Revisar si los campos requeridos no están vacías
		if(isset($purchaseDetailsItemNumber) && isset($purchaseDetailsPurchaseDate) && isset($purchaseDetailsItemName) && isset($purchaseDetailsQuantity) && isset($purchaseDetailsUnitPrice)){
			
			// Compruebe si Número de Producto está vacío
			if($purchaseDetailsItemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese número de producto.</div>';
				exit();
			}
			
			// Compruebe si Número de Producto está vacío
			if($purchaseDetailsItemName == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese nombre de producto.</div>';
				exit();
			}
			
			// Compruebe si Cantidad está vacío
			if($purchaseDetailsQuantity == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese cantidad.</div>';
				exit();
			}
			
			// Compruebe si Precio Unitario está vacío
			if($purchaseDetailsUnitPrice == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese precio unitario.</div>';
				exit();
			}
			
			// Desinfectar Número de Producto
			$purchaseDetailsItemNumber = filter_var($purchaseDetailsItemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número entero.
			if(filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT)){
				// Cantidad válida
			} else {
				// La cantidad no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese un número válido para cantidad.</div>';
				exit();
			}
			
			// Validar el precio unitario. Debe ser un valor entero o de punto flotante.
			if(filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// El precio unitario no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para el precio unitario.</div>';
				exit();
			}
			
			// Verificar si el artículo existe en la tabla de productos y 
			//  Calcular los valores de existencia y actualizarlos para que coincidan con la nueva cantidad de compra.
			$stockSql = 'SELECT stock FROM item WHERE itemNumber=:itemNumber';
			$stockStatement = $conn->prepare($stockSql);
			$stockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
			if($stockStatement->rowCount() > 0){
				
				// Obtener el Proveedor Id para el Nombre de Proveedor dado
				$vendorIDsql = 'SELECT * FROM vendor WHERE fullName = :fullName';
				$vendorIDStatement = $conn->prepare($vendorIDsql);
				$vendorIDStatement->execute(['fullName' => $purchaseDetailsVendorName]);
				$row = $vendorIDStatement->fetch(PDO::FETCH_ASSOC);
				$vendorID = $row['vendorID'];
				
				// El artículo existe en la tabla de artículos, por lo tanto, comience a insertar datos en la tabla de compras
				$insertPurchaseSql = 'INSERT INTO purchase(itemNumber, purchaseDate, itemName, unitPrice, quantity, vendorName, vendorID) VALUES(:itemNumber, :purchaseDate, :itemName, :unitPrice, :quantity, :vendorName, :vendorID)';
				$insertPurchaseStatement = $conn->prepare($insertPurchaseSql);
				$insertPurchaseStatement->execute(['itemNumber' => $purchaseDetailsItemNumber, 'purchaseDate' => $purchaseDetailsPurchaseDate, 'itemName' => $purchaseDetailsItemName, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID]);
				
				// Calcular el nuevo valor de existencia utilizando la existencia en la tabla de productos
				$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
				$initialStock = $row['stock'];
				$newStock = $initialStock + $purchaseDetailsQuantity;
				
				// Actualizar el nuevo valor de existencia en la tabla de productos
				$updateStockSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
				$updateStockStatement = $conn->prepare($updateStockSql);
				$updateStockStatement->execute(['stock' => $newStock, 'itemNumber' => $purchaseDetailsItemNumber]);
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se agregaron detalles de compra a la base de datos y se actualizaron los valores de existencia.</div>';
				exit();
				
			} else {
				// El artículo no existe en la tabla de productos, por lo tanto, no se puede comprar.
				// Para agregarlo a la base de datos como una nueva compra.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El artículo no existe en la base de datos. Por lo tanto, primero introdúzcalo en la base de datos mediante la pestaña <strong>Producto</strong>.</div>';
				exit();
			}

		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se mostrará un mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>