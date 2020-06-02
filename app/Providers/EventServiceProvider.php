<?php

namespace App\Providers;

use App\Events\TicketCreated;
use App\Events\TicketReplyCreated;
use App\Events\TicketsAssigned;
use App\Listeners\SendReplyCreatedNotif;
use App\Listeners\SendTicketCreatedNotif;
use App\Listeners\SendTicketsAssignedNotif;
use App\Listeners\TicketEventSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [
        TicketsAssigned::class => [SendTicketsAssignedNotif::class],
        TicketCreated::class => [SendTicketCreatedNotif::class],
        TicketReplyCreated::class => [SendReplyCreatedNotif::class],
    ];

    /**
     * @var array
     */
    protected $subscribe = [
        TicketEventSubscriber::class,
    ];

    /**
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
