<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Formatear fecha en español
     */
    public static function formatSpanish($date, $format = 'd/m/Y')
    {
        if (!$date) return null;
        
        $carbon = Carbon::parse($date);
        $carbon->setLocale('es');
        
        return $carbon->format($format);
    }

    /**
     * Formatear fecha y hora en español
     */
    public static function formatSpanishDateTime($date, $format = 'd/m/Y H:i')
    {
        if (!$date) return null;
        
        $carbon = Carbon::parse($date);
        $carbon->setLocale('es');
        
        return $carbon->format($format);
    }

    /**
     * Formatear fecha relativa en español
     */
    public static function formatSpanishRelative($date)
    {
        if (!$date) return null;
        
        $carbon = Carbon::parse($date);
        $carbon->setLocale('es');
        
        return $carbon->diffForHumans();
    }

    /**
     * Obtener nombre del mes en español
     */
    public static function getSpanishMonthName($month)
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $months[$month] ?? '';
    }

    /**
     * Obtener nombre del día en español
     */
    public static function getSpanishDayName($dayOfWeek)
    {
        $days = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];
        
        return $days[$dayOfWeek] ?? '';
    }
}
