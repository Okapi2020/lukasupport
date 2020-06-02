<?php

namespace App\Services;

use App\Article;
use App\Category;
use App\Ticket;
use Common\Core\Prerender\BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @param Ticket|array $ticket
     * @return string
     */
    public function ticket($ticket)
    {
        $tag = $ticket['status'] || 1;
        return url("mailbox/tickets/tag/$tag/ticket/{$ticket->id}");
    }

    /**
     * @param Article|array $article
     * @return string
     */
    public function article($article)
    {
        return url('help-center/articles') . "/{$article['id']}/" . str_slug($article['title']);
    }

    /**
     * @param Category|array $category
     * @return string
     */
    public function category($category)
    {
        return url('help-center/categories') . "/{$category['id']}/" . str_slug($category['name']);
    }

    /**
     * @param array $data
     * @return string
     */
    public function search($data)
    {
        return url('help-center/search') . "/{$data['query']}";
    }
}
