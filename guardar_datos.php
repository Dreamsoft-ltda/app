<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario
    $tipoDocumento = $_POST['tipo_documento'];
    $numeroDocumento = $_POST['numero_documento'];
    $digitoVerificacion = $_POST['digito_verificacion'] ?? '';
    $tipoContribuyente = $_POST['tipo_contribuyente'];
    $razonSocial = $_POST['razon_social'] ?? '';
    $departamento = $_POST['departamento'] ?? '';
    $ciudad = $_POST['ciudad'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $correo = $_POST['correo'] ?? '';

    // Crear un array con los datos del formulario
    $datosCliente = array(
        'tipo_documento' => $tipoDocumento,
        'numero_documento' => $numeroDocumento,
        'digito_verificacion' => $digitoVerificacion,
        'tipo_contribuyente' => $tipoContribuyente,
        'razon_social' => $razonSocial,
        'departamento' => $departamento,
        'ciudad' => $ciudad,
        'direccion' => $direccion,
        'correo' => $correo,
    );

    // Si es persona natural, agregar los campos de nombres
    if ($tipoContribuyente == 'persona_natural') {
        $datosCliente['primer_nombre'] = $_POST['primer_nombre'] ?? '';
        $datosCliente['segundo_nombre'] = $_POST['segundo_nombre'] ?? '';
        $datosCliente['primer_apellido'] = $_POST['primer_apellido'] ?? '';
        $datosCliente['segundo_apellido'] = $_POST['segundo_apellido'] ?? '';
    }

    // Validación adicional del lado del servidor
    $errores = array();

    // Validar NIT y dígito de verificación
    if ($tipoDocumento == 'nit') {
        if (!preg_match('/^\d{9,10}$/', $numeroDocumento)) {
            $errores[] = "El NIT debe tener entre 9 y 10 dígitos sin incluir el dígito de verificación.";
        }
        if (!preg_match('/^\d{1}$/', $digitoVerificacion)) {
            $errores[] = "El dígito de verificación debe ser un solo número.";
        }
    } else {
        if (!preg_match('/^\d+$/', $numeroDocumento)) {
            $errores[] = "El número de documento debe contener solo dígitos.";
        }
    }

    // Validar correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    // Si hay errores, mostrarlos y detener el proceso
    if (!empty($errores)) {
        echo "Se encontraron los siguientes errores:<br>";
        foreach ($errores as $error) {
            echo "- " . $error . "<br>";
        }
        exit;
    }

    // Codificar los datos en formato JSON
    $jsonDatosCliente = json_encode($datosCliente, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Generar el nombre del archivo usando el número de documento (NIT)
    $archivo = 'clientes/' . $numeroDocumento . '.json';

    // Crear el directorio 'clientes' si no existe
    if (!is_dir('clientes')) {
        mkdir('clientes', 0777, true);
    }

    // Guardar el JSON en el archivo específico
    if (file_put_contents($archivo, $jsonDatosCliente)) {
        echo "Los datos del cliente se han guardado correctamente en el archivo: $archivo";
    } else {
        echo "Error al guardar los datos del cliente.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
