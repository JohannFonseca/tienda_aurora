<?php
// administrador/gestionar_usuarios.php

// Iniciar la sesión al principio del script
session_start();

// Opcional: Redirigir si el usuario no está autenticado o no es admin
// if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
//     header('Location: ../login_admin.php'); // Asume que tienes un login_admin.php en la raíz del proyecto
//     exit();
// }

// Habilitar reporte de errores para depuración (¡QUITAR EN PRODUCCIÓN!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Rutas a los archivos necesarios
// db.php está un nivel arriba de 'admin/'
require_once '../db.php';
// GestorUsuarios.php está en 'classes/' que está un nivel arriba de 'admin/'
require_once 'classes/GestorUsuarios.php';

$gestorUsuarios = new GestorUsuarios($pdo);
// Para cargar la tabla inicial con PHP (útil como fallback o para depuración)
$usuarios = $gestorUsuarios->obtenerUsuarios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        /* CSS general para el panel de administración */
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #34495e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar .brand {
            font-size: 1.8em;
            font-weight: bold;
            color: white;
            text-decoration: none;
            margin-right: auto;
        }
        .container-wrapper {
            display: flex;
            flex-grow: 1;
            width: 100%;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ecf0f1;
            font-size: 1.8em;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 15px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li {
            margin-bottom: 10px;
        }
        .sidebar ul li a {
            display: block;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .sidebar ul li a:hover {
            background-color: #34495e;
            transform: translateX(5px);
        }
        .sidebar ul li a.active {
            background-color: #007bff;
            font-weight: bold;
        }
        .main-content {
            flex-grow: 1;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            margin: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .form-container {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 30px;
        }
        .form-container h3 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        textarea,
        select,
        input[type="number"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .data-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        .data-table tr:hover {
            background-color: #f2f2f2;
        }
        .data-table .action-button {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            margin-right: 5px;
        }
        .data-table .action-button:hover {
            background-color: #0056b3;
        }
        .data-table .edit-button {
            background-color: #ffc107;
        }
        .data-table .edit-button:hover {
            background-color: #e0a800;
        }
        .data-table .delete-button {
            background-color: #dc3545;
        }
        .data-table .delete-button:hover {
            background-color: #c82333;
        }
        .logout-button {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            margin-top: auto;
            display: block;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: #d32f2f;
        }

        /* Estilos para el Modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Permanece en su lugar */
            z-index: 1000; /* Se sitúa por encima de todo */
            left: 0;
            top: 0;
            width: 100%; /* Ancho completo */
            height: 100%; /* Alto completo */
            overflow: auto; /* Habilitar scroll si es necesario */
            background-color: rgba(0,0,0,0.6); /* Fondo semi-transparente */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto; /* Margen automático para centrado */
            padding: 30px;
            border: 1px solid #888;
            width: 80%; /* Podrías ajustar el ancho */
            max-width: 500px; /* Ancho máximo */
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
        }

        .modal-content h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        /* Ajustes para los inputs dentro del modal */
        .modal-content input[type="text"],
        .modal-content input[type="password"],
        .modal-content select {
            width: calc(100% - 22px); /* Mantener el padding */
        }

        .modal-content input[type="submit"] {
            background-color: #007bff; /* Un azul para guardar */
            width: 100%;
        }

        .modal-content input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="../index.php" class="brand">Aurora Boutique</a>
    </nav>
    <div class="container-wrapper">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="gestionar_usuarios.php" class="nav-link active"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
                    <li><a href="gestionar_pedidos.php" class="nav-link"><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</a></li>
                    <li><a href="gestionar_productos.php" class="nav-link"><i class="fas fa-box-open"></i> Gestión de Productos</a></li>
                    <li><a href="ver_ventas.php" class="nav-link"><i class="fas fa-chart-line"></i> Ver Ventas</a></li>
                </ul>
            </nav>
            <a href="../logout_admin.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <div class="main-content">
            <h2>Gestión de Usuarios</h2>
            <div class="form-container">
                <h3>Registrar Nuevo Usuario</h3>
                <form id="form-registro-usuario">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre completo">

                    <label for="clave">Clave:</label>
                    <input type="password" id="clave" name="clave" required placeholder="Ingrese la clave">

                    <label for="tipo_usuario_select">Tipo de Usuario:</label>
                    <select id="tipo_usuario_select" name="tipo_usuario_select" required>
                        <option value="">-- Seleccione un tipo --</option>
                        <option value="1">Administrador</option>
                        <option value="2">Cliente</option>
                        <option value="3">Personal de Envíos</option>
                    </select>

                    <input type="submit" value="Registrar Usuario">
                </form>
            </div>
            <div class="user-list">
                <h3>Usuarios Existentes</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-table-body">
                        <?php
                        // Renderizar usuarios obtenidos por PHP para la carga inicial
                        if (is_array($usuarios) && !isset($usuarios['error'])) {
                            foreach ($usuarios as $usuario) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($usuario['id']) . '</td>';
                                echo '<td>' . htmlspecialchars($usuario['nombre']) . '</td>';
                                // ¡Asegúrate de que esta columna exista en el resultado del SP!
                                echo '<td>' . htmlspecialchars($usuario['nombre_tipo_usuario']) . '</td>';
                                echo '<td>';
                                echo '<button class="action-button edit-button" data-id="' . htmlspecialchars($usuario['id']) . '" data-action="edit-user">Editar</button>';
                                echo '<button class="action-button delete-button" data-id="' . htmlspecialchars($usuario['id']) . '" data-action="delete-user">Eliminar</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } elseif (isset($usuarios['error'])) {
                            echo '<tr><td colspan="4" style="color:red;">' . htmlspecialchars($usuarios['error']) . '</td></tr>';
                        } else {
                            echo '<tr><td colspan="4">No hay usuarios disponibles.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditModal">&times;</span>
            <h3>Editar Usuario</h3>
            <form id="form-editar-usuario">
                <input type="hidden" id="edit-user-id" name="id">

                <label for="edit-nombre">Nombre:</label>
                <input type="text" id="edit-nombre" name="nombre" required>

                <label for="edit-clave">Nueva Clave (dejar en blanco para no cambiar):</label>
                <input type="password" id="edit-clave" name="clave" placeholder="Dejar en blanco para no cambiar">

                <label for="edit-tipo_usuario_select">Tipo de Usuario:</label>
                <select id="edit-tipo_usuario_select" name="tipo_usuario_select" required>
                    <option value="1">Administrador</option>
                    <option value="2">Cliente</option>
                    <option value="3">Personal de Envíos</option>
                </select>

                <input type="submit" value="Guardar Cambios">
            </form>
        </div>
    </div>

    <script>
        // Referencias al modal y sus elementos
        const editUserModal = document.getElementById('editUserModal');
        const closeEditModalButton = document.getElementById('closeEditModal');
        const formEditarUsuario = document.getElementById('form-editar-usuario');
        const editUserId = document.getElementById('edit-user-id');
        const editNombre = document.getElementById('edit-nombre');
        const editClave = document.getElementById('edit-clave');
        const editTipoUsuarioSelect = document.getElementById('edit-tipo_usuario_select');

        // Función para cerrar el modal
        function closeEditModal() {
            editUserModal.style.display = 'none';
            formEditarUsuario.reset(); // Limpiar el formulario al cerrar
            editUserId.value = ''; // Asegurarse de limpiar el ID oculto
        }

        // Event listener para cerrar el modal haciendo clic en la X
        closeEditModalButton.onclick = closeEditModal;

        // Cerrar el modal si se hace clic fuera de él
        window.onclick = function(event) {
            if (event.target == editUserModal) {
                closeEditModal();
            }
        }

        // Adjuntar eventos a los botones de la tabla de usuarios
        // MOVIDA HACIA ARRIBA para que esté definida antes de usarse
        function attachUserEventListeners() {
            // Botones de Editar
            document.querySelectorAll('#usuarios-table-body .action-button[data-action="edit-user"]').forEach(button => {
                button.onclick = function() {
                    const userId = this.dataset.id;
                    loadUserForEdit(userId); // Llamar a la función para cargar datos y abrir modal
                };
            });

            // Botones de Eliminar
            document.querySelectorAll('#usuarios-table-body .action-button[data-action="delete-user"]').forEach(button => {
                button.onclick = async function() {
                    const userId = this.dataset.id;
                    if (confirm('¿Está seguro de eliminar este usuario? Esta acción es irreversible.')) {
                        await deleteUser(userId);
                    }
                };
            });
        }

        // Función para cargar los datos de un usuario en el formulario de edición
        async function loadUserForEdit(userId) {
            try {
                // Ruta para la API que obtiene un solo usuario por ID
                // Asume que api/users/get_user_by_id.php está en la raíz del dominio
                const response = await fetch('api/users/get_user_by_id.php?id=${userId}');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const userData = await response.json();

                if (userData.error) {
                    alert('Error al cargar datos del usuario: ' + userData.error);
                    return;
                }

                // Rellenar el formulario del modal con los datos del usuario
                editUserId.value = userData.id;
                editNombre.value = userData.nombre;
                editClave.value = ''; // La clave se deja en blanco por seguridad al editar
                editTipoUsuarioSelect.value = userData.id_tipo_usuario; // Asume que la API devuelve 'id_tipo_usuario'

                editUserModal.style.display = 'flex'; // Mostrar el modal (usar 'flex' para centrado CSS)
            } catch (error) {
                console.error('Error al cargar datos para edición:', error);
                alert('Error de comunicación al cargar datos del usuario para edición: ' + error.message);
            }
        }

        // Función para eliminar usuario
        async function deleteUser(userId) {
            try {
                const formData = new FormData();
                formData.append('id', userId);
                // Ruta ajustada para la API
                const response = await fetch('../api/users/delete_user.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    alert('Usuario eliminado con éxito.');
                    loadUsers(); // Recargar la tabla después de eliminar
                } else {
                    alert('Error al eliminar el usuario: ' + result.message);
                }
            } catch (error) {
                console.error('Error al eliminar usuario:', error);
                alert('Error de comunicación al eliminar el usuario.');
            }
        }

        // Función para cargar/recargar usuarios
        async function loadUsers() {
            try {
                // Ruta ajustada para la API
                const response = await fetch('api/users/get_users.php');
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                const result = await response.json();
                const tableBody = document.getElementById('usuarios-table-body');
                tableBody.innerHTML = '';

                if (result.error) {
                    tableBody.innerHTML = `<tr><td colspan="4" style="color:red;">${result.error}</td></tr>`;
                    return;
                }

                if (result.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="4">No hay usuarios disponibles.</td></tr>`;
                    return;
                }

                result.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.nombre}</td>
                        <td>${item.nombre_tipo_usuario}</td>
                        <td>
                            <button class="action-button edit-button" data-id="${item.id}" data-action="edit-user">Editar</button>
                            <button class="action-button delete-button" data-id="${item.id}" data-action="delete-user">Eliminar</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                attachUserEventListeners(); // Volver a adjuntar eventos a los nuevos botones
            } catch (error) {
                console.error("Error al cargar usuarios:", error);
                document.getElementById('usuarios-table-body').innerHTML = `<tr><td colspan="4" style="color:red;">Error al cargar los usuarios: ${error.message}</td></tr>`;
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            // Manejo del formulario de registro de usuario
            document.getElementById('form-registro-usuario').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // Añadir un campo oculto para indicar que es un registro
                formData.append('action', 'register');

                try {
                    const response = await fetch('api/users/process_user.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Usuario registrado con éxito. ID: ' + result.newUserId);
                        this.reset();
                        loadUsers();
                    } else {
                        alert('Error al registrar usuario: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al enviar formulario de usuario:', error);
                    alert('Error de comunicación al registrar usuario.');
                }
            });

            // Manejo del formulario de edición de usuario
            formEditarUsuario.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                // Añadir un campo oculto para indicar que es una edición
                formData.append('action', 'update');

                try {
                    const response = await fetch('../api/users/process_user.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Usuario actualizado con éxito.');
                        closeEditModal(); // Cerrar el modal
                        loadUsers(); // Recargar la tabla
                    } else {
                        alert('Error al actualizar usuario: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error al enviar formulario de edición de usuario:', error);
                    alert('Error de comunicación al actualizar usuario.');
                }
            });

            // Al cargar la página, se adjuntan los eventos a los usuarios ya cargados por PHP
            attachUserEventListeners();
        });
    </script>
</body>
</html>