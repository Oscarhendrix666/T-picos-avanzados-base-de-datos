<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Vuelos</title>
    <style>
        /* Estilos generales para el cuerpo y la estructura */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex; /* Flexbox para la estructura de la página */
            flex-direction: column; /* Coloca los elementos en columna */
            min-height: 100vh; /* Altura mínima igual al alto de la ventana */
        }

        /* Encabezado */
        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }

        /* Navegación */
        nav {
            background-color: #333;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex; /* Flexbox para la navegación */
            justify-content: center; /* Centrar los elementos horizontalmente */
        }

        nav ul li {
            margin: 0 10px;
        }

        nav ul li a {
            color: white;
            padding: 15px 20px;
            text-decoration: none;
        }

        nav ul li a:hover {
            background-color: #0056b3;
        }

        /* Contenido principal */
        main {
            padding: 20px;
            text-align: center;
            flex: 1; /* Ocupa el espacio restante disponible */
        }

        /* Pie de página */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Bienvenido al Sistema de Gestión de Vuelos</h1>
    </header>
    <nav>
        <ul>
            <li><a href="PasajerosCRUD.php">Manejo de Pasajeros</a></li>
            <li><a href="VuelosCRUD.php">Gestión de Vuelos</a></li>
            <li><a href="EquipajeCRUD.php">Gestión de equipaje</a></li>
            <li><a href="CheckInCRUD.php">Totem CheckIn</a></li>
            <li><a href="AvionesCRUD.php">Gestión de Aviones</a></li>
            <li><a href="AsientosCRUD.php">Gestión de Asientos</a></li>
            <li><a href="ReservasCRUD.php">Gestión de Reservas</a></li>
        </ul>
    </nav>
    <main>
        <p>Este es el punto de entrada a tu sistema. Selecciona una opción del menú para comenzar.</p>
    </main>
    <footer>
        <p>© 2024 Sistema de Gestión de Vuelos y Aerolínea. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
