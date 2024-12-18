document.addEventListener("DOMContentLoaded", () => {
    const btnCreate = document.getElementById("btnCreateProfesor");
    const modal = new bootstrap.Modal(document.getElementById("modalProfesor"));
    const form = document.getElementById("formularioProfesor");
    const btnEditList = document.querySelectorAll(".btn-edit");

    // Abrir modal para crear nuevo profesor
    btnCreate.addEventListener("click", () => {
        form.reset();
        document.getElementById("dni_nie").readOnly = false; // Habilitar DNI/NIE para creación
        modal.show();
    });

    // Abrir modal para editar un profesor existente
    btnEditList.forEach((btn) => {
        btn.addEventListener("click", () => {
            const dni = btn.getAttribute("data-dni");
            fetch(`getProfesor.php?dni_nie=${dni}`)
                .then((response) => response.json())
                .then((data) => {
                    // Prellenar campos del modal con los datos del profesor
                    document.getElementById("dni_nie").value = data.dni_nie;
                    document.getElementById("dni_nie").readOnly = true; // No modificar DNI/NIE
                    document.getElementById("nombre").value = data.nombre;
                    document.getElementById("apellido1").value = data.apellido1;
                    document.getElementById("apellido2").value = data.apellido2;
                    document.getElementById("telefono").value = data.telefono;
                    document.getElementById("email").value = data.email;
                    document.getElementById("tipo_usuario").value = data.tipo_usuario;
                    modal.show();
                })
                .catch((error) => console.error("Error al obtener los datos del profesor:", error));
        });
    });

    // Guardar datos del profesor (crear o actualizar)
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("saveProfesor.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.text())
            .then((result) => {
                console.log(result);
                alert("Datos guardados correctamente.");
                location.reload(); // Recargar página
            })
            .catch((error) => console.error("Error al guardar los datos:", error));
    });
});
