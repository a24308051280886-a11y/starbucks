<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$conexion = new mysqli("127.0.0.1", "root", "", "starbucks");

if ($conexion->connect_error) {
    echo json_encode(["exito" => false, "mensaje" => "Error de conexión a MySQL"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    if ($accion == "registro") {
        $nombre = $conexion->real_escape_string(trim($_POST['nombre']));
        $correo = $conexion->real_escape_string(trim($_POST['correo']));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, correo, password) VALUES ('$nombre', '$correo', '$password')";

        if ($conexion->query($sql) === TRUE) {
            echo json_encode(["exito" => true, "nombre" => $nombre, "correo" => $correo]);
        } else {
            echo json_encode(["exito" => false, "mensaje" => "Ese correo ya está registrado."]);
        }
    } 
    elseif ($accion == "login") {
        $correo = $conexion->real_escape_string(trim($_POST['correo']));
        $password = $_POST['password'];

        $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if (password_verify($password, $fila['password'])) {
                echo json_encode(["exito" => true, "nombre" => $fila['nombre'], "correo" => $fila['correo']]);
            } else {
                echo json_encode(["exito" => false, "mensaje" => "Contraseña incorrecta."]);
            }
        } else {
            echo json_encode(["exito" => false, "mensaje" => "Correo no registrado. Regístrate primero."]);
        }
    }
}

$conexion->close();
?>