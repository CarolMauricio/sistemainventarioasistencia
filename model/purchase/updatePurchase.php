<?php

// Updated script - 2025-10-01

	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['purchaseDetailsPurchaseID'])){

		$purchaseDetailsItemNumber = htmlentities($_POST['purchaseDetailsItemNumber']);
		$purchaseDetailsPurchaseDate = htmlentities($_POST['purchaseDetailsPurchaseDate']);
		$purchaseDetailsItemName = htmlentities($_POST['purchaseDetailsItemName']);
		$purchaseDetailsQuantity = htmlentities($_POST['purchaseDetailsQuantity']);
		$purchaseDetailsUnitPrice = htmlentities($_POST['purchaseDetailsUnitPrice']);
		$purchaseDetailsPurchaseID = htmlentities($_POST['purchaseDetailsPurchaseID']);
		$purchaseDetailsVendorName = htmlentities($_POST['purchaseDetailsVendorName']);
		
		$quantityInOriginalOrder = 0;
		$quantityInNewOrder = 0;
		$originalStockInItemTable = 0;
		$newStock = 0;
		$originalPurchaseItemNumber = '';
		
		// Compruebe si los campos obligatorios no están vacíos
		if(isset($purchaseDetailsItemNumber) && isset($purchaseDetailsPurchaseDate) && isset($purchaseDetailsQuantity) && isset($purchaseDetailsUnitPrice)){
			
			// Desinfectar número de producto
			$purchaseDetailsItemNumber = filter_var($purchaseDetailsItemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número entero.
			if(filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT)){
				// La cantidad es válida
			} else {
				// La cantidad no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número válido para cantidad.</div>';
				exit();
			}
			
			// Validar el precio unitario. Debe ser un valor entero o de punto flotante.
			if(filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// El precio unitario no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número válido para precio unitario.</div>';
				exit();
			}
			
			// Compruebe si Compra ID está vacío
			if($purchaseDetailsPurchaseID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese Compra ID.</div>';
				exit();
			}
			
			// Compruebe si Número de Producto está vacío
			if($purchaseDetailsItemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese número de producto.</div>';
				exit();
			}
			
			// Compruebe si Cantidad está vacío
			if($purchaseDetailsQuantity == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese cantidad.</div>';
				exit();
			}
			
			// Obtenga la cantidad y el número de artículo en la orden de compra original
			$orginalPurchaseQuantitySql = 'SELECT * FROM purchase WHERE purchaseID = :purchaseID';
			$originalPurchaseQuantityStatement = $conn->prepare($orginalPurchaseQuantitySql);
			$originalPurchaseQuantityStatement->execute(['purchaseID' => $purchaseDetailsPurchaseID]);
			
			// Obtenga el Proveedor ID para el dato Nombre de Proveedor
			$vendorIDsql = 'SELECT * FROM vendor WHERE fullName = :fullName';
			$vendorIDStatement = $conn->prepare($vendorIDsql);
			$vendorIDStatement->execute(['fullName' => $purchaseDetailsVendorName]);
			$row = $vendorIDStatement->fetch(PDO::FETCH_ASSOC);
			$vendorID = $row['vendorID'];
			
			if($originalPurchaseQuantityStatement->rowCount() > 0){
				
				// Los detalles de la compra existen en la base de datos. Por lo tanto, proceda a calcular las existencias.
				$originalQtyRow = $originalPurchaseQuantityStatement->fetch(PDO::FETCH_ASSOC);
				$quantityInOriginalOrder = $originalQtyRow['quantity'];
				$originalOrderItemNumber = $originalQtyRow['itemNumber'];

				// Verificar si el usuario también desea actualizar el número de artículo. En ese caso,
				// debemos eliminar la cantidad del pedido original para ese artículo y
				// actualizar los detalles del nuevo artículo en la tabla de artículos.
				// Verificar si el número de artículo original coincide con el nuevo.
				if($originalOrderItemNumber !== $purchaseDetailsItemNumber) {
					// Los números de artículo son diferentes. Esto significa que el usuario también desea actualizar un nuevo número de artículo.
					// En ese caso, es necesario actualizar el stock de ambos artículos.

					// Obtener el stock del nuevo artículo de la tabla de artículos.
					$newItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
					$newItemCurrentStockStatement = $conn->prepare($newItemCurrentStockSql);
					$newItemCurrentStockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
					
					if($newItemCurrentStockStatement->rowCount() < 1){
						// El número de artículo no está en la base de datos. Por lo tanto, se cancela.
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El número de producto no existe en la base de datos. Si desea actualizar este artículo, agréguelo primero a la base de datos.</div>';
						exit();
					}
					
					// Calcular el nuevo valor de existencia para un artículo nuevo utilizando el stock existente en la tabla de productos
					$newItemRow = $newItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$originalQuantityForNewItem = $newItemRow['stock'];
					$enteredQuantityForNewItem = $purchaseDetailsQuantity;
					$newItemNewStock = $originalQuantityForNewItem + $enteredQuantityForNewItem;
					
					// UPDATE la existencia del nuevo producto en la tabla de productos
					$newItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$newItemStockUpdateStatement = $conn->prepare($newItemStockUpdateSql);
					$newItemStockUpdateStatement->execute(['stock' => $newItemNewStock, 'itemNumber' => $purchaseDetailsItemNumber]);
					
					// Obtener las existencias del producto anterior
					$previousItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$previousItemCurrentStockStatement = $conn->prepare($previousItemCurrentStockSql);
					$previousItemCurrentStockStatement->execute(['itemNumber' => $originalOrderItemNumber]);
					
					// Calcular el nuevo valor de existencia para el producto anterior utilizando la existencia en la tabla de productos
					$previousItemRow = $previousItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$currentQuantityForPreviousItem = $previousItemRow['stock'];
					$previousItemNewStock = $currentQuantityForPreviousItem - $quantityInOriginalOrder;
					
					// UPDATE la existencia del producto anterior en la tabla de productos
					$previousItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$previousItemStockUpdateStatement = $conn->prepare($previousItemStockUpdateSql);
					$previousItemStockUpdateStatement->execute(['stock' => $previousItemNewStock, 'itemNumber' => $originalOrderItemNumber]);
					
					// Finalmente UPDATE la tabla de compra para el nuevo producto
					$updatePurchaseDetailsSql = 'UPDATE purchase SET itemNumber = :itemNumber, purchaseDate = :purchaseDate, itemName = :itemName, unitPrice = :unitPrice, quantity = :quantity, vendorName = :vendorName, vendorID = :vendorID WHERE purchaseID = :purchaseID';
					$updatePurchaseDetailsStatement = $conn->prepare($updatePurchaseDetailsSql);
					$updatePurchaseDetailsStatement->execute(['itemNumber' => $purchaseDetailsItemNumber, 'purchaseDate' => $purchaseDetailsPurchaseDate, 'itemName' => $purchaseDetailsItemName, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID, 'purchaseID' => $purchaseDetailsPurchaseID]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se agregaron detalles de compra a la base de datos y se actualizaron los valores de existencia.</div>';
					exit();
					
				} else {
					// Los números de artículo son iguales. Esto significa que el número de artículo es válido.

					// Obtener la cantidad (existencias) en la tabla de artículos.
					$stockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$stockStatement = $conn->prepare($stockSql);
					$stockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
					
					if($stockStatement->rowCount() > 0){
						// El artículo existe en la tabla de artículos, por lo tanto, comience a insertar datos en la tabla de compras.

						// Calcule el nuevo valor de stock utilizando el stock existente en la tabla de artículos.
						$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
						$quantityInNewOrder = $purchaseDetailsQuantity;
						$originalStockInItemTable = $row['stock'];
						$newStock = $originalStockInItemTable + ($quantityInNewOrder - $quantityInOriginalOrder);
						
						//Actualizar el nuevo valor de stock en la tabla de artículos.
						$updateStockSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
						$updateStockStatement = $conn->prepare($updateStockSql);
						$updateStockStatement->execute(['stock' => $newStock, 'itemNumber' => $purchaseDetailsItemNumber]);
						
						// A continuación, actualice la tabla de compras
						$updatePurchaseDetailsSql = 'UPDATE purchase SET purchaseDate = :purchaseDate, unitPrice = :unitPrice, quantity = :quantity, vendorName = :vendorName, vendorID = :vendorID WHERE purchaseID = :purchaseID';
						$updatePurchaseDetailsStatement = $conn->prepare($updatePurchaseDetailsSql);
						$updatePurchaseDetailsStatement->execute(['purchaseDate' => $purchaseDetailsPurchaseDate, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID, 'purchaseID' => $purchaseDetailsPurchaseID]);
						
						echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se agregaron detalles de compra a la base de datos y se actualizaron los valores de existencia.</div>';
						exit();
						
					} else {
						// El artículo no existe en la tabla de artículos, por lo tanto, no se pueden 
						// actualizar los detalles de compra. 
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El artículo no existe en la base de datos. Por lo tanto, primero introdúzcalo en la base de datos mediante la pestaña de <strong>producto</strong>.</div>';
						exit();
					}	
					
				}
	
			} else {
				
				// Compra ID no existe en la tabla de compras, por lo tanto, no puedes actualizarlo
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Los detalles de compra no existen en la base de datos para Compra ID indicado. Por lo tanto, no se pueden actualizar.</div>';
				exit();
				
			}

		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se mostrará un mensaje de error
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>