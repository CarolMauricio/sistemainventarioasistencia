<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$itemDetailsSearchSql = 'SELECT * FROM item';
	$itemDetailsSearchStatement = $conn->prepare($itemDetailsSearchSql);
	$itemDetailsSearchStatement->execute();
	
	$output = '<table id="itemDetailsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Producto ID</th>
						<th>Número de Producto</th>
						<th>Nombre de Producto</th>
						<th>% de Descuento</th>
						<th>Existencias</th>
						<th>Precio Unitario</th>
						<th>Sucursal</th>
						<th>Descripción</th>
					</tr>
				</thead>
				<tbody>';
	
	// Crear la filas de la tabla con los datos seleccionados
	while($row = $itemDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		
		$output .= '<tr>' .
						'<td>' . $row['productID'] . '</td>' .
						'<td>' . $row['itemNumber'] . '</td>' .
						'<td><a href="#" class="itemDetailsHover" data-toggle="popover" id="' . $row['productID'] . '">' . $row['itemName'] . '</a></td>' .
						'<td>' . $row['discount'] . '</td>' .
						'<td>' . $row['stock'] . '</td>' .
						'<td>' . $row['unitPrice'] . '</td>' .
						'<td>' . $row['status'] . '</td>' .
						'<td>' . $row['description'] . '</td>' .
					'</tr>';
	}
	
	$itemDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Producto ID</th>
							<th>Número de Producto</th>
							<th>Nombre de Producto</th>
							<th>% de Descuento</th>
							<th>Existencias</th>
							<th>Precio Unitario</th>
							<th>Sucursal</th>
							<th>Descripción</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>