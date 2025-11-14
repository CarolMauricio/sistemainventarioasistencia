<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$customerDetailsSearchSql = 'SELECT * FROM customer';
	$customerDetailsSearchStatement = $conn->prepare($customerDetailsSearchSql);
	$customerDetailsSearchStatement->execute();

	$output = '<table id="customerReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>Cliente ID</th>
						<th>Nombre Completo</th>
						<th>Correo Electrónico</th>
						<th>Teléfono</th>
						<th>Teléfono Adicional</th>
						<th>Dirección</th>
						<th>Dirección Complementaria</th>
						<th>Municipio</th>
						<th>Departamento</th>
						<th>Sucursal</th>
					</tr>
				</thead>
				<tbody>';
	
	// Crear filas de tabla a partir de los datos seleccionados
	while($row = $customerDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$output .= '<tr>' .
						'<td>' . $row['customerID'] . '</td>' .
						'<td>' . $row['fullName'] . '</td>' .
						'<td>' . $row['email'] . '</td>' .
						'<td>' . $row['mobile'] . '</td>' .
						'<td>' . $row['phone2'] . '</td>' .
						'<td>' . $row['address'] . '</td>' .
						'<td>' . $row['address2'] . '</td>' .
						'<td>' . $row['city'] . '</td>' .
						'<td>' . $row['district'] . '</td>' .
						'<td>' . $row['status'] . '</td>' .
					'</tr>';
	}
	
	$customerDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Cliente ID</th>
							<th>Nombre Completo</th>
							<th>Correo Electrónico</th>
							<th>Teléfono</th>
							<th>Teléfono Adicional</th>
							<th>Dirección</th>
							<th>Dirección Complementaria</th>
							<th>Municipio</th>
							<th>Departamento</th>
							<th>Sucursal</th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>