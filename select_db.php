<!DOCTYPE html>
<html>
<head>
    <title>Script de series</title>
</head>
<body>
    <h1>Script que busca series en la base de datos</h1>
    <p>Este es un script desarrollado en php que se conecta a una base de datos MySql.</p>
    <h2>Filtrar series</h2>
    <form method="post">
        <label for="filter_date">Filtrar por fecha:</label>
        <input type="datetime-local" id="filter_date" name="filter_date">

        <label for="filter_title">Filtrar por título:</label>
        <input type="text" id="filter_title" name="filter_title">

        <input type="submit" value="Filtrar">
    </form>
    <h2>Resultados</h2>
</body>
</html>

<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "capabilia_prueba";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("La conexión falló: " . $conn->connect_error);
}

// Obtener la fecha actual y el día de la semana
$currentDayOfWeek = date('l');
$currentDateTime = date('Y-m-d H:i:s');

// Consulta a la base de datos
$sql = "SELECT tv_series.title, tv_series_intervals.week_day, tv_series_intervals.show_time
        FROM tv_series
        INNER JOIN tv_series_intervals ON tv_series.id = tv_series_intervals.tv_series_id
        WHERE (tv_series_intervals.week_day >= '$currentDayOfWeek' AND tv_series_intervals.show_time >= '$currentDateTime')
        OR tv_series_intervals.week_day >= '$currentDayOfWeek'
        ORDER BY tv_series_intervals.week_day ASC, tv_series_intervals.show_time ASC
        LIMIT 1";

// Devolucion de la base de datos
$result = $conn->query($sql);

// Verifica que el resultado tenga mas de una fila
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Mostrar el resultado
    echo "La próxima serie a emitirse es " . $row["title"] . " el día " . $row["week_day"] . " a las " . $row["show_time"] . "<br>";
} else {

    // Mostrar el mensaje que no se encontraron series
    echo "No se encontraron series próximas a emitirse hoy.<br>";
}

// Filtrar por fecha específica y título específico si se proporcionan
if(isset($_POST['filter_title']) && isset($_POST['filter_date'])) {

    // Asigna los valores del formulario a variables
    $filterDate = $_POST['filter_date'];
    $filterTitle = $_POST['filter_title'];

    if ($filterDate != "" && $filterTitle != "") {
        // Convertir $filterDate a formato 'l' para que devuelva el dia en texto
        $dayOfWeek = date('l', strtotime($filterDate));
    
        // Convertir $filterDate a formato 'Y-m-d H:i:s' para obtener una fecha que podamos usar para la consulta
        $dateWithTime = date('Y-m-d H:i:s', strtotime($filterDate));
    
        // Consulta a la base de datos
        $sql = "SELECT tv_series.title, tv_series_intervals.week_day, tv_series_intervals.show_time
        FROM tv_series
        INNER JOIN tv_series_intervals ON tv_series.id = tv_series_intervals.tv_series_id
        WHERE tv_series_intervals.week_day >= '$dayOfWeek' 
        AND (tv_series_intervals.show_time > '$dateWithTime' OR tv_series_intervals.show_time = '$dateWithTime')
        AND tv_series.title = '$filterTitle'
        ORDER BY tv_series_intervals.show_time ASC
        LIMIT 1;
        ";
    
        // Devolucion de la base de datos
        $result = $conn->query($sql);
    
        // Verifica que el resultado tenga mas de una fila
        if ($result->num_rows > 0) {
            echo "<h3>¿Esta $filterTitle el dia $dayOfWeek($filterDate)?</h3>";
            while ($row = $result->fetch_assoc()) {
                // Mostrar los resultados filtrados
                echo $row["title"] . " esta el dia " . $row["week_day"] . " a las " . $row["show_time"] . "<br>";
            }
        } else {
            // Mostrar el mensaje que no se encontraron series ese dia o con ese nombre
            echo "No se encontraron series para la fecha $filterDate<br>";
        }
    }
}

// Filtrar por fecha específica
if(isset($_POST['filter_date'])) {
    // Asigna el valor del formulario a su respectiva variable
    $filterDate = $_POST['filter_date'];

    // Convertir $filterDate a formato 'l'
    $dayOfWeek = date('l', strtotime($filterDate));

    // Convertir $filterDate a formato 'Y-m-d H:i:s'
    $dateWithTime = date('Y-m-d H:i:s', strtotime($filterDate));
    
    // Consulta a la base de datos
    $sql = "SELECT tv_series.title, tv_series_intervals.week_day, tv_series_intervals.show_time
    FROM tv_series
    INNER JOIN tv_series_intervals ON tv_series.id = tv_series_intervals.tv_series_id
    WHERE tv_series_intervals.week_day = '$dayOfWeek' 
    AND (tv_series_intervals.show_time > '$dateWithTime' OR tv_series_intervals.show_time = '$dateWithTime')
    ORDER BY tv_series_intervals.show_time ASC
    LIMIT 1;
    ";

    // Devolucion de la base de datos
    $result = $conn->query($sql);

    // Verifica que el resultado tenga mas de una fila
    if ($result->num_rows > 0) {
        echo "<h3>Primera serie el dia $dayOfWeek($filterDate):</h3>";
        while ($row = $result->fetch_assoc()) {
            // Mostrar los resultados filtrados
            echo $row["title"] . " es la primera serie del dia " . $row["week_day"] . " y se transmite a las " . $row["show_time"] . "<br>";
        }
    } else {
        // Mostrar el mensaje que no se encontraron series ese dia
        echo "No se encontraron series para la fecha $filterDate<br>";
    }
}

// Cierra la conexion con la base de datos
$conn->close();
?>