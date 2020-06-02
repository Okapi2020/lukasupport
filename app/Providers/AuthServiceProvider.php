<?php namespace App\Providers;

use App\CannedReply;
use App\Policies\ActionPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\CannedReplyPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ConditionPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\ReportPolicy;
use App\Policies\TagPolicy;
use App\Policies\TicketFileEntryPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TriggerPolicy;
use App\Tag;
use App\Reply;
use App\Action;
use App\Ticket;
use App\Trigger;
use App\Article;
use App\Category;
use App\Condition;
use Common\Files\FileEntry;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'         => 'App\Policies\ModelPolicy',
        'ReportPolicy'      => ReportPolicy::class,
        Ticket::class       => TicketPolicy::class,
        Reply::class        => ReplyPolicy::class,
        CannedReply::class  => CannedReplyPolicy::class,
        Category::class     => CategoryPolicy::class,
        Article::class      => ArticlePolicy::class,
        Tag::class          => TagPolicy::class,
        Condition::class    => ConditionPolicy::class,
        Action::class       => ActionPolicy::class,
        Trigger::class      => TriggerPolicy::class,
        FileEntry::class    => TicketFileEntryPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
