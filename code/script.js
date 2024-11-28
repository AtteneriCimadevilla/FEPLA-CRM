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
        var username = $("#login_user").val().trim();
        var password = $("#login_password").val().trim();
        
        if(username === "" || password === "") {
            alert("Por favor, completa todos los campos.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "login.php",
            data: $(this).serialize(),
            success: function(response) {
                if(response === "success") {
                    window.location.href = "home.php";
                } else {
                    alert("Error de inicio de sesión: " + response);
                }
            }
        });
    });

    // Validación del formulario de registro
    $("#registerFormSubmit").submit(function(e) {
        e.preventDefault();
        var username = $("#register_user").val().trim();
        var email = $("#register_email").val().trim();
        var password = $("#register_password").val().trim();
        
        if(username === "" || email === "" || password === "") {
            alert("Por favor, completa todos los campos.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "register.php",
            data: $(this).serialize(),
            success: function(response) {
                if(response === "success") {
                    alert("Registro exitoso. Ahora puedes iniciar sesión.");
                    $("#registerForm").hide();
                    $("#loginForm").show();
                    $("#registerFormSubmit")[0].reset();
                } else {
                    alert("Error de registro: " + response);
                }
            }
        });
    });
});