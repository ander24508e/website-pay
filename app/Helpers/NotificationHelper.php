<?php

namespace App\Helpers;

class NotificationHelper
{
    /**
     * Guarda una notificación de éxito
     * @param string $message
     */
    public static function success($message)
    {
        session()->flash('success', $message);
    }

    /**
     * Guarda una notificación de error
     * @param string $message
     */
    public static function error($message)
    {
        session()->flash('error', $message);
    }

    /**
     * Guarda una notificación de advertencia
     * @param string $message
     */
    public static function warning($message)
    {
        session()->flash('warning', $message);
    }

    /**
     * Guarda una notificación informativa
     * @param string $message
     */
    public static function info($message)
    {
        session()->flash('info', $message);
    }
}