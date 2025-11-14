<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Comprobar si se recibe la consulta POST
	if(isset($_POST['vendorDetailsVendorID'])) {
		
		$vendorDetailsVendorID = htmlentities($_POST['vendorDetailsVendorID']);
		$vendorDetailsVendorFullName = htmlentities($_POST['vendorDetailsVendorFullName']);
		$vendorDetailsVendorMobile = htmlentities($_POST['vendorDetailsVendorMobile']);
		$vendorDetailsVendorPhone2 = htmlentities($_POST['vendorDetailsVendorPhone2']);
		$vendorDetailsVendorEmail = htmlentities($_POST['vendorDetailsVendorEmail']);
		$vendorDetailsVendorAddress = htmlentities($_POST['vendorDetailsVendorAddress']);
		$vendorDetailsVendorAddress2 = htmlentities($_POST['vendorDetailsVendorAddress2']);
		$vendorDetailsVendorCity = htmlentities($_POST['vendorDetailsVendorCity']);
		$vendorDetailsVendorDistrict = htmlentities($_POST['vendorDetailsVendorDistrict']);
		$vendorDetailsStatus = htmlentities($_POST['vendorDetailsStatus']);
		
		
		// Comprueba si se proporciona el ID del proveedor. Si no se proporciona, se mostrará un mensaje.
		if(!empty($vendorDetailsVendorID)){
			// Compruebe si los campos obligatorios no están vacíos
			if(!empty($vendorDetailsVendorFullName) && !empty($vendorDetailsVendorMobile) && !empty($vendorDetailsVendorAddress)) {
				
				// Validar número de móvil
				if(filter_var($vendorDetailsVendorMobile, FILTER_VALIDATE_INT) === 0 || filter_var($vendorDetailsVendorMobile, FILTER_VALIDATE_INT)) {
					// El número de móvil es válido
				} else {
					// El número de móvil no es válido
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un número de móvil válido</div>';
					exit();
				}
				
				// Verificar si el campo ID del vendedor está vacío. De ser así, mostrar un mensaje de error.
				// Debemos informar específicamente al usuario porque el (*) no se añade a ese campo.
				if(empty($vendorDetailsVendorID)){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese el ID del proveedor para actualizarlo. Puede encontrarlo en la pestaña búsqueda.</div>';
					exit();
				}
				
				// Validar el segundo número de teléfono solo si lo proporciona el usuario
				if(isset($vendorDetailsVendorPhone2)){
					if(filter_var($vendorDetailsVendorPhone2, FILTER_VALIDATE_INT) === 0 || filter_var($vendorDetailsVendorPhone2, FILTER_VALIDATE_INT)) {
						// Teléfono adicional es válido
					} else {
						// Teléfono adicional no es válido
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese un número válido para el número de teléfono adicional</div>';
						exit();
					}
				}
				
				// Validar el correo electrónico solo si es proporcionado por el usuario
				if(!empty($vendorDetailsVendorEmail)) {
					if (filter_var($vendorDetailsVendorEmail, FILTER_VALIDATE_EMAIL) === false) {
						// Correo electrónico no es válido
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca un correo electrónico válido</div>';
						exit();
					}
				}

				// Comprueba si el ID de vendedor indicado está en la base de datos
				$vendorIDSelectSql = 'SELECT vendorID FROM vendor WHERE vendorID = :vendorID';
				$vendorIDSelectStatement = $conn->prepare($vendorIDSelectSql);
				$vendorIDSelectStatement->execute(['vendorID' => $vendorDetailsVendorID]);
				
				if($vendorIDSelectStatement->rowCount() > 0) {
					
					// El ID del proveedor está disponible en la base de datos. Por lo tanto, podemos actualizar sus datos.

					// Pero primero, actualice el nombre del proveedor en la tabla de compras.
					$purchaseVendorNameSql = 'UPDATE purchase SET vendorName = :vendorName WHERE vendorID = :vendorID';
					$purchaseVendorNameStatement = $conn->prepare($purchaseVendorNameSql);
					$purchaseVendorNameStatement->execute(['vendorName' => $vendorDetailsVendorFullName, 'vendorID' => $vendorDetailsVendorID]);
					
					// Construir la consulta UPDATE
					$updateVendorDetailsSql = 'UPDATE vendor SET fullName = :fullName, email = :email, mobile = :mobile, phone2 = :phone2, address = :address, address2 = :address2, city = :city, district = :district, status = :status WHERE vendorID = :vendorID';
					$updateVendorDetailsStatement = $conn->prepare($updateVendorDetailsSql);
					$updateVendorDetailsStatement->execute(['fullName' => $vendorDetailsVendorFullName, 'email' => $vendorDetailsVendorEmail, 'mobile' => $vendorDetailsVendorMobile, 'phone2' => $vendorDetailsVendorPhone2, 'address' => $vendorDetailsVendorAddress, 'address2' => $vendorDetailsVendorAddress2, 'city' => $vendorDetailsVendorCity, 'district' => $vendorDetailsVendorDistrict, 'vendorID' => $vendorDetailsVendorID, 'status' => $vendorDetailsStatus]);
					
					// UPDATE el nombre del proveedor en la tabla de compras también
					$updateVendorInPurchaseTableSql = 'UPDATE purchase SET vendorName = :vendorName WHERE vendorID = :vendorID';
					$updateVendorInPurchaseTableStatement = $conn->prepare($updateVendorInPurchaseTableSql);
					$updateVendorInPurchaseTableStatement->execute(['vendorName' => $vendorDetailsVendorFullName, 'vendorID' => $vendorDetailsVendorID]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalles del proveedor actualizados.</div>';
					exit();
				} else {
					// El ID del proveedor no está en la base de datos. Por lo tanto, detenga la actualización y salga.
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El ID del proveedor no existe en la base de datos. Por lo tanto, no es posible actualizarlo.</div>';
					exit();
				}
				
			} else {
				// Uno o más campos obligatorios están vacíos. Por lo tanto, muestre el mensaje de error.
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, introduzca todos los campos marcados con un (*)</div>';
				exit();
			}
		} else {
			// El usuario no proporciona el ID del proveedor. Por lo tanto, no se puede actualizar.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Ingrese el ID del proveedor para actualizarlo. Puede encontrarlo en la pestaña Búsqueda.</div>';
			exit();
		}
	}
?>