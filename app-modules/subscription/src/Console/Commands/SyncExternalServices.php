<?php

namespace Hotash\Subscription\Console\Commands;

use App\Models\User;
use Hotash\Chatwoot\Jobs\SyncChatwootAccountJob;
use Hotash\N8n\Jobs\SyncN8nWorkflowJob;
use Illuminate\Console\Command;

class SyncExternalServices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:sync-external-services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periodically sync all users Chatwoot accounts and n8n workflows based on subscription status.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting external services synchronization for all users...');

        // $user = User::query()->latest()->first();
        User::query()->chunk(100, function ($users) {
            foreach ($users as $user) {
                dispatch(new SyncChatwootAccountJob($user->id));
                dispatch(new SyncN8nWorkflowJob($user->id));
            }
        });

        $this->info('Synchronization jobs dispatched successfully.');

        return Command::SUCCESS;
    }
}
