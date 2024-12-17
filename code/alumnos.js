
document.addEventListener('DOMContentLoaded', function () {
    var popupElement = document.getElementById('popup');
    var popup = new bootstrap.Modal(popupElement);
    popup.show();

    // Handle closing the modal
    popupElement.addEventListener('hidden.bs.modal', function () {
        // Remove the modal from the DOM after it's hidden
        popupElement.parentNode.removeChild(popupElement);
    });

    // Add event listeners to close buttons
    var closeButtons = popupElement.querySelectorAll('[data-bs-dismiss="modal"]');
    closeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            popup.hide();
        });
    });
});

document.querySelectorAll('.btn-edit, .btn-create').forEach(button => {
    button.addEventListener('click', () => {
        console.log('Botón presionado:', button);

        const modal = document.getElementById('modalFormacion');
        const dni = button.getAttribute('data-dni');
        document.getElementById('dni_nie').value = dni;

        modal.style.display = 'block';

        // Preseleccionar valores si es edición
        if (button.classList.contains('btn-edit')) {
            const row = button.closest('tr');
            const empresa = row.querySelector('td:nth-child(5)').innerText;
            const curso = row.querySelector('td:nth-child(6)').innerText;

            document.getElementById('empresa').value = empresa !== 'Sin asignar' ? empresa : '';
            document.getElementById('curso').value = curso !== 'Sin asignar' ? curso : '';
        } else {
            document.getElementById('empresa').value = '';
            document.getElementById('curso').value = '';
        }

        modal.style.display = 'block';
    });
});

// Cerrar modal
document.querySelector('.close').addEventListener('click', () => {
    document.getElementById('modalFormacion').style.display = 'none';
});
window.addEventListener('click', event => {
    const modal = document.getElementById('modalFormacion');
    if (event.target === modal) modal.style.display = 'none';
});

function crearFormacion() {
    document.getElementById("formularioFormacion").style.display = "block";
}

function editarFormacion(id) {
    fetch(`backend.php?action=getFormacion&id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("empresaAsignada").textContent = data.empresa;
            // Abre el formulario de edición
        })
        .catch(error => console.error("Error al obtener la formación:", error));
}
