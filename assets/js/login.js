$(document).ready(function(){
	
	// Escuchar el botón de registro
	$('#register').on('click', function(){
		register();
	});
	
	// Escuchar el botón para restablecer la contraseña
	$('#resetPasswordButton').on('click', function(){
		resetPassword();
	});
	
	// Escuchar el botón de inicio de sesión
	$('#login').on('click', function(){
		login();
	});
});


// Función para registrar un nuevo usuario
function register(){
	var registerFullName = $('#registerFullName').val();
	var registerUsername = $('#registerUsername').val();
	var registerPassword1 = $('#registerPassword1').val();
	var registerPassword2 = $('#registerPassword2').val();
	
	$.ajax({
		url: 'model/login/register.php',
		method: 'POST',
		data: {
			registerFullName:registerFullName,
			registerUsername:registerUsername,
			registerPassword1:registerPassword1,
			registerPassword2:registerPassword2,
		},
		success: function(data){
			$('#registerMessage').html(data);
		}
	});
}


// Función para restablecer contraseña
function resetPassword(){
	var resetPasswordUsername = $('#resetPasswordUsername').val();
	var resetPasswordPassword1 = $('#resetPasswordPassword1').val();
	var resetPasswordPassword2 = $('#resetPasswordPassword2').val();
	
	$.ajax({
		url: 'model/login/resetPassword.php',
		method: 'POST',
		data: {
			resetPasswordUsername:resetPasswordUsername,
			resetPasswordPassword1:resetPasswordPassword1,
			resetPasswordPassword2:resetPasswordPassword2,
		},
		success: function(data){
			$('#resetPasswordMessage').html(data);
		}
	});
}


// Función para iniciar sesión como usuario
function login(){
	var loginUsername = $('#loginUsername').val();
	var loginPassword = $('#loginPassword').val();
	
	$.ajax({
		url: 'model/login/checkLogin.php',
		method: 'POST',
		data: {
			loginUsername:loginUsername,
			loginPassword:loginPassword,
		},
		success: function(data){
			$('#loginMessage').html(data);
			
			if(data.indexOf('Redirecting') >= 0){
				window.location = 'index.php';
			}
		}
	});
}