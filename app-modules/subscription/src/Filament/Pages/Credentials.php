<?php

namespace Hotash\Subscription\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Hotash\N8n\Models\N8nWorkflow;
use Hotash\N8n\Services\N8nService;
use Illuminate\Support\Facades\Auth;

class Credentials extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Credentials';

    protected static ?string $title = 'Integration Credentials';

    protected static ?string $slug = 'credentials';

    protected string $view = 'subscription::filament.pages.credentials';

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $credentials = $user->userCredential;
            $this->form->fill($credentials ? $credentials->toArray() : []);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Chatwoot Integration')
                    ->description('Automatically generated via FariHa Bot, or customize manually.')
                    ->schema([
                        TextInput::make('chatwoot_access_token')
                            ->label('Chatwoot Access Token')
                            ->password()
                            ->revealable()
                            ->helperText('Automatically provisioned by FariHa Agent Bot. You can customize this if you want to use a different token.')
                            ->placeholder('Automatically generated'),
                    ]),

                Section::make('AI Capabilities')
                    ->description('Required for automated text, voice, and image analysis.')
                    ->schema([
                        TextInput::make('openai_api_key')
                            ->label('OpenAI API Key')
                            ->password()
                            ->revealable()
                            ->required()
                            ->placeholder('Enter your OpenAI API Key (sk-...)'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        if ($user) {
            $user->userCredential()->updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            Notification::make()
                ->title('Credentials Saved')
                ->body('Your API and integration credentials have been saved successfully.')
                ->success()
                ->send();
        }
    }

    public function publishWorkflow(N8nService $n8n): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        // 1. Validate subscription
        if (! $user->activeSubscription()) {
            Notification::make()
                ->title('Inactive Subscription')
                ->body('You must have an active ReplyNat subscription to publish a workflow.')
                ->danger()
                ->send();

            return;
        }

        // 2. Validate Chatwoot account
        if (! $user->chatwootAccount || $user->chatwootAccount->status !== 'active') {
            Notification::make()
                ->title('Chatwoot Not Active')
                ->body('Your Chatwoot workspace account is not active or provisioned yet.')
                ->danger()
                ->send();

            return;
        }

        // 3. Ensure credentials exist
        if (! $user->userCredential || empty($user->userCredential->chatwoot_access_token) || empty($user->userCredential->openai_api_key)) {
            Notification::make()
                ->title('Missing Credentials')
                ->body('Please save Chatwoot and OpenAI credentials first before publishing.')
                ->danger()
                ->send();

            return;
        }

        try {
            // 4. Import and activate
            $workflowId = $n8n->importUserWorkflow($user);

            // 5. Update/Save N8nWorkflow model status
            N8nWorkflow::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'n8n_workflow_id' => $workflowId,
                    'status' => 'active',
                ]
            );

            Notification::make()
                ->title('Workflow Published Successfully')
                ->body('Your n8n automation workflow has been created and activated.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Workflow Publication Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getWorkflowStatus(): string
    {
        $user = Auth::user();
        $workflow = $user ? N8nWorkflow::where('user_id', $user->id)->first() : null;

        return $workflow ? $workflow->status : 'not_created';
    }
}
