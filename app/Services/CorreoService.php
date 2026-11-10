<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class CorreoService
{
    /**
     * @param array|null $adjunto Formato: ['data' => $contenidoRaw, 'nombre' => 'archivo.pdf', 'mime' => 'application/pdf']
     */
    public static function enviarCorreo(string $destinatario, string $asunto, string $cuerpoHtml, ?array $adjunto = null): bool
    {
        try {
            Mail::html($cuerpoHtml, function (Message $message) use ($destinatario, $asunto, $adjunto) {
                $message->to($destinatario)
                    ->subject($asunto)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

                // Si viene un adjunto, lo pegamos al correo
                if ($adjunto) {
                    $message->attachData($adjunto['data'], $adjunto['nombre'], [
                        'mime' => $adjunto['mime'] ?? 'application/pdf',
                    ]);
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::error("Error al enviar correo: " . $e->getMessage());
            return false;
        }
    }
}
