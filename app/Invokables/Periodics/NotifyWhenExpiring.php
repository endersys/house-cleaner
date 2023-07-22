<?php

namespace App\Invokables\Periodics;

use App\Models\Periodic;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class NotifyWhenExpiring {
    public function __invoke()
    {
        $periodics = Periodic::whereHas(
            'house', fn ($query) => 
            $query->whereStatus('active')
        )
        ->whereCanAlert(1)
        ->get();

        if ($periodics) {
            foreach ($periodics as $periodic) {
                if ($periodic->next_service_date) {
                    if(count_days_between_now_and_date($periodic->next_service_date) === 1) {
                        $recipient = User::first();
                 
                        $recipient->notify(
                            Notification::make()
                                ->title('Próximo serviço chegando.')
                                ->body('O serviço na casa de **' . $periodic->house->owner->name . '** já é amanhã!')
                                ->icon('heroicon-o-exclamation')
                                ->iconColor('warning')
                                ->actions([
                                    Action::make('showHouse')
                                        ->label('Ver casa')
                                        ->url("/admin/houses/" . $periodic->house->id . "/edit")
                                        ->icon('heroicon-o-eye')
                                ])
                                ->toDatabase()
                        );
                    }
                }
            }
        }
    }
}