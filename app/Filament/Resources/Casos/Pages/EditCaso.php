<?php

namespace App\Filament\Resources\Casos\Pages;

use App\Filament\Resources\Casos\CasoResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCaso extends EditRecord
{
    protected static string $resource = CasoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CU-013 Actualizar Estado del Caso: cambia el estado y registra el motivo.
            // TODO: registrar en el log de auditoría del sistema cuando exista (ver nota en
            // AdminPanelProvider sobre rmsramos/activitylog); por ahora el motivo queda como
            // Observación firmada por el usuario, que sirve de rastro visible dentro del expediente.
            Action::make('cambiarEstado')
                ->label('Cambiar estado')
                ->icon('heroicon-o-arrow-path')
                ->schema([
                    Select::make('estado')
                        ->options([
                            'Nuevo' => 'Nuevo',
                            'En preparación' => 'En preparación',
                            'En trámite' => 'En trámite',
                            'En espera' => 'En espera',
                            'Suspendido' => 'Suspendido',
                            'Sentenciado' => 'Sentenciado',
                            'Archivado' => 'Archivado',
                            'Cerrado' => 'Cerrado',
                        ])
                        ->default(fn () => $this->record->estado)
                        ->required(),
                    Textarea::make('motivo')
                        ->label('Motivo del cambio')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['estado' => $data['estado']]);

                    $this->record->observaciones()->create([
                        'user_id' => auth()->id(),
                        'texto' => "Cambio de estado a \"{$data['estado']}\": {$data['motivo']}",
                    ]);

                    Notification::make()->title('Estado actualizado')->success()->send();
                }),
            // CU-025 Cerrar Caso.
            Action::make('cerrarCaso')
                ->label('Cerrar caso')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->estado !== 'Cerrado')
                ->action(function () {
                    $motivos = $this->record->motivosQueImpidenCierre();

                    if ($motivos !== []) {
                        Notification::make()
                            ->title('No se puede cerrar el caso')
                            ->body(implode("\n", $motivos))
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->cerrar();

                    Notification::make()->title('Caso cerrado')->success()->send();
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
