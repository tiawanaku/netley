<?php

namespace App\Filament\Resources\Clientes\Concerns;

use Filament\Notifications\Notification;

trait GeneratesStaffAccess
{
    protected function generarAccesoStaffSiCorresponde(): void
    {
        if (! $this->record->necesitaAccesoStaff()) {
            return;
        }

        $passwordTemporal = $this->record->generarAccesoStaff();

        Notification::make()
            ->title('Acceso al panel generado')
            ->body("Usuario: {$this->record->correo}\nContraseña temporal: {$passwordTemporal}\n\nCompártela ahora, no se podrá volver a mostrar.")
            ->success()
            ->persistent()
            ->send();
    }
}
