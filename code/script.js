$(document).ready(function () {
    $("#showRegister").click(function (e) {
        e.preventDefault();
        $("#loginForm").hide();
        $("#registerForm").show();
    });

    $("#showLogin").click(function (e) {
        e.preventDefault();
        $("#registerForm").hide();
        $("#loginForm").show();
    });

    // Login form validation and submission
    $("#loginFormSubmit").submit(function (e) {
        e.preventDefault();
        var email = $("#login_user").val().trim();
        var password = $("#login_password").val().trim();

        if (email === "" || password === "") {
            alert("Por favor, completa todos los campos.");
            return;
        }

        var $submitButton = $(this).find('button[type="submit"]');
        $submitButton.prop('disabled', true).text('Iniciando sesión...');

        $.ajax({
            type: "POST",
            url: "login.php", // Changed from conexion.php to login.php
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = "home.php";
                } else {
                    alert("Error de inicio de sesión: " + response.message);
                    console.error("Login error:", response);
                }
            },
            error: function (xhr, status, error) {
                alert("Error en la conexión. Por favor, intenta de nuevo más tarde.");
                console.error("AJAX error:", status, error);
                console.log("Response:", xhr.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).text('Iniciar sesión');
            }
        });
    });

    // Registration form validation and submission
    $("#registerFormSubmit").submit(function (e) {
        e.preventDefault();
        var dni = $("#register_dni").val().trim();
        var nombre = $("#register_nombre").val().trim();
        var apellido1 = $("#register_apellido1").val().trim();
        var email = $("#register_email").val().trim();

        if (dni === "" || nombre === "" || apellido1 === "" || email === "") {
            alert("Por favor, completa todos los campos obligatorios.");
            return;
        }

        var $submitButton = $(this).find('button[type="submit"]');
        $submitButton.prop('disabled', true).text('Registrando...');

        $.ajax({
            type: "POST",
            url: "register.php",
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    $("#registerForm").hide();
                    $("#loginForm").show();
                    $("#registerFormSubmit")[0].reset();
                } else {
                    alert("Error de registro: " + response.message);
                    console.error("Registration error:", response);
                }
            },
            error: function (xhr, status, error) {
                alert("Error en la conexión. Por favor, intenta de nuevo más tarde.");
                console.error("AJAX error:", status, error);
                console.log("Response:", xhr.responseText);
            },
            complete: function () {
                $submitButton.prop('disabled', false).text('Registrarse');
            }
        });
    });
});