<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	$saleDetailsSearchSql = 'SELECT * FROM sale';
	$saleDetailsSearchStatement = $conn->prepare($saleDetailsSearchSql);
	$saleDetailsSearchStatement->execute();

	$output = '<table id="saleDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Venta ID</th>
						<th>Número de Producto</th>
						<th>Cliente ID</th>
						<th>Nombre de Cliente</th>
						<th>Nombre de Producto</th>
						<th>Fecha de Venta</th>
						<th>% de Descuento</th>
						<th>Cantidad</th>
						<th>Precio Unitario</th>
						<th>Precio Total</th>
					</tr>
				</thead>
				<tbody>';
	
	// Crear filas de tabla a partir de los datos seleccionados
	while($row = $saleDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$uPrice = $row['unitPrice'];
		$qty = $row['quantity'];
		$discount = $row['discount'];
		$totalPrice = $uPrice * $qty * ((100 - $discount)/100);
			
		$output .= '<tr>' .
						'<td>' . $row['saleID'] . '</td>' .
						'<td>' . $row['itemNumber'] . '</td>' .
						'<td>' . $row['customerID'] . '</td>' .
						'<td>' . $row['customerName'] . '</td>' .
						'<td>' . $row['itemName'] . '</td>' .
						'<td>' . $row['saleDate'] . '</td>' .
						'<td>' . $row['discount'] . '</td>' .
						'<td>' . $row['quantity'] . '</td>' .
						'<td>' . $row['unitPrice'] . '</td>' .
						'<td>' . $totalPrice . '</td>' .
					'</tr>';
	}
	
	$saleDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Venta ID</th>
							<th>Número de Producto</th>
							<th>Cliente ID</th>
							<th>Nombre de Cliente</th>
							<th>Nombre de Producto</th>
							<th>Fecha de Venta</th>
							<th>% de Descuento</th>
							<th>Cantidad</th>
							<th>Precio Unitario</th>
							<th>Precio Total</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>


