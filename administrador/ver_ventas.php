<?php
// administrador/ver_ventas.php
require_once '../config_sesion.php';

// Redirección si no es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 1) {
    header("Location: ../login_admin.php");
    exit();
}

require_once '../db.php';
require_once 'classes/GestorPedidos.php';

$gestorPedidos = new GestorPedidos($pdo);
// MODIFICACIÓN CLAVE AQUÍ: Llamar al método existente y disponible
$ventas = $gestorPedidos->obtenerPedidosAdministrador();
?>
<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas - Admin</title>
    <link rel="icon" href="imagenes/AB.ico" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        /* CSS general para el panel de administración (igual que en las otras páginas) */
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
                    <li><a href="gestionar_usuarios.php" class="nav-link"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
                    <li><a href="gestionar_pedidos.php" class="nav-link"><i class="fas fa-clipboard-list"></i> Gestión de Pedidos</a></li>
                    <li><a href="gestionar_productos.php" class="nav-link"><i class="fas fa-box-open"></i> Gestión de Productos</a></li>
                    <li><a href="ver_ventas.php" class="nav-link active"><i class="fas fa-chart-line"></i> Ver Ventas</a></li>
                </ul>
            </nav>
            <a href="../logout_admin.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>

        <div class="main-content">
            <h2>Reporte de Ventas</h2>
            <p>Aquí se muestra un resumen de las ventas (pedidos).</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Código Pedido</th>
                        <th>Cliente (ID)</th>
                        <th>Fecha Compra</th>
                        <th>Estado</th>
                        <th>Total Pedido</th>
                    </tr>
                </thead>
                <tbody id="ventas-table-body">
                    <?php
                    // Renderizar ventas (pedidos) obtenidos por PHP
                    // Ahora $ventas contiene objetos, no arrays asociativos.
                    // Necesitarás adaptar los nombres de las propiedades a lo que retorna tu función PostgreSQL 'obtener_pedidos_administrador()'
                    // Los nombres comunes serían 'id', 'codigo_pedido', 'id_cliente', 'nombre_cliente', 'fecha_compra', 'estado_texto', 'total_pedido'
                    // Revisa la definición de tu función SQL para los nombres exactos.
                    if (is_array($ventas) && !empty($ventas)) { // No necesitas verificar 'error' si el método devuelve array vacío en error
                        foreach ($ventas as $venta) {
                            // MODIFICACIÓN CLAVE AQUÍ: Acceder a propiedades del objeto usando ->
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($venta->id) . '</td>';
                            echo '<td>' . htmlspecialchars($venta->codigo_pedido) . '</td>';
                            // Asumiendo que obtener_pedidos_administrador() devuelve 'nombre_cliente' y 'id_cliente'
                            echo '<td>' . htmlspecialchars($venta->id_cliente) . ' - ' . htmlspecialchars($venta->nombre_cliente ?? 'N/A') . '</td>';
                            echo '<td>' . htmlspecialchars($venta->fecha_compra ? (new DateTime($venta->fecha_compra))->format('d/m/Y') : 'N/A') . '</td>';
                            // Asumiendo que obtener_pedidos_administrador() devuelve 'nombre_estado' o similar (ej. 'estado_texto')
                            echo '<td>' . htmlspecialchars($venta->estado_texto ?? 'Desconocido') . '</td>'; // CAMBIADO a 'estado_texto' o el nombre que uses
                            // Asumiendo que obtener_pedidos_administrador() devuelve 'total_pedido'
                            echo '<td>₡' . htmlspecialchars(number_format($venta->total_pedido ?? 0, 2)) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        // Si no hay ventas o hubo un error (obtenerPedidosAdministrador retorna [] en caso de error)
                        echo '<tr><td colspan="6">No hay ventas disponibles o hubo un error al cargarlas.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>