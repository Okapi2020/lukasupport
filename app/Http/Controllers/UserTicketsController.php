<?php namespace App\Http\Controllers;

use App\Ticket;
use Common\Core\BaseController;
use Common\Database\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserTicketsController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Ticket
     */
    private $ticket;

    /**
     * @param Ticket $ticket
     * @param Request $request
     */
    public function __construct(Ticket $ticket, Request $request)
    {
        $this->request = $request;
        $this->ticket = $ticket;
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function index($userId)
    {
        $this->authorize('index', [Ticket::class, $userId]);

        $paginator = (new Paginator($this->ticket, $this->request->all()))
            ->where('user_id', $userId)
            ->with(['tags', 'latest_reply'])
            ->withCount('replies');

        $paginator->searchColumn = 'subject';

        if ($status = $paginator->param('status')) {
            $paginator->query()->whereHas('tags', function(Builder $query) use($status) {
                return $query->where('tags.name', $status);
            });
        }

        $pagination = $paginator->paginate();

        //remove html tags from replies
        $pagination->each(function($ticket) {
            if ($ticket->latest_reply) {
                $ticket->latest_reply->stripBody(335);
            }
        });

        return $this->success(['pagination' => $pagination]);
    }
}
