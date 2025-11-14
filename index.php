<?php
	session_start();
	// Redirigir al usuario a la página de inicio de sesión si no ha iniciado sesión.
	if(!isset($_SESSION['loggedIn'])){
		header('Location: login.php');
		exit();
	}
	
	require_once('inc/config/constants.php');
	require_once('inc/config/db.php');
	require_once('inc/header.html');
?>
  <body>
<?php
	require 'inc/navigation.php';
?>
    <!-- Contenido de la Página -->
    <div class="container-fluid">
	  <div class="row">
		<div class="col-lg-2">
		<h1 class="my-4"></h1>
			<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
			  <a class="nav-link active" id="v-pills-item-tab" data-toggle="pill" href="#v-pills-item" role="tab" aria-controls="v-pills-item" aria-selected="true">Productos</a>
			  <a class="nav-link" id="v-pills-purchase-tab" data-toggle="pill" href="#v-pills-purchase" role="tab" aria-controls="v-pills-purchase" aria-selected="false">Compras</a>
			  <a class="nav-link" id="v-pills-vendor-tab" data-toggle="pill" href="#v-pills-vendor" role="tab" aria-controls="v-pills-vendor" aria-selected="false">Proveedores</a>
			  <a class="nav-link" id="v-pills-sale-tab" data-toggle="pill" href="#v-pills-sale" role="tab" aria-controls="v-pills-sale" aria-selected="false">Ventas</a>
			  <a class="nav-link" id="v-pills-customer-tab" data-toggle="pill" href="#v-pills-customer" role="tab" aria-controls="v-pills-customer" aria-selected="false">Clientes</a>
			  <a class="nav-link" id="v-pills-search-tab" data-toggle="pill" href="#v-pills-search" role="tab" aria-controls="v-pills-search" aria-selected="false">Búsqueda</a>
			  <a class="nav-link" id="v-pills-reports-tab" data-toggle="pill" href="#v-pills-reports" role="tab" aria-controls="v-pills-reports" aria-selected="false">Reportes</a>
			  <a class="nav-link" id="v-pills-assistance-tab" data-toggle="pill" href="#v-pills-assistance" role="tab" aria-controls="v-pills-assistance" aria-selected="false">Asistencia</a>
			</div>
		</div>
		 <div class="col-lg-10">
			<div class="tab-content" id="v-pills-tabContent">
			  <div class="tab-pane fade show active" id="v-pills-item" role="tabpanel" aria-labelledby="v-pills-item-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Detalle de Producto</div>
				  <div class="card-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#itemDetailsTab">Producto</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#itemImageTab">Cargar Imagen</a>
						</li>
					</ul>
					
					<!-- Paneles de pestañas para detalles de artículos y secciones de imágenes -->
					<div class="tab-content">
						<div id="itemDetailsTab" class="container-fluid tab-pane active">
							<br>
							<!-- Div para mostrar mensajes ajax de validaciones/envíos a base de datos -->
							<div id="itemDetailsMessage"></div>
							<form>
							  <div class="form-row">
								<div class="form-group col-md-3" style="display:inline-block">
								  <label for="itemDetailsItemNumber">Número de Producto<span class="requiredIcon">*</span></label>
								  <input type="text" class="form-control" name="itemDetailsItemNumber" id="itemDetailsItemNumber" autocomplete="off">
								  <div id="itemDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
								</div>
								<div class="form-group col-md-3">
								  <label for="itemDetailsProductID">Producto ID</label>
								  <input class="form-control invTooltip" type="number" readonly  id="itemDetailsProductID" name="itemDetailsProductID" title="Esto se generará automáticamente cuando agregue un nuevo registro">
								</div>
							  </div>
							  <div class="form-row">
								  <div class="form-group col-md-6">
									<label for="itemDetailsItemName">Nombre de Producto<span class="requiredIcon">*</span></label>
									<input type="text" class="form-control" name="itemDetailsItemName" id="itemDetailsItemName" autocomplete="off">
									<div id="itemDetailsItemNameSuggestionsDiv" class="customListDivWidth"></div>
								  </div>
								  <div class="form-group col-md-2">
									<label for="itemDetailsStatus">Sucursal</label>
									<select id="itemDetailsStatus" name="itemDetailsStatus" class="form-control chosenSelect">
										<?php include('inc/statusList.html'); ?>
									</select>
								  </div>
							  </div>
							  <div class="form-row">
								<div class="form-group col-md-6" style="display:inline-block">
								  <!-- <label for="itemDetailsDescription">Description</label> -->
								  <textarea rows="4" class="form-control" placeholder="Descripción" name="itemDetailsDescription" id="itemDetailsDescription"></textarea>
								</div>
							  </div>
							  <div class="form-row">
								<div class="form-group col-md-3">
								  <label for="itemDetailsDiscount">% de Descuento</label>
								  <input type="text" class="form-control" value="0" name="itemDetailsDiscount" id="itemDetailsDiscount">
								</div>
								<div class="form-group col-md-3">
								  <label for="itemDetailsQuantity">Cantidad<span class="requiredIcon">*</span></label>
								  <input type="number" class="form-control" value="0" name="itemDetailsQuantity" id="itemDetailsQuantity">
								</div>
								<div class="form-group col-md-3">
								  <label for="itemDetailsUnitPrice">Precio Unitario<span class="requiredIcon">*</span></label>
								  <input type="text" class="form-control" value="0" name="itemDetailsUnitPrice" id="itemDetailsUnitPrice">
								</div>
								<div class="form-group col-md-3">
								  <label for="itemDetailsTotalStock">Total de Existencias</label>
								  <input type="text" class="form-control" name="itemDetailsTotalStock" id="itemDetailsTotalStock" readonly>
								</div>
								<div class="form-group col-md-3">
									<div id="imageContainer"></div>
								</div>
							  </div>
							  <button type="button" id="addItem" class="btn btn-success">Añadir</button>
							  <button type="button" id="updateItemDetailsButton" class="btn btn-primary">Actualizar</button>
							  <button type="button" id="deleteItem" class="btn btn-danger">Eliminar</button>
							  <button type="reset" class="btn" id="itemClear">Limpiar</button>
							</form>
						</div>
						<div id="itemImageTab" class="container-fluid tab-pane fade">
							<br>
							<div id="itemImageMessage"></div>
							<p>Puedes cargar una imagen para un producto en particular usando esta sección.</p> 
							<p>Asegúrese de que el producto ya esté agregado a la base de datos antes de cargar la imagen.</p>
							<br>							
							<form name="imageForm" id="imageForm" method="post">
							  <div class="form-row">
								<div class="form-group col-md-3" style="display:inline-block">
								  <label for="itemImageItemNumber">Número de Producto<span class="requiredIcon">*</span></label>
								  <input type="text" class="form-control" name="itemImageItemNumber" id="itemImageItemNumber" autocomplete="off">
								  <div id="itemImageItemNumberSuggestionsDiv" class="customListDivWidth"></div>
								</div>
								<div class="form-group col-md-4">
									<label for="itemImageItemName">Nombre de Producto</label>
									<input type="text" class="form-control" name="itemImageItemName" id="itemImageItemName" readonly>
								</div>
							  </div>
							  <br>
							  <div class="form-row">
								  <div class="form-group col-md-7">
									<label for="itemImageFile">Seleccione una imagen ( <span class="blueText">jpg</span>, <span class="blueText">jpeg</span>, <span class="blueText">gif</span>, <span class="blueText">png</span> solo)</label>
									<input type="file" class="form-control-file btn btn-dark" id="itemImageFile" name="itemImageFile">
								  </div>
							  </div>
							  <br>
							  <button type="button" id="updateImageButton" class="btn btn-primary">Cargar Imagen</button>
							  <button type="button" id="deleteImageButton" class="btn btn-danger">Eliminar Imagen</button>
							  <button type="reset" class="btn">Limpiar</button>
							</form>
						</div>
					</div>
				  </div> 
				</div>
			  </div>
			  <div class="tab-pane fade" id="v-pills-purchase" role="tabpanel" aria-labelledby="v-pills-purchase-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Detalle de Compra</div>
				  <div class="card-body">
					<div id="purchaseDetailsMessage"></div>
					<form>
					  <div class="form-row">
						<div class="form-group col-md-3">
						  <label for="purchaseDetailsItemNumber">Número de Producto<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="purchaseDetailsItemNumber" name="purchaseDetailsItemNumber" autocomplete="off">
						  <div id="purchaseDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
						</div>
						<div class="form-group col-md-3">
						  <label for="purchaseDetailsPurchaseDate">Fecha de Compra<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control datepicker" id="purchaseDetailsPurchaseDate" name="purchaseDetailsPurchaseDate" readonly value="2025-10-01">
						</div>
						<div class="form-group col-md-2">
						  <label for="purchaseDetailsPurchaseID">Compra ID</label>
						  <input type="text" class="form-control invTooltip" id="purchaseDetailsPurchaseID" name="purchaseDetailsPurchaseID" title="Esto se generará automáticamente cuando agregue un nuevo registro" autocomplete="off">
						  <div id="purchaseDetailsPurchaseIDSuggestionsDiv" class="customListDivWidth"></div>
						</div>
					  </div>
					  <div class="form-row"> 
						  <div class="form-group col-md-4">
							<label for="purchaseDetailsItemName">Nombre de Producto<span class="requiredIcon">*</span></label>
							<input type="text" class="form-control invTooltip" id="purchaseDetailsItemName" name="purchaseDetailsItemName" readonly title="Esto se completará automáticamente cuando ingrese el número de artículo anterior">
						  </div>
						  <div class="form-group col-md-2">
							  <label for="purchaseDetailsCurrentStock">En Existencia</label>
							  <input type="text" class="form-control" id="purchaseDetailsCurrentStock" name="purchaseDetailsCurrentStock" readonly>
						  </div>
						  <div class="form-group col-md-4">
							<label for="purchaseDetailsVendorName">Proveedor<span class="requiredIcon">*</span></label>
							<select id="purchaseDetailsVendorName" name="purchaseDetailsVendorName" class="form-control chosenSelect">
								<?php 
									require('model/vendor/getVendorNames.php');
								?>
							</select>
						  </div>
					  </div>
					  <div class="form-row">
						<div class="form-group col-md-2">
						  <label for="purchaseDetailsQuantity">Cantidad<span class="requiredIcon">*</span></label>
						  <input type="number" class="form-control" id="purchaseDetailsQuantity" name="purchaseDetailsQuantity" value="0">
						</div>
						<div class="form-group col-md-2">
						  <label for="purchaseDetailsUnitPrice">Precio Unitario<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="purchaseDetailsUnitPrice" name="purchaseDetailsUnitPrice" value="0">
						  
						</div>
						<div class="form-group col-md-2">
						  <label for="purchaseDetailsTotal">Total</label>
						  <input type="text" class="form-control" id="purchaseDetailsTotal" name="purchaseDetailsTotal" readonly>
						</div>
					  </div>
					  <button type="button" id="addPurchase" class="btn btn-success">Añadir Compra</button>
					  <button type="button" id="updatePurchaseDetailsButton" class="btn btn-primary">Actualizar</button>
					  <button type="reset" class="btn">Limpiar</button>
					</form>
				  </div> 
				</div>
			  </div>

				<!-- Sección: Asistencia -->
				<div class="tab-pane fade" id="v-pills-assistance" role="tabpanel" aria-labelledby="v-pills-assistance-tab">
				<div class="card mt-4 text-center">
					<div class="card-header bg-light">
					<h5 class="mb-0">Portal de Asistencia</h5>
					</div>

					<div class="card-body d-flex flex-column justify-content-center align-items-center" style="height: 400px;">
					<p class="mb-4" style="font-size: 1.1rem;">
						Accede al portal oficial de Hik-Connect para consultar y descargar los registros de asistencia.
					</p>
					<a 
						href="https://www.hik-connect.com/views/login/index.html#/login" 
						target="_blank" 
						class="btn btn-primary btn-lg px-5 py-3" 
						style="font-size: 1.2rem; border-radius: 8px;">
						Accede al portal de asistencia
					</a>
					</div>
				</div>
				</div>
			  
			  <div class="tab-pane fade" id="v-pills-vendor" role="tabpanel" aria-labelledby="v-pills-vendor-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Detalle de Proveedor</div>
				  <div class="card-body">
				  <!-- Div para mostrar mensajes ajax de validaciones/envíos a base de datos -->
				  <div id="vendorDetailsMessage"></div>
					 <form> 
					  <div class="form-row">
						<div class="form-group col-md-6">
						  <label for="vendorDetailsVendorFullName">Nombre Completo<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="vendorDetailsVendorFullName" name="vendorDetailsVendorFullName" placeholder="">
						</div>
						<div class="form-group col-md-2">
							<label for="vendorDetailsStatus">Sucursal</label>
							<select id="vendorDetailsStatus" name="vendorDetailsStatus" class="form-control chosenSelect">
								<?php include('inc/statusList.html'); ?>
							</select>
						</div>
						 <div class="form-group col-md-3">
							<label for="vendorDetailsVendorID">Proveedor ID</label>
							<input type="text" class="form-control invTooltip" id="vendorDetailsVendorID" name="vendorDetailsVendorID" title="Esto se generará automáticamente cuando agregue un nuevo proveedor" autocomplete="off">
							<div id="vendorDetailsVendorIDSuggestionsDiv" class="customListDivWidth"></div>
						</div>
					  </div>
					  <div class="form-row">
						  <div class="form-group col-md-3">
							<label for="vendorDetailsVendorMobile">Teléfono móvil<span class="requiredIcon">*</span></label>
							<input type="text" class="form-control invTooltip" id="vendorDetailsVendorMobile" name="vendorDetailsVendorMobile" title="No introduzca 0 a la izquierda">
						  </div>
						  <div class="form-group col-md-3">
							<label for="vendorDetailsVendorPhone2">Teléfono adicional</label>
							<input type="text" class="form-control invTooltip" id="vendorDetailsVendorPhone2" name="vendorDetailsVendorPhone2" title="No introduzca 0 a la izquierda">
						  </div>
						  <div class="form-group col-md-6">
							<label for="vendorDetailsVendorEmail">Correo Electrónico</label>
							<input type="email" class="form-control" id="vendorDetailsVendorEmail" name="vendorDetailsVendorEmail">
						</div>
					  </div>
					  <div class="form-group">
						<label for="vendorDetailsVendorAddress">Dirección<span class="requiredIcon">*</span></label>
						<input type="text" class="form-control" id="vendorDetailsVendorAddress" name="vendorDetailsVendorAddress">
					  </div>
					  <div class="form-group">
						<label for="vendorDetailsVendorAddress2">Dirección Complementaria</label>
						<input type="text" class="form-control" id="vendorDetailsVendorAddress2" name="vendorDetailsVendorAddress2">
					  </div>
					  <div class="form-row">
						<div class="form-group col-md-6">
						  <label for="vendorDetailsVendorCity">Municipio</label>
						  <input type="text" class="form-control" id="vendorDetailsVendorCity" name="vendorDetailsVendorCity">
						</div>
						<div class="form-group col-md-4">
						  <label for="vendorDetailsVendorDistrict">Departamento</label>
						  <select id="vendorDetailsVendorDistrict" name="vendorDetailsVendorDistrict" class="form-control chosenSelect">
							<?php include('inc/districtList.html'); ?>
						  </select>
						</div>
					  </div>					  
					  <button type="button" id="addVendor" name="addVendor" class="btn btn-success">Añadir Proveedor</button>
					  <button type="button" id="updateVendorDetailsButton" class="btn btn-primary">Actualizar</button>
					  <button type="button" id="deleteVendorButton" class="btn btn-danger">Eliminar</button>
					  <button type="reset" class="btn">Limpiar</button>
					 </form>
				  </div> 
				</div>
			  </div>
			    
			  <div class="tab-pane fade" id="v-pills-sale" role="tabpanel" aria-labelledby="v-pills-sale-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Detalle de Venta</div>
				  <div class="card-body">
					<div id="saleDetailsMessage"></div>
					<form>
					  <div class="form-row">
						<div class="form-group col-md-3">
						  <label for="saleDetailsItemNumber">Número de Producto<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="saleDetailsItemNumber" name="saleDetailsItemNumber" autocomplete="off">
						  <div id="saleDetailsItemNumberSuggestionsDiv" class="customListDivWidth"></div>
						</div>
						<div class="form-group col-md-3">
							<label for="saleDetailsCustomerID">Cliente ID<span class="requiredIcon">*</span></label>
							<input type="text" class="form-control" id="saleDetailsCustomerID" name="saleDetailsCustomerID" autocomplete="off">
							<div id="saleDetailsCustomerIDSuggestionsDiv" class="customListDivWidth"></div>
						</div>
						<div class="form-group col-md-4">
						  <label for="saleDetailsCustomerName">Nombre de Cliente</label>
						  <input type="text" class="form-control" id="saleDetailsCustomerName" name="saleDetailsCustomerName" readonly>
						</div>
						<div class="form-group col-md-2">
						  <label for="saleDetailsSaleID">Venta ID</label>
						  <input type="text" class="form-control invTooltip" id="saleDetailsSaleID" name="saleDetailsSaleID" title="Esto se generará automáticamente cuando agregue un nuevo registro" autocomplete="off">
						  <div id="saleDetailsSaleIDSuggestionsDiv" class="customListDivWidth"></div>
						</div>
					  </div>
					  <div class="form-row">
						  <div class="form-group col-md-5">
							<label for="saleDetailsItemName">Nombre de Producto</label>
							<!--<select id="saleDetailsItemNames" name="saleDetailsItemNames" class="form-control chosenSelect"> -->
								<?php 
									//require('model/item/getItemDetails.php');
								?>
							<!-- </select> -->
							<input type="text" class="form-control invTooltip" id="saleDetailsItemName" name="saleDetailsItemName" readonly title="Esto se completará automáticamente cuando ingrese el número de artículo anterior">
						  </div>
						  <div class="form-group col-md-3">
							  <label for="saleDetailsSaleDate">Fecha de Venta<span class="requiredIcon">*</span></label>
							  <input type="text" class="form-control datepicker" id="saleDetailsSaleDate" value="2025-10-01" name="saleDetailsSaleDate" readonly>
						  </div>
					  </div>
					  <div class="form-row">
						<div class="form-group col-md-2">
								  <label for="saleDetailsTotalStock">Total de Existencias</label>
								  <input type="text" class="form-control" name="saleDetailsTotalStock" id="saleDetailsTotalStock" readonly>
								</div>
						<div class="form-group col-md-2">
						  <label for="saleDetailsDiscount">% de Descuento</label>
						  <input type="text" class="form-control" id="saleDetailsDiscount" name="saleDetailsDiscount" value="0">
						</div>
						<div class="form-group col-md-2">
						  <label for="saleDetailsQuantity">Cantidad<span class="requiredIcon">*</span></label>
						  <input type="number" class="form-control" id="saleDetailsQuantity" name="saleDetailsQuantity" value="0">
						</div>
						<div class="form-group col-md-2">
						  <label for="saleDetailsUnitPrice">Precio Unitario<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="saleDetailsUnitPrice" name="saleDetailsUnitPrice" value="0">
						</div>
						<div class="form-group col-md-3">
						  <label for="saleDetailsTotal">Total</label>
						  <input type="text" class="form-control" id="saleDetailsTotal" name="saleDetailsTotal">
						</div>
					  </div>
					  <div class="form-row">
						  <div class="form-group col-md-3">
							<div id="saleDetailsImageContainer"></div>
						  </div>
					 </div>
					  <button type="button" id="addSaleButton" class="btn btn-success">Añadir Venta</button>
					  <button type="button" id="updateSaleDetailsButton" class="btn btn-primary">Actualizar</button>
					  <button type="reset" id="saleClear" class="btn">Limpiar</button>
					</form>
				  </div> 
				</div>
			  </div>
			  <div class="tab-pane fade" id="v-pills-customer" role="tabpanel" aria-labelledby="v-pills-customer-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Detalle de Cliente</div>
				  <div class="card-body">
				   <!-- Div para mostrar mensajes ajax de validaciones/envíos a base de datos -->
				  <div id="customerDetailsMessage"></div>
					 <form> 
					  <div class="form-row">
						<div class="form-group col-md-6">
						  <label for="customerDetailsCustomerFullName">Nombre Completo<span class="requiredIcon">*</span></label>
						  <input type="text" class="form-control" id="customerDetailsCustomerFullName" name="customerDetailsCustomerFullName">
						</div>
						<div class="form-group col-md-2">
							<label for="customerDetailsStatus">Sucursal</label>
							<select id="customerDetailsStatus" name="customerDetailsStatus" class="form-control chosenSelect">
								<?php include('inc/statusList.html'); ?>
							</select>
						</div>
						 <div class="form-group col-md-3">
							<label for="customerDetailsCustomerID">Cliente ID</label>
							<input type="text" class="form-control invTooltip" id="customerDetailsCustomerID" name="customerDetailsCustomerID" title="Esto se generará automáticamente cuando agregue un nuevo cliente" autocomplete="off">
							<div id="customerDetailsCustomerIDSuggestionsDiv" class="customListDivWidth"></div>
						</div>
					  </div>
					  <div class="form-row">
						  <div class="form-group col-md-3">
							<label for="customerDetailsCustomerMobile">Teléfono (móvil)<span class="requiredIcon">*</span></label>
							<input type="text" class="form-control invTooltip" id="customerDetailsCustomerMobile" name="customerDetailsCustomerMobile" title="Do not enter leading 0">
						  </div>
						  <div class="form-group col-md-3">
							<label for="customerDetailsCustomerPhone2">Teléfono Adicional</label>
							<input type="text" class="form-control invTooltip" id="customerDetailsCustomerPhone2" name="customerDetailsCustomerPhone2" title="Do not enter leading 0">
						  </div>
						  <div class="form-group col-md-6">
							<label for="customerDetailsCustomerEmail">Correo Electrónico</label>
							<input type="email" class="form-control" id="customerDetailsCustomerEmail" name="customerDetailsCustomerEmail">
						</div>
					  </div>
					  <div class="form-group">
						<label for="customerDetailsCustomerAddress">Dirección<span class="requiredIcon">*</span></label>
						<input type="text" class="form-control" id="customerDetailsCustomerAddress" name="customerDetailsCustomerAddress">
					  </div>
					  <div class="form-group">
						<label for="customerDetailsCustomerAddress2">Dirección Complementaria</label>
						<input type="text" class="form-control" id="customerDetailsCustomerAddress2" name="customerDetailsCustomerAddress2">
					  </div>
					  <div class="form-row">
						<div class="form-group col-md-6">
						  <label for="customerDetailsCustomerCity">Municipio</label>
						  <input type="text" class="form-control" id="customerDetailsCustomerCity" name="customerDetailsCustomerCity">
						</div>
						<div class="form-group col-md-4">
						  <label for="customerDetailsCustomerDistrict">Departamento</label>
						  <select id="customerDetailsCustomerDistrict" name="customerDetailsCustomerDistrict" class="form-control chosenSelect">
							<?php include('inc/districtList.html'); ?>
						  </select>
						</div>
					  </div>					  
					  <button type="button" id="addCustomer" name="addCustomer" class="btn btn-success">Añadir Cliente</button>
					  <button type="button" id="updateCustomerDetailsButton" class="btn btn-primary">Actualizar</button>
					  <button type="button" id="deleteCustomerButton" class="btn btn-danger">Eliminar</button>
					  <button type="reset" class="btn">Limpiar</button>
					 </form>
				  </div> 
				</div>
			  </div>
			  
			  <div class="tab-pane fade" id="v-pills-search" role="tabpanel" aria-labelledby="v-pills-search-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Búsqueda de Inventario<button id="searchTablesRefresh" name="searchTablesRefresh" class="btn btn-warning float-right btn-sm">Actualizar Registros</button></div>
				  <div class="card-body">										
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#itemSearchTab">Productos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#customerSearchTab">Clientes</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#saleSearchTab">Ventas</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#purchaseSearchTab">Compras</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#vendorSearchTab">Proveedores</a>
						</li>
					</ul>
  
					<!-- Tab panes -->
					<div class="tab-content">
						<div id="itemSearchTab" class="container-fluid tab-pane active">
						  <br>
						  <p>Use la tabla a continuación para buscar todos los detalles de los productos</p>
						  <!-- <a href="#" class="itemDetailsHover" data-toggle="popover" id="10">wwwee</a> -->
							<div class="table-responsive" id="itemDetailsTableDiv"></div>
						</div>
						<div id="customerSearchTab" class="container-fluid tab-pane fade">
						  <br>
						  <p>Use la tabla a continuación para buscar todos los detalles de los clientes</p>
							<div class="table-responsive" id="customerDetailsTableDiv"></div>
						</div>
						<div id="saleSearchTab" class="container-fluid tab-pane fade">
							<br>
							<p>Use la tabla a continuación para buscar todos los detalles de las ventas</p>
							<div class="table-responsive" id="saleDetailsTableDiv"></div>
						</div>
						<div id="purchaseSearchTab" class="container-fluid tab-pane fade">
							<br>
							<p>Use la tabla a continuación para buscar todos los detalles de las compras</p>
							<div class="table-responsive" id="purchaseDetailsTableDiv"></div>
						</div>
						<div id="vendorSearchTab" class="container-fluid tab-pane fade">
							<br>
							<p>Use la tabla a continuación para buscar todos los detalles de los proveedores</p>
							<div class="table-responsive" id="vendorDetailsTableDiv"></div>
						</div>
					</div>
				  </div> 
				</div>
			  </div>
			  
			  <div class="tab-pane fade" id="v-pills-reports" role="tabpanel" aria-labelledby="v-pills-reports-tab">
				<div class="card card-outline-secondary my-4">
				  <div class="card-header">Reportes<button id="reportsTablesRefresh" name="reportsTablesRefresh" class="btn btn-warning float-right btn-sm">Actualizar Registros</button></div>
				  <div class="card-body">										
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#itemReportsTab">Productos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#customerReportsTab">Clientes</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#saleReportsTab">Ventas</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#purchaseReportsTab">Compras</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#vendorReportsTab">Proveedores</a>
						</li>
					</ul>
  
					<!-- Tab panes para la sección de reportes -->
					<div class="tab-content">
						<div id="itemReportsTab" class="container-fluid tab-pane active">
							<br>
							<p>Use la siguiente tabla para obtener el reporte de productos</p>
							<div class="table-responsive" id="itemReportsTableDiv"></div>
						</div>
						<div id="customerReportsTab" class="container-fluid tab-pane fade">
							<br>
							<p>Use la siguiente tabla para obtener el reporte de clientes</p>
							<div class="table-responsive" id="customerReportsTableDiv"></div>
						</div>
						<div id="saleReportsTab" class="container-fluid tab-pane fade">
							<br>
							<!-- <p>Use the grid below to get reports for sales</p> -->
							<form> 
							  <div class="form-row">
								  <div class="form-group col-md-3">
									<label for="saleReportStartDate">Fecha Inicial</label>
									<input type="text" class="form-control datepicker" id="saleReportStartDate" value="2025-10-01" name="saleReportStartDate" readonly>
								  </div>
								  <div class="form-group col-md-3">
									<label for="saleReportEndDate">Fecha Final</label>
									<input type="text" class="form-control datepicker" id="saleReportEndDate" value="2025-10-31" name="saleReportEndDate" readonly>
								  </div>
							  </div>
							  <button type="button" id="showSaleReport" class="btn btn-dark">Mostrar Reporte</button>
							  <button type="reset" id="saleFilterClear" class="btn">Limpiar</button>
							</form>
							<br><br>
							<div class="table-responsive" id="saleReportsTableDiv"></div>
						</div>
						<div id="purchaseReportsTab" class="container-fluid tab-pane fade">
							<br>
							<!-- <p>Use the grid below to get reports for purchases</p> -->
							<form> 
							  <div class="form-row">
								  <div class="form-group col-md-3">
									<label for="purchaseReportStartDate">Fecha Inicial</label>
									<input type="text" class="form-control datepicker" id="purchaseReportStartDate" value="2025-10-01" name="purchaseReportStartDate" readonly>
								  </div>
								  <div class="form-group col-md-3">
									<label for="purchaseReportEndDate">Fecha Final</label>
									<input type="text" class="form-control datepicker" id="purchaseReportEndDate" value="2025-10-31" name="purchaseReportEndDate" readonly>
								  </div>
							  </div>
							  <button type="button" id="showPurchaseReport" class="btn btn-dark">Mostrar Reporte</button>
							  <button type="reset" id="purchaseFilterClear" class="btn">Limpiar</button>
							</form>
							<br><br>
							<div class="table-responsive" id="purchaseReportsTableDiv"></div>
						</div>
						<div id="vendorReportsTab" class="container-fluid tab-pane fade">
							<br>
							<p>Use la siguiente tabla para obtener el reporte de proveedores</p>
							<div class="table-responsive" id="vendorReportsTableDiv"></div>
						</div>
					</div>
				  </div> 
				</div>
			  </div>
			</div>
		 </div>
	  </div>
    </div>
<?php
	require 'inc/footer.php';
?>
  </body>
</html>
