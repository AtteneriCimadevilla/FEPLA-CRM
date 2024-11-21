const inputFilter = document.getElementById('input-filter');
const inputButton = document.getElementById('input-button');

searchButton.addEventListener('click', () => {
    const busqueda = inputFilter.value;
    inputFilter.value = '';
    inputFilter.focus();

// TODO:
// CONECTAR A LA BASE DE DATOS Y BUSCAR POR TODOS LOS CAMPOS DE TODAS LAS EMPRESAS
});

// TODO:
// AÑADIR BOTONES DE EDITAR Y BORRAR