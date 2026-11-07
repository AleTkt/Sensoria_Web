<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class CorreoService
{
    public static function enviarCorreo(string $destinatario, string $asunto, string $cuerpoHtml): bool
    {
        try {
            Mail::html($cuerpoHtml, function (Message $message) use ($destinatario, $asunto) {
                $message->to($destinatario)
                    ->subject($asunto)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            return true;
        } catch (\Exception $e) {
            \Log::error("Error al enviar correo: " . $e->getMessage());
            return false;
        }
    }
}
