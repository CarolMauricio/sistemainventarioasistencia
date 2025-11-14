<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['saleDetailsItemNumber'])){
		
		$itemNumber = htmlentities($_POST['saleDetailsItemNumber']);
		$itemName = htmlentities($_POST['saleDetailsItemName']);
		$discount = htmlentities($_POST['saleDetailsDiscount']);
		$quantity = htmlentities($_POST['saleDetailsQuantity']);
		$unitPrice = htmlentities($_POST['saleDetailsUnitPrice']);
		$customerID = htmlentities($_POST['saleDetailsCustomerID']);
		$customerName = htmlentities($_POST['saleDetailsCustomerName']);
		$saleDate = htmlentities($_POST['saleDetailsSaleDate']);
		
		// Check if mandatory fields are not empty
		if(!empty($itemNumber) && isset($customerID) && isset($saleDate) && isset($quantity) && isset($unitPrice)){
			
			// Desinfectar número de producto
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);
			
			// Validar la cantidad del artículo. Debe ser un número.
			if(filter_var($quantity, FILTER_VALIDATE_INT) === 0 || filter_var($quantity, FILTER_VALIDATE_INT)){
				// Cantidad válida
			} else {
				// La cantidad no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número válido para la cantidad</div>';
				exit();
			}
			
			// Compruebe si Cliente ID está vacío
			if($customerID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese Customer ID.</div>';
				exit();
			}
			
			// Validar Cliente ID
			if(filter_var($customerID, FILTER_VALIDATE_INT) === 0 || filter_var($customerID, FILTER_VALIDATE_INT)){
				// Cliente ID válido
			} else {
				// Cliente ID no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese Customer ID válido</div>';
				exit();
			}
			
			// Compruebe si número de producto está vacía
			if($itemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese número de producto.</div>';
				exit();
			}
			
			// Comprobar si el precio unitario está vacío
			if($unitPrice == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese precio unitario.</div>';
				exit();
			}
			
			// Validar precio unitario. Debe ser un número o un valor de punto flotante.
			if(filter_var($unitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($unitPrice, FILTER_VALIDATE_FLOAT)){
				// Precio unitario válido
			} else {
				// El precio unitario no es un número válido
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para el precio unitario</div>';
				exit();
			}
			
			// Validar el descuento solo si se proporciona
			if(!empty($discount)){
				if(filter_var($discount, FILTER_VALIDATE_FLOAT) === false){
					// El descuento no es un número de punto flotante válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un monto de descuento válido</div>';
					exit();
				}
			}

			// Calcular los valores de las acciones
			$stockSql = 'SELECT stock FROM item WHERE itemNumber = :itemNumber';
			$stockStatement = $conn->prepare($stockSql);
			$stockStatement->execute(['itemNumber' => $itemNumber]);
			if($stockStatement->rowCount() > 0){
				// El artículo existe en la base de datos, por lo tanto, se puede proceder a una venta.
				$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
				$currentQuantityInItemsTable = $row['stock'];
				
				if($currentQuantityInItemsTable <= 0) {
					// Si currentQuantityInItemsTable es <= 0, ¡el stock está vacío! Esto significa que no podemos vender. Por lo tanto, cancelamos la venta.
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>La existencias están agotadas. Por lo tanto, no se puede vender. Seleccione otro producto.</div>';
					exit();
				} elseif ($currentQuantityInItemsTable < $quantity) {
					// La cantidad de venta solicitada es mayor que la cantidad de artículos disponibles. Por lo tanto, cancelar. 
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>No hay suficientes existencias para esta venta. Por lo tanto, no se puede realizar. Seleccione otro producto.</div>';
					exit();
				}
				else {
					// Tiene al menos 1 o más en stock, por lo tanto, proceda a los siguientes pasos
					$newQuantity = $currentQuantityInItemsTable - $quantity;
					
					// Compruebe si la cliente está en DB
					$customerSql = 'SELECT * FROM customer WHERE customerID = :customerID';
					$customerStatement = $conn->prepare($customerSql);
					$customerStatement->execute(['customerID' => $customerID]);
					
					if($customerStatement->rowCount() > 0){
						// El cliente sale. Esto significa que tanto el cliente como el artículo y las existencias están disponibles. Por lo tanto, empieza INSERT y UPDATE
						$customerRow = $customerStatement->fetch(PDO::FETCH_ASSOC);
						$customerName = $customerRow['fullName'];
						
						// INSERT datos a la tabla de ventas
						$insertSaleSql = 'INSERT INTO sale(itemNumber, itemName, discount, quantity, unitPrice, customerID, customerName, saleDate) VALUES(:itemNumber, :itemName, :discount, :quantity, :unitPrice, :customerID, :customerName, :saleDate)';
						$insertSaleStatement = $conn->prepare($insertSaleSql);
						$insertSaleStatement->execute(['itemNumber' => $itemNumber, 'itemName' => $itemName, 'discount' => $discount, 'quantity' => $quantity, 'unitPrice' => $unitPrice, 'customerID' => $customerID, 'customerName' => $customerName, 'saleDate' => $saleDate]);
						
						// UPDATE las existencias en la tabla de productos
						$stockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
						$stockUpdateStatement = $conn->prepare($stockUpdateSql);
						$stockUpdateStatement->execute(['stock' => $newQuantity, 'itemNumber' => $itemNumber]);
						
						echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se agregaron detalles de venta a la base de datos y se actualizaron las existencias.</div>';
						exit();
						
					} else {
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El cliente no existe.</div>';
						exit();
					}
				}
				
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El producto ya existe en la base de datos. Haga clic en el botón <strong>Actualizar</strong>  para actualizar los detalles. O utilice un número de producto diferente.</div>';
				exit();
			} else {
				// El artículo no existe, por lo tanto no puedes realizar una venta desde él.
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>El producto no existe en la base de datos.</div>';
				exit();
			}

		} else {
			// Uno o más campos obligatorios están vacíos. Por lo tanto, se mostrará un mensaje de error.

			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
			exit();
		}
	}
?>