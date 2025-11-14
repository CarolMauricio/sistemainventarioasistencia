<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	$purchaseDetailsSearchSql = 'SELECT * FROM purchase';
	$purchaseDetailsSearchStatement = $conn->prepare($purchaseDetailsSearchSql);
	$purchaseDetailsSearchStatement->execute();

	$output = '<table id="purchaseDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Compra ID</th>
						<th>Número de Producto</th>
						<th>Fecha de Compra</th>
						<th>Nombre de Producto</th>
						<th>Precio Unitario</th>
						<th>Cantidad</th>
						<th>Nombre de Proveedor</th>
						<th>Proveedor ID</th>
						<th>Precio Total</th>
					</tr>
				</thead>
				<tbody>';
	
	// Crear filas de tablas de los datos seleccionados
	while($row = $purchaseDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$uPrice = $row['unitPrice'];
		$qty = $row['quantity'];
		$totalPrice = $uPrice * $qty;
		
		$output .= '<tr>' .
						'<td>' . $row['purchaseID'] . '</td>' .
						'<td>' . $row['itemNumber'] . '</td>' .
						'<td>' . $row['purchaseDate'] . '</td>' .
						'<td>' . $row['itemName'] . '</td>' .
						'<td>' . $row['unitPrice'] . '</td>' .
						'<td>' . $row['quantity'] . '</td>' .
						'<td>' . $row['vendorName'] . '</td>' .
						'<td>' . $row['vendorID'] . '</td>' .
						'<td>' . $totalPrice . '</td>' .
					'</tr>';
	}
	
	$purchaseDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Compra ID</th>
							<th>Número de Producto</th>
							<th>Fecha de Compra</th>
							<th>Nombre de Producto</th>
							<th>Precio Unitario</th>
							<th>Cantidad</th>
							<th>Nombre de Proveedor</th>
							<th>Proveedor ID</th>
							<th>Precio Total</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>


