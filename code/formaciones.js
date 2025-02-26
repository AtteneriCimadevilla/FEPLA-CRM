// Funciones para gestionar formaciones
function crearFormacion(tipo, id) {
    let url = 'crear_formacion.php?';
    if (tipo === 'empresa') {
        url += `tipo=empresa&id_empresa=${encodeURIComponent(id)}`;
    } else {
        url += `tipo=alumno&dni_nie=${encodeURIComponent(id)}`;
    }
    window.open(url, 'CrearFormacion', 'width=800,height=600,resizable=yes,scrollbars=yes');
}

function editarFormacion(dni, idEmpresa) {
    const url = `crear_formacion.php?dni_nie=${encodeURIComponent(dni)}&id_empresa=${encodeURIComponent(idEmpresa)}&edit=1`;
    window.open(url, 'EditarFormacion', 'width=800,height=600,resizable=yes,scrollbars=yes');
}

function borrarFormacion(dni, idEmpresa, curso) {
    if (!confirm('¿Está seguro de que desea eliminar esta formación?')) {
        return;
    }

    const formData = new FormData();
    formData.append('dni_nie', dni);
    formData.append('id_empresa', idEmpresa);
    formData.append('curso', curso);

    fetch('borrar_formacion.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Si estamos en la página de formaciones, recargamos
                if (window.location.pathname.includes('formaciones.php')) {
                    location.reload();
                } else {
                    // Si estamos en otra página, podemos actualizar de otra manera
                    // o recargar también
                    location.reload();
                }
            } else {
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
}

// Función para actualizar formación
function actualizarFormacion(formData) {
    return fetch('formacion_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                return true;
            } else {
                throw new Error(data.message);
            }
        });
}

// Función para manejar el envío del formulario de formación
function manejarFormularioFormacion(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    fetch('formacion_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Cerrar la ventana actual y recargar la ventana padre
                window.opener.location.reload();
                window.close();
            } else {
                alert('Error: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
}

// Agregar event listeners cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formacionForm');
    if (form) {
        form.addEventListener('submit', manejarFormularioFormacion);
    }
});