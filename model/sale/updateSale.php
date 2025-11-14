<?php

	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['saleDetailsSaleID'])){

		$saleDetailsItemNumber = htmlentities($_POST['saleDetailsItemNumber']);
		$saleDetailsSaleDate = htmlentities($_POST['saleDetailsSaleDate']);
		$saleDetailsItemName = htmlentities($_POST['saleDetailsItemName']);
		$saleDetailsQuantity = htmlentities($_POST['saleDetailsQuantity']);
		$saleDetailsUnitPrice = htmlentities($_POST['saleDetailsUnitPrice']);
		$saleDetailsSaleID = htmlentities($_POST['saleDetailsSaleID']);
		$saleDetailsCustomerName = htmlentities($_POST['saleDetailsCustomerName']);
		$saleDetailsDiscount = htmlentities($_POST['saleDetailsDiscount']);
		$saleDetailsCustomerID = htmlentities($_POST['saleDetailsCustomerID']);
		
		$quantityInOriginalOrder = 0;
		$quantityInNewOrder = 0;
		$originalStockInItemTable = 0;
		$newStock = 0;
		
		// Compruebe si los campos obligatorios no están vacíos
		if(isset($saleDetailsItemNumber) && isset($saleDetailsSaleDate) && isset($saleDetailsQuantity) && isset($saleDetailsUnitPrice) && isset($saleDetailsCustomerID)){
			
			// Desinfectar número de producto
			$saleDetailsItemNumber = filter_var($saleDetailsItemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número entero.
			if(filter_var($saleDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($saleDetailsQuantity, FILTER_VALIDATE_INT)){
				// La cantidad es válida
			} else {
				// La cantidad no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para Cantidad.</div>';
				exit();
			}
			
			// Validar el precio unitario. Debe ser un valor entero o de punto flotante.
			if(filter_var($saleDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($saleDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// El precio unitario no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para el precio unitario.</div>';
				exit();
			}
			
			// Validar descuento
			if($saleDetailsDiscount !== ''){
				if(filter_var($saleDetailsDiscount, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($saleDetailsDiscount, FILTER_VALIDATE_FLOAT)){
				// Descuento válido
				} else {
					// El descuento no es un número válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para el descuento.</div>';
					exit();
				}
			}
			
			// Compruebe si Venta ID está vacío
			if($saleDetailsSaleID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese Venta ID.</div>';
				exit();
			}
			
			// Compruebe si Cliente ID está vacío
			if($saleDetailsCustomerID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese Cliente ID.</div>';
				exit();
			}
			
			// Compruebe si número de oproducto está vacío
			if($saleDetailsItemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese número de producto.</div>';
				exit();
			}
			
			// Compruebe si cantidad está vacío
			if($saleDetailsQuantity == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese cantidad.</div>';
				exit();
			}
			
			// Compruebe si precio unitario está vacío
			if($saleDetailsUnitPrice == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese precio unitario.</div>';
				exit();
			}
			
			// Obtener la cantidad y el número de artículo en el pedido de venta original
			$orginalSaleQuantitySql = 'SELECT * FROM sale WHERE saleID = :saleID';
			$originalSaleQuantityStatement = $conn->prepare($orginalSaleQuantitySql);
			$originalSaleQuantityStatement->execute(['saleID' => $saleDetailsSaleID]);
			
			// Obtener el ID de cliente para el nombre de cliente dado
			/* $customerIDsql = 'SELECT * FROM customer WHERE fullName = :fullName';
			$customerIDStatement = $conn->prepare($customerIDsql);
			$customerIDStatement->execute(['fullName' => $saleDetailsCustomerName]);
			$row = $customerIDStatement->fetch(PDO::FETCH_ASSOC);
			$customerID = $row['customerID']; */
			
			$customerIDsql = 'SELECT * FROM customer WHERE customerID = :customerID';
			$customerIDStatement = $conn->prepare($customerIDsql);
			$customerIDStatement->execute(['customerID' => $saleDetailsCustomerID]);
			
			if($customerIDStatement->rowCount() < 1){
				// Cliente id es incorrecto
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Cliente ID no existe en la base de datos. Por favor, introduzca un valor válido de Cliente ID.</div>';
				exit();
			} else {
				$row = $customerIDStatement->fetch(PDO::FETCH_ASSOC);
				$customerID = $row['customerID'];
				$saleDetailsCustomerName = $row['fullName'];
			}
			
			if($originalSaleQuantityStatement->rowCount() > 0){
				
				// Los detalles de la venta existen en la base de datos. Por lo tanto, proceda a calcular el stock.
				$originalQtyRow = $originalSaleQuantityStatement->fetch(PDO::FETCH_ASSOC);
				$quantityInOriginalOrder = $originalQtyRow['quantity'];
				$originalOrderItemNumber = $originalQtyRow['itemNumber'];

				// Verificar si el usuario también desea actualizar el número de artículo. En ese caso,
				// debemos eliminar la cantidad del pedido original para ese artículo y
				// actualizar los detalles del nuevo artículo en la tabla de artículos.
				// Verificar si el número de artículo original coincide con el nuevo.
				if($originalOrderItemNumber !== $saleDetailsItemNumber) {
					// Los números de artículo son diferentes. Esto significa que el usuario también desea actualizar un nuevo número de artículo.
					// En ese caso, es necesario actualizar el stock de ambos artículos.

					// Obtener el stock del nuevo artículo de la tabla de artículos.
					$newItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
					$newItemCurrentStockStatement = $conn->prepare($newItemCurrentStockSql);
					$newItemCurrentStockStatement->execute(['itemNumber' => $saleDetailsItemNumber]);
					
					if($newItemCurrentStockStatement->rowCount() < 1){
						// El número de artículo no está en la base de datos. Por lo tanto, se cancela.
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El número de producto no existe en la base de datos. Si desea actualizar este producto, agréguelo primero a la base de datos.</div>';
						exit();
					}
					
					// Calcular el nuevo valor de stock para un artículo nuevo utilizando el stock existente en la tabla de artículos
					$newItemRow = $newItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$originalQuantityForNewItem = $newItemRow['stock'];
					$enteredQuantityForNewItem = $saleDetailsQuantity;
					$newItemNewStock = $originalQuantityForNewItem - $enteredQuantityForNewItem;
					
					// UPDATE el stock del nuevo artículo en la tabla de artículos
					$newItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$newItemStockUpdateStatement = $conn->prepare($newItemStockUpdateSql);
					$newItemStockUpdateStatement->execute(['stock' => $newItemNewStock, 'itemNumber' => $saleDetailsItemNumber]);
					
					// Obtener el stock actual del artículo anterior
					$previousItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$previousItemCurrentStockStatement = $conn->prepare($previousItemCurrentStockSql);
					$previousItemCurrentStockStatement->execute(['itemNumber' => $originalOrderItemNumber]);
					
					// Calcular el nuevo valor de stock para el artículo anterior utilizando el stock existente en la tabla de artículos
					$previousItemRow = $previousItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$currentQuantityForPreviousItem = $previousItemRow['stock'];
					$previousItemNewStock = $currentQuantityForPreviousItem + $quantityInOriginalOrder;
					
					// UPDATE el stock del artículo anterior en la tabla de productos
					$previousItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$previousItemStockUpdateStatement = $conn->prepare($previousItemStockUpdateSql);
					$previousItemStockUpdateStatement->execute(['stock' => $previousItemNewStock, 'itemNumber' => $originalOrderItemNumber]);
					
					// Finalmente UPDATE la tabla de venta para el nuevo producto
					$updateSaleDetailsSql = 'UPDATE sale SET itemNumber = :itemNumber, saleDate = :saleDate, itemName = :itemName, unitPrice = :unitPrice, discount = :discount, quantity = :quantity, customerName = :customerName, customerID = :customerID WHERE saleID = :saleID';
					$updateSaleDetailsStatement = $conn->prepare($updateSaleDetailsSql);
					$updateSaleDetailsStatement->execute(['itemNumber' => $saleDetailsItemNumber, 'saleDate' => $saleDetailsSaleDate, 'itemName' => $saleDetailsItemName, 'unitPrice' => $saleDetailsUnitPrice, 'discount' => $saleDetailsDiscount, 'quantity' => $saleDetailsQuantity, 'customerName' => $saleDetailsCustomerName, 'customerID' => $customerID, 'saleID' => $saleDetailsSaleID]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalles de venta actualizados.</div>';
					exit();
					
				} else {
					// Los números de artículo son iguales. Esto significa que el número de artículo es válido.
					
					// Obtener la cantidad (stock) en la tabla de artículos
					$stockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$stockStatement = $conn->prepare($stockSql);
					$stockStatement->execute(['itemNumber' => $saleDetailsItemNumber]);
					
					if($stockStatement->rowCount() > 0){
						// El artículo ya existe en la tabla de artículos, por lo tanto, se actualizan los datos en la tabla de ventas.

						// Calcular el nuevo valor de stock utilizando el stock existente en la tabla de artículos.
						$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
						$quantityInNewOrder = $saleDetailsQuantity;
						$originalStockInItemTable = $row['stock'];
						$newStock = $originalStockInItemTable - ($quantityInNewOrder - $quantityInOriginalOrder);
						
						// Actualice el nuevo valor de stock en la tabla de artículos.
						$updateStockSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
						$updateStockStatement = $conn->prepare($updateStockSql);
						$updateStockStatement->execute(['stock' => $newStock, 'itemNumber' => $saleDetailsItemNumber]);
						
						// A continuación, actualice la tabla de ventas.
						$updateSaleDetailsSql = 'UPDATE sale SET itemNumber = :itemNumber, saleDate = :saleDate, itemName = :itemName, unitPrice = :unitPrice, discount = :discount, quantity = :quantity, customerName = :customerName, customerID = :customerID WHERE saleID = :saleID';
						$updateSaleDetailsStatement = $conn->prepare($updateSaleDetailsSql);
						$updateSaleDetailsStatement->execute(['itemNumber' => $saleDetailsItemNumber, 'saleDate' => $saleDetailsSaleDate, 'itemName' => $saleDetailsItemName, 'unitPrice' => $saleDetailsUnitPrice, 'discount' => $saleDetailsDiscount, 'quantity' => $saleDetailsQuantity, 'customerName' => $saleDetailsCustomerName, 'customerID' => $customerID, 'saleID' => $saleDetailsSaleID]);
						
						echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalles de venta actualizados.</div>';
						exit();
						
					} else {
						// El artículo no productos en la tabla de productos, por lo tanto, no se pueden actualizar los detalles de venta. 
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El producto no existe en la base de datos. Por lo tanto, primero introdúzcalo en la base de datos mediante la pestaña <strong>Artículo</strong>.</div>';
						exit();
					}	
					
				}
	
			} else {
				
				// Venta ID no existe en la tabla de compras, por lo tanto no puedes actualizarlo
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Los detalles de la venta no existen en la base de datos para el ID de venta indicado. Por lo tanto, no se pueden actualizar.</div>';
				exit();
				
			}

		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se mostrará un mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>