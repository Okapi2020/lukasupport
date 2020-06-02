<?php namespace App\Http\Controllers;

use App\Ticket;
use Illuminate\Http\Request;
use App\Services\Ticketing\TicketRepository;
use Common\Core\BaseController;

class TicketStatusController extends BaseController
{
    /**
     * TicketRepository model instance.
     *
     * @var TicketRepository
     */
    private $tickets;

    /**
     * Laravel request instance.
     *
     * @var Request
     */
    private $request;

    /**
     * TicketStatusController constructor.
     *
     * @param TicketRepository $tickets
     * @param Request          $request
     */
    public function __construct(TicketRepository $tickets, Request $request)
    {
        $this->tickets = $tickets;
        $this->request = $request;
    }

    /**
     * Change status of multiple tickets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function change()
    {
        $this->validate($this->request, [
            'ids'    => 'required|array',
            'status' => 'required|string|in:open,closed,pending,spam'
        ]);

        $ids    = $this->request->input('ids');
        $status = $this->request->input('status');

        $tickets = app(Ticket::class)->whereIn('id', $ids)->get();

        $this->authorize('update', [Ticket::class, $tickets]);

        $this->tickets->changeStatus($ids, $status);

        return $this->success();
    }
}
