<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupportRequest;
use App\Models\SupportMessage;

class CloseInactiveSupportRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:close-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra solicitudes de soporte por inactividad del usuario y libera al asesor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ⏱️ minutos máximos de inactividad del USUARIO
        $timeoutMinutes = 20;

        $expiredRequests = SupportRequest::where('status', 'active')
            ->whereNotNull('advisor_id')
            ->whereNotNull('last_user_message_at')
            ->where('last_user_message_at', '<=', now()->subMinutes($timeoutMinutes))
            ->get();

        if ($expiredRequests->isEmpty()) {
            $this->info('No hay solicitudes inactivas.');
            return Command::SUCCESS;
        }

        foreach ($expiredRequests as $supportRequest) {
            $supportRequest->update([
                'status'     => 'closed',
                'advisor_id'=> null,
                'closed_at'  => now(),
            ]);

            SupportMessage::create([
                'support_request_id' => $supportRequest->id,
                'sender_type'        => 'system',
                'message'            => 'El chat se cerró automáticamente por inactividad del usuario.',
            ]);
        }

        $this->info('Solicitudes inactivas cerradas correctamente.');

        return Command::SUCCESS;
    }
}
