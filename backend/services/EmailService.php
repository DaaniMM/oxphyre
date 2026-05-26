<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private function buildMailer(): PHPMailer
    {
        $autoload = ROOT_PATH . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new \RuntimeException('PHPMailer no disponible. Ejecuta: composer require phpmailer/phpmailer');
        }
        require_once $autoload;

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST']     ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME']  ?? '';
        $mail->Password   = $_ENV['MAIL_PASSWORD']  ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(
            $_ENV['MAIL_FROM']      ?? $_ENV['MAIL_USERNAME'] ?? '',
            $_ENV['MAIL_FROM_NAME'] ?? 'Oxphyre'
        );
        return $mail;
    }

    public function sendVerification(string $toEmail, string $toName, string $token): void
    {
        $verifyUrl = (APP_URL) . '/verify?token=' . urlencode($token);

        $body = $this->templateVerification($toName, $verifyUrl);

        try {
            $mail = $this->buildMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Verifica tu cuenta en Oxphyre';
            $mail->Body    = $body;
            $mail->AltBody = "Hola {$toName},\n\nVerifica tu email accediendo a este enlace:\n{$verifyUrl}\n\nEl enlace caduca en 24 horas.";
            $mail->send();
        } catch (\Exception $e) {
            error_log('EmailService::sendVerification error: ' . $e->getMessage());
        }
    }

    public function sendPasswordReset(string $toEmail, string $toName, string $token): void
    {
        $resetUrl = (APP_URL) . '/reset?token=' . urlencode($token);

        $body = $this->templatePasswordReset($toName, $resetUrl);

        try {
            $mail = $this->buildMailer();
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Recupera tu contraseña de Oxphyre';
            $mail->Body    = $body;
            $mail->AltBody = "Hola {$toName},\n\nRestablece tu contraseña en:\n{$resetUrl}\n\nEl enlace caduca en 1 hora. Si no lo solicitaste, ignora este mensaje.";
            $mail->send();
        } catch (\Exception $e) {
            error_log('EmailService::sendPasswordReset error: ' . $e->getMessage());
        }
    }

    public function sendContactNotification(array $data, int $messageId): bool
    {
        try {
            $mail = $this->buildMailer();
            $toEmail = $_ENV['CONTACT_TO_EMAIL'] ?? 'support@oxphyre.com';
            $toName  = $_ENV['CONTACT_TO_NAME']  ?? 'Oxphyre Support';
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo((string) $data['email'], (string) $data['name']);
            $mail->isHTML(true);
            $mail->Subject = 'Nuevo mensaje de contacto en Oxphyre #' . $messageId;
            $mail->Body    = $this->templateContactNotification($data, $messageId);
            $mail->AltBody = $this->plainContactNotification($data, $messageId);
            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('EmailService::sendContactNotification error: ' . $e->getMessage());
            return false;
        }
    }

    private function templateVerification(string $name, string $url): string
    {
        $name = htmlspecialchars($name);
        $url  = htmlspecialchars($url);
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0a0800;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0800;padding:40px 20px;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0" style="background:#111009;border:1px solid rgba(254,179,84,0.18);border-radius:12px;overflow:hidden;">
        <tr><td style="padding:40px 48px;">
          <p style="font-family:'Courier New',monospace;font-size:11px;color:#FEB354;letter-spacing:0.3em;text-transform:uppercase;margin:0 0 28px;">&#9711; OXPHYRE</p>
          <h1 style="font-family:Georgia,serif;color:#ffffff;font-size:26px;font-weight:400;margin:0 0 20px;line-height:1.2;">Verifica tu dirección<br>de email</h1>
          <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.6;margin:0 0 12px;">Hola {$name},</p>
          <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.6;margin:0 0 32px;">Gracias por crear tu cuenta en Oxphyre. Solo falta un paso: confirma tu dirección de email haciendo clic en el botón.</p>
          <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
            <tr><td align="center" style="background:linear-gradient(135deg,#FEB354,#ffcc80);border-radius:8px;">
              <a href="{$url}" style="display:inline-block;color:#0a0800;font-family:Arial,sans-serif;font-size:15px;font-weight:700;padding:14px 36px;text-decoration:none;border-radius:8px;">Verificar email →</a>
            </td></tr>
          </table>
          <p style="color:rgba(255,255,255,0.35);font-size:12px;line-height:1.5;margin:0 0 28px;">Si el botón no funciona, copia y pega este enlace en tu navegador:<br><span style="color:rgba(254,179,84,0.6);word-break:break-all;">{$url}</span></p>
          <p style="color:rgba(255,255,255,0.25);font-size:12px;margin:0 0 28px;">El enlace caduca en 24 horas. Si no creaste esta cuenta, ignora este mensaje.</p>
          <hr style="border:none;border-top:1px solid rgba(254,179,84,0.08);margin:0 0 20px;">
          <p style="font-family:'Courier New',monospace;font-size:10px;color:rgba(255,255,255,0.2);text-transform:uppercase;letter-spacing:0.3em;margin:0;text-align:center;">oxphyre.com</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function templateContactNotification(array $data, int $messageId): string
    {
        $safe = [];
        foreach ($data as $key => $value) {
            $safe[$key] = nl2br(htmlspecialchars((string) $value));
        }
        $privacy    = !empty($data['privacy_accepted'])  ? 'Sí' : 'No';
        $commercial = !empty($data['commercial_contact']) ? 'Sí' : 'No';

        // Nota de compatibilidad email:
        // - bgcolor en <body> y <td> cubre Outlook/Gmail que ignoran background CSS en body.
        // - Todos los colores son hexadecimales sólidos; rgba() no se renderiza en Gmail/Outlook.
        //   Equivalentes computados sobre fondo #111009:
        //   rgba(255,255,255,0.78) → #c8c8c8   rgba(255,255,255,0.84) → #d6d6d6
        //   rgba(254,179,84,0.18)  → #2c2210   rgba(254,179,84,0.16)  → #272010
        //   rgba(255,255,255,0.04) → #161410   (bloque mensaje, antes se veía gris)
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body bgcolor="#0a0800" style="margin:0;padding:0;background:#0a0800;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#0a0800" style="background:#0a0800;padding:32px 18px;">
    <tr><td align="center" bgcolor="#0a0800" style="background:#0a0800;">
      <table width="620" cellpadding="0" cellspacing="0" bgcolor="#111009" style="background:#111009;border:1px solid #2c2210;border-radius:12px;overflow:hidden;">
        <tr><td bgcolor="#111009" style="padding:34px 40px;background:#111009;">
          <p style="font-family:'Courier New',monospace;font-size:11px;color:#FEB354;letter-spacing:0.3em;text-transform:uppercase;margin:0 0 24px;">OXPHYRE CONTACTO</p>
          <h1 style="color:#ffffff;font-size:24px;font-weight:700;margin:0 0 20px;line-height:1.25;">Nuevo mensaje #{$messageId}</h1>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Nombre:</strong> {$safe['name']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Apellidos/negocio:</strong> {$safe['business_or_lastname']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Email:</strong> {$safe['email']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Teléfono:</strong> {$safe['phone']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Tipo:</strong> {$safe['inquiry_type']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Plan:</strong> {$safe['plan_interest']}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 8px;"><strong style="color:#e0e0e0;">Privacidad:</strong> {$privacy}</p>
          <p style="color:#c8c8c8;font-size:15px;line-height:1.6;margin:0 0 20px;"><strong style="color:#e0e0e0;">Contacto comercial:</strong> {$commercial}</p>
          <hr style="border:none;border-top:1px solid #2c2210;margin:0 0 20px;">
          <p style="font-family:'Courier New',monospace;font-size:11px;color:#FEB354;letter-spacing:0.2em;text-transform:uppercase;margin:0 0 10px;">Mensaje</p>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td bgcolor="#161410" style="padding:18px;border:1px solid #272010;border-radius:8px;background:#161410;color:#d6d6d6;font-size:15px;line-height:1.65;">{$safe['message']}</td></tr>
          </table>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function plainContactNotification(array $data, int $messageId): string
    {
        return "Nuevo mensaje de contacto #{$messageId}\n"
            . "Nombre: {$data['name']}\n"
            . "Apellidos/negocio: {$data['business_or_lastname']}\n"
            . "Email: {$data['email']}\n"
            . "Telefono: {$data['phone']}\n"
            . "Tipo: {$data['inquiry_type']}\n"
            . "Plan: {$data['plan_interest']}\n\n"
            . $data['message'];
    }

    private function templatePasswordReset(string $name, string $url): string
    {
        $name = htmlspecialchars($name);
        $url  = htmlspecialchars($url);
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0a0800;font-family:Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0a0800;padding:40px 20px;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0" style="background:#111009;border:1px solid rgba(254,179,84,0.18);border-radius:12px;overflow:hidden;">
        <tr><td style="padding:40px 48px;">
          <p style="font-family:'Courier New',monospace;font-size:11px;color:#FEB354;letter-spacing:0.3em;text-transform:uppercase;margin:0 0 28px;">&#9711; OXPHYRE</p>
          <h1 style="font-family:Georgia,serif;color:#ffffff;font-size:26px;font-weight:400;margin:0 0 20px;line-height:1.2;">Recupera tu<br>contraseña</h1>
          <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.6;margin:0 0 12px;">Hola {$name},</p>
          <p style="color:rgba(255,255,255,0.7);font-size:15px;line-height:1.6;margin:0 0 32px;">Recibimos una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el botón para crear una nueva.</p>
          <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
            <tr><td align="center" style="background:linear-gradient(135deg,#FEB354,#ffcc80);border-radius:8px;">
              <a href="{$url}" style="display:inline-block;color:#0a0800;font-family:Arial,sans-serif;font-size:15px;font-weight:700;padding:14px 36px;text-decoration:none;border-radius:8px;">Restablecer contraseña →</a>
            </td></tr>
          </table>
          <p style="color:rgba(255,255,255,0.35);font-size:12px;line-height:1.5;margin:0 0 28px;">Si el botón no funciona, copia y pega este enlace:<br><span style="color:rgba(254,179,84,0.6);word-break:break-all;">{$url}</span></p>
          <p style="color:rgba(255,255,255,0.25);font-size:12px;margin:0 0 28px;">El enlace caduca en 1 hora. Si no solicitaste este cambio, ignora este mensaje — tu contraseña no se modificará.</p>
          <hr style="border:none;border-top:1px solid rgba(254,179,84,0.08);margin:0 0 20px;">
          <p style="font-family:'Courier New',monospace;font-size:10px;color:rgba(255,255,255,0.2);text-transform:uppercase;letter-spacing:0.3em;margin:0;text-align:center;">oxphyre.com</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
