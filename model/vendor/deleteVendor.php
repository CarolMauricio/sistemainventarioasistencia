<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['vendorDetailsVendorID'])){
		
		$vendorDetailsVendorID = htmlentities($_POST['vendorDetailsVendorID']);
		
		// Compruebe si los campos obligatorios no están vacíos
		if(!empty($vendorDetailsVendorID)){
			
			// Desinfectar número de producto
			$vendorDetailsVendorID = filter_var($vendorDetailsVendorID, FILTER_SANITIZE_STRING);

			// Compruebe si el cliente está en la base de datos.
			$vendorSql = 'SELECT vendorID FROM vendor WHERE vendorID=:vendorID';
			$vendorStatement = $conn->prepare($vendorSql);
			$vendorStatement->execute(['vendorID' => $vendorDetailsVendorID]);
			
			if($vendorStatement->rowCount() > 0){
				
				// El proveedor existe en la base de datos. Por lo tanto, inicie el proceso de DELETE.
				$deleteVendorSql = 'DELETE FROM vendor WHERE vendorID=:vendorID';
				$deleteVendorStatement = $conn->prepare($deleteVendorSql);
				$deleteVendorStatement->execute(['vendorID' => $vendorDetailsVendorID]);

				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Proveedor eliminado.</div>';
				exit();
				
			} else {
				// El proveedor no existe, por lo tanto, dígale al usuario que no puede eliminar ese proveedor. 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>El proveedor no existe en la base de datos. Por lo tanto, no se puede eliminar.</div>';
				exit();
			}
			
		} else {
			// El número de Proveedor ID está vacío. Por lo tanto, se muestra el mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese el Proveedor ID</div>';
			exit();
		}
	}
?>