<?php namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Collection;

class TicketPolicy
{
    use HandlesAuthorization;

    public function index(User $user, $userId = null)
    {
        return $user->hasPermission('tickets.view') || $user->id === (int) $userId;
    }

    public function show(User $user, $ticket)
    {
        return $user->hasPermission('tickets.view') || $user->id === $ticket->user_id;
    }

    public function store(User $user)
    {
        return $user->hasPermission('tickets.create');
    }

    public function update(User $user, Collection $tickets = null)
    {
        return $user->hasPermission('tickets.update') ||
            ($tickets && $tickets->every('user_id', '=', $user->id));
    }

    public function destroy(User $user)
    {
        return $user->hasPermission('tickets.delete');
    }
}
