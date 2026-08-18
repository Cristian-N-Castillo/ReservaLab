<?php

declare(strict_types=1);

namespace App\Services;

use Core\Config;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

/**
 * Servicio de notificaciones por correo.
 *
 * Si existen credenciales SMTP en el .env (MAIL_HOST, MAIL_USERNAME, etc.)
 * envía el correo real mediante PHPMailer. Si no están configuradas, o si
 * el envío falla, deja constancia en storage/logs/mail.log en lugar de
 * interrumpir el flujo que lo invocó (crear una reserva o una observación
 * no debe fallar por un problema de correo).
 */
final class NotificacionService
{
    private string $logPath;

    public function __construct()
    {
        $this->logPath = dirname(__DIR__, 2) . '/storage/logs/mail.log';
    }

    /**
     * Envía un correo de texto plano.
     */
    public function enviar(string $destinatario, string $asunto, string $cuerpo): void
    {
        $html = nl2br(htmlspecialchars($cuerpo));

        $this->despachar($destinatario, $asunto, $html, $cuerpo);
    }

    /**
     * Envía un correo HTML, con una versión de texto plano como respaldo.
     */
    public function enviarHtml(string $destinatario, string $asunto, string $cuerpoHtml, string $cuerpoTexto): void
    {
        $this->despachar($destinatario, $asunto, $cuerpoHtml, $cuerpoTexto);
    }

    private function despachar(string $destinatario, string $asunto, string $cuerpoHtml, string $cuerpoTexto): void
    {
        if ($destinatario === '') {
            return;
        }

        $host = trim((string) Config::get('MAIL_HOST', ''));

        if ($host === '') {
            $this->registrarLog($destinatario, $asunto, $cuerpoTexto, 'SIN CONFIGURAR (modo log)');
            return;
        }

        try {

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = (int) Config::get('MAIL_PORT', 587);
            $mail->SMTPAuth = true;
            $mail->Username = (string) Config::get('MAIL_USERNAME', '');
            $mail->Password = (string) Config::get('MAIL_PASSWORD', '');
            $mail->SMTPSecure = (string) Config::get('MAIL_ENCRYPTION', 'tls');
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(
                (string) Config::get('MAIL_FROM_ADDRESS', $mail->Username),
                (string) Config::get('MAIL_FROM_NAME', 'ReservaLab')
            );

            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpoHtml;
            $mail->AltBody = $cuerpoTexto;

            $mail->send();

            $this->registrarLog($destinatario, $asunto, $cuerpoTexto, 'ENVIADO');

        } catch (PHPMailerException|Throwable $e) {

            $this->registrarLog($destinatario, $asunto, $cuerpoTexto, 'ERROR: ' . $e->getMessage());
        }
    }

    private function registrarLog(string $destinatario, string $asunto, string $cuerpo, string $estado): void
    {
        $directorio = dirname($this->logPath);

        if (!is_dir($directorio)) {
            mkdir($directorio, 0775, true);
        }

        $entrada = sprintf(
            "[%s] (%s) Para: %s | Asunto: %s\n%s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $estado,
            $destinatario,
            $asunto,
            str_repeat('-', 60),
            $cuerpo
        );

        file_put_contents($this->logPath, $entrada, FILE_APPEND | LOCK_EX);
    }
}
