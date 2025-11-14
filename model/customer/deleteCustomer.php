<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['customerDetailsCustomerID'])){
		
		$customerDetailsCustomerID = htmlentities($_POST['customerDetailsCustomerID']);
		
		// Compruebe si los campos obligatorios no están vacíos
		if(!empty($customerDetailsCustomerID)){
			
			// Desinfectar ClienteID
			$customerDetailsCustomerID = filter_var($customerDetailsCustomerID, FILTER_SANITIZE_STRING);

			// Revisar si el cliente esta en la base de datos
			$customerSql = 'SELECT customerID FROM customer WHERE customerID=:customerID';
			$customerStatement = $conn->prepare($customerSql);
			$customerStatement->execute(['customerID' => $customerDetailsCustomerID]);
			
			if($customerStatement->rowCount() > 0){
				
				// El cliente existe en la base de datos. Por lo tanto, inicie el proceso DELETE.
				$deleteCustomerSql = 'DELETE FROM customer WHERE customerID=:customerID';
				$deleteCustomerStatement = $conn->prepare($deleteCustomerSql);
				$deleteCustomerStatement->execute(['customerID' => $customerDetailsCustomerID]);

				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Cliente eliminado.</div>';
				exit();
				
			} else {
				//El cliente no existe, por lo tanto, le indicamos al usuario que no puede eliminar ese cliente
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Cliente no existe en la base de datos. Por lo tanto, no se puede eliminar</div>';
				exit();
			}
			
		} else {
			// El ClienteID está vacío. Por lo tanto, se muestra el mensaje de error.
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor ingrese ClienteID</div>';
			exit();
		}
	}
?>