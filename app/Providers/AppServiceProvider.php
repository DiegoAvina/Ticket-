<?php

namespace App\Providers;

use App\Domain\Tickets\Events\TicketAssigned;
use App\Domain\Tickets\Events\TicketClosed;
use App\Domain\Tickets\Events\TicketCreated;
use App\Domain\Tickets\Events\TicketMessageAdded;
use App\Domain\Tickets\Events\TicketReopened;
use App\Domain\Tickets\Events\TicketResolved;
use App\Domain\Tickets\Events\TicketSlaBreached;
use App\Domain\Tickets\Events\TicketStatusChanged;
use App\Domain\Tickets\Listeners\SendTicketAssignedNotification;
use App\Domain\Tickets\Listeners\SendTicketClosedNotification;
use App\Domain\Tickets\Listeners\SendTicketCreatedNotification;
use App\Domain\Tickets\Listeners\SendTicketMessageNotification;
use App\Domain\Tickets\Listeners\SendTicketReopenedNotification;
use App\Domain\Tickets\Listeners\SendTicketResolvedNotification;
use App\Domain\Tickets\Listeners\SendTicketSlaNotification;
use App\Domain\Tickets\Listeners\SendTicketStatusChangedNotification;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Policies\TicketPolicy;
use App\Mail\Transport\GraphApiTransport;
use App\Services\Graph\GraphAccessTokenProvider;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuzzleClient::class, fn () => new GuzzleClient([
            'timeout' => 15,
        ]));

        $this->app->singleton(GraphAccessTokenProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(GuzzleClient $client, GraphAccessTokenProvider $tokenProvider): void
    {
        // Ticket vive en App\Domain\Tickets\Models, fuera de app/Models, así
        // que Laravel no la descubre por convención: se registra a mano.
        Gate::policy(Ticket::class, TicketPolicy::class);

        // Notificaciones por correo (FASE 4) vía Microsoft Graph / M365.
        Mail::extend('graph', fn () => new GraphApiTransport(
            $client,
            $tokenProvider,
            (string) config('graph.mail_from'),
        ));

        // Los listeners viven en App\Domain\Tickets\Listeners, fuera de
        // app/Listeners, así que tampoco los descubre la convención de Laravel.
        Event::listen(TicketCreated::class, SendTicketCreatedNotification::class);
        Event::listen(TicketAssigned::class, SendTicketAssignedNotification::class);
        Event::listen(TicketMessageAdded::class, SendTicketMessageNotification::class);
        Event::listen(TicketStatusChanged::class, SendTicketStatusChangedNotification::class);
        Event::listen(TicketResolved::class, SendTicketResolvedNotification::class);
        Event::listen(TicketClosed::class, SendTicketClosedNotification::class);
        Event::listen(TicketReopened::class, SendTicketReopenedNotification::class);
        Event::listen(TicketSlaBreached::class, SendTicketSlaNotification::class);

        // FASE 5: login con Microsoft Entra ID vía Socialite.
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('microsoft', MicrosoftExtendSocialite::class);
        });
    }
}
