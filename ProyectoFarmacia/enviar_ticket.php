<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

header('Content-Type: application/json'); // Configurar la respuesta como JSON

// ✅ Verificar si se recibió el archivo correctamente
if (!isset($_FILES["ticketPDF"]) || $_FILES["ticketPDF"]["error"] != UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "message" => "Error: No se recibió el archivo PDF correctamente."]);
    exit;
}

// ✅ Guardar el PDF con un nombre único
$nombreArchivo = uniqid("ticket_", true) . ".pdf";
$rutaArchivo = "tickets/" . $nombreArchivo;
move_uploaded_file($_FILES["ticketPDF"]["tmp_name"], $rutaArchivo);

$mail = new PHPMailer(true);

try {
    // ✅ Configurar el correo SMTP
    $mail->isSMTP();
    $mail->Host = "smtp.tudominio.com";  
    $mail->SMTPAuth = true;
    $mail->Username = "tuemail@example.com";  
    $mail->Password = "tucontraseña";  
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    // ✅ Configurar los destinatarios
    $destinatario = isset($_POST["correo"]) ? $_POST["correo"] : "cliente@example.com"; 
    $mail->setFrom("tuemail@example.com", "Farma-Patito");
    $mail->addAddress($destinatario);
    
    // ✅ Adjuntar el PDF correctamente
    if (file_exists($rutaArchivo)) {
        $mail->addAttachment($rutaArchivo);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: El archivo PDF no se encontró después de guardarlo."]);
        exit;
    }

    // ✅ Configurar el contenido del correo
    $mail->Subject = "Ticket de Compra";
    $mail->Body = "Adjunto el ticket de tu compra.";

    // ✅ Enviar el correo y responder
    $mail->send();
    echo json_encode(["status" => "success", "message" => "Correo enviado correctamente con el PDF adjunto."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}
?>