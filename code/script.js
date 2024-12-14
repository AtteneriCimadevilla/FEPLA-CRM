$(document).ready(function() {
    $("#showRegister").click(function(e) {
        e.preventDefault();
        $("#loginForm").hide();
        $("#registerForm").show();
    });

    $("#showLogin").click(function(e) {
        e.preventDefault();
        $("#registerForm").hide();
        $("#loginForm").show();
    });

    // Validación del formulario de inicio de sesión
    $("#loginFormSubmit").submit(function(e) {
        e.preventDefault();
        var email = $("#login_user").val().trim();
        var password = $("#login_password").val().trim();
        
        if(email === "" || password === "") {
            alert("Por favor, completa todos los campos.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "conexion.php",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status === "success") {
                    window.location.href = "home.php";
                } else {
                    alert("Error de inicio de sesión: " + response.message);
                }
            },
            error: function() {
                alert("Error en la conexión. Por favor, intenta de nuevo más tarde.");
            }
        });
    });

    // Validación del formulario de registro
    $("#registerFormSubmit").submit(function(e) {
        e.preventDefault();
        var dni = $("#register_dni").val().trim();
        var nombre = $("#register_nombre").val().trim();
        var apellido1 = $("#register_apellido1").val().trim();
        var email = $("#register_email").val().trim();
        
        if(dni === "" || nombre === "" || apellido1 === "" || email === "") {
            alert("Por favor, completa todos los campos obligatorios.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "register.php",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status === "success") {
                    alert(response.message);
                    $("#registerForm").hide();
                    $("#loginForm").show();
                    $("#registerFormSubmit")[0].reset();
                } else {
                    alert("Error de registro: " + response.message);
                }
            },
            error: function() {
                alert("Error en la conexión. Por favor, intenta de nuevo más tarde.");
            }
        });
    });
});