$(document).ready(function () {
    // Load companies
    $.getJSON('obtener_empresas.php', function (data) {
        $.each(data, function (key, value) {
            $('#empresa').append($('<option>').text(value.nombre_comercial).attr('value', value.id));
            $('#empresas-filtro').append($('<option>').text(value.nombre_comercial).attr('value', 'empresa_' + value.id));
        });
    });

    // Load students
    $.getJSON('obtener_alumnos.php', function (data) {
        console.log("Estudiantes recibidos:", data); // Agregar esta línea para depuración
        if (data.length === 0) {
            console.log("No se encontraron estudiantes");
            $('#alumno').append($('<option>').text("No hay estudiantes disponibles").attr('value', ''));
        } else {
            $.each(data, function (key, value) {
                $('#alumno').append($('<option>').text(value.nombre_completo).attr('value', value.dni_nie));
                $('#alumnos-filtro').append($('<option>').text(value.nombre_completo).attr('value', 'alumno_' + value.dni_nie));
            });
        }
    }).fail(function ( textStatus, errorThrown) {
        console.log("Error al obtener estudiantes:", textStatus, errorThrown);
    });

    // Handle form submission
    $('#activityForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'procesar_actividad.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#activityForm')[0].reset();
                } else {
                    $('#message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#message').html('<div class="alert alert-danger">Error al procesar la solicitud.</div>');
            }
        });
    });

    // Handle filtering
    $('#filtrarBtn').click(function () {
        var selectedValue = $('#filtro').val();
        if (selectedValue) {
            var [type, id] = selectedValue.split('_');
            if (type === 'empresa') {
                window.location.href = 'empresa.php?id=' + id;
            } else if (type === 'alumno') {
                window.location.href = 'alumno.php?dni=' + id;
            }
        } else {
            alert('Por favor, seleccione una empresa o un alumno para filtrar.');
        }
    });
});