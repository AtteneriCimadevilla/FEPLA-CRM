document.addEventListener("DOMContentLoaded", () => {
    var popupElement = document.getElementById("popup");
    if (popupElement) {
        var popup = new bootstrap.Modal(popupElement);
        popup.show();

        // Handle closing the modal
        popupElement.addEventListener("hidden.bs.modal", () => {
            // Remove the modal from the DOM after it's hidden
            popupElement.parentNode.removeChild(popupElement);
        });

        // Add event listeners to close buttons
        var closeButtons = popupElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach((button) => {
            button.addEventListener("click", () => {
                popup.hide();
            });
        });
    }

    document.querySelectorAll(".btn-edit, .btn-create").forEach((button) => {
        button.addEventListener("click", () => {
            console.log("Botón presionado:", button);

            const modal = document.getElementById("modalFormacion");
            const dni = button.getAttribute("data-dni");
            document.getElementById("dni_nie").value = dni;

            // Predefinir el curso a 24/25
            document.getElementById("curso").value = "24/25";

            modal.style.display = "block";
        });
    });

    // Cerrar modal
    const closeButton = document.querySelector(".close");
    if (closeButton) {
        closeButton.addEventListener("click", () => {
            document.getElementById("modalFormacion").style.display = "none";
        });
    }

    window.addEventListener("click", (event) => {
        const modal = document.getElementById("modalFormacion");
        if (event.target === modal) modal.style.display = "none";
    });

    // Manejar el envío del formulario de formación
    const formFormacion = document.getElementById("formFormacion");
    if (formFormacion) {
        formFormacion.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("formacion_handler.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Recargar la página para mostrar los cambios
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Ocurrió un error al procesar la solicitud.");
                });
        });
    }
});