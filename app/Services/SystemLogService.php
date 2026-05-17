<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class SystemLogService
{
    /**
     * @param string $action       e.g., 'Crear', 'Editar', 'Eliminar'
     * @param string $module       e.g., 'Clientes', 'Ventas', 'Gastos Operativos'
     * @param string $description  e.g., 'Se eliminó el cliente ID: 5 (Juan Perez)'
     */
    public static function log(string $action, string $module, string $description)
    {
        try {
            SystemLog::create([
                'user_name' => Auth::check() ? Auth::user()->name : 'Sistema',
                'action' => $action,
                'module' => $module,
                'description' => $description,
            ]);
        } catch (\Exception $e) {
            // Silently fail if log can't be created to avoid breaking main flows
            \Log::error('Cannot create SystemLog: ' . $e->getMessage());
        }
    }
}
