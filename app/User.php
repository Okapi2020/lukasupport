<?php namespace App;

use Carbon\Carbon;
use Common\Auth\BaseUser;
use Common\Notifications\NotificationSubscription;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Laravel\Scout\Searchable;
use Illuminate\Notifications\Notifiable;

/**
 * App\User
 *
 * @property-read Collection|Ticket[] $tickets
 * @property-read Collection|Reply[] $replies
 * @property-read Collection|CannedReply[] $cannedReplies
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $unreadNotifications
 * @mixin Eloquent
 * @property-read Collection|PurchaseCode[] $envato_purchase_codes
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $readNotifications
 * @property-read Collection|PurchaseCode[] $purchase_codes
 * @property-read Collection|Tag[] $tags
 * @property-read Collection|Email[] $secondary_emails
 * @property-read UserDetails $details
 * @property string $language
 * @property string $country
 * @property string $timezone
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string $avatar
 * @property string|null $legacy_permissions
 * @property string|null $password
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property bool $confirmed
 * @property string|null $confirmation_code
 * @property string|null $stripe_id
 * @property int $available_space
 * @property string|null $username
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Files\FileEntry[] $entries
 * @property-read string $display_name
 * @property-read bool $has_password
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Notifications\NotificationSubscription[] $notificationSubscriptions
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Auth\Permissions\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Auth\Roles\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Auth\SocialProfile[] $social_profiles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Common\Billing\Subscription[] $subscriptions
 * @method static \Illuminate\Database\Eloquent\Builder|\Common\Auth\BaseUser compact()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvailableSpace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLegacyPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Common\Auth\BaseUser whereNeedsNotificationFor($notifId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 */
class User extends BaseUser
{
    use Notifiable, Searchable;

    protected $billingEnabled = false;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'envato_username' => $this->purchase_codes->pluck('envato_username')
        ];
    }

    /**
     * Tickets created by this user.
     *
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket')->orderBy('created_at', 'desc');
    }

    /**
     * User profile.
     *
     * @return HasOne
     */
    public function details()
    {
        return $this->hasOne(UserDetails::class);
    }

    /**
     * Secondary email address belonging to user.
     *
     * @return HasMany
     */
    public function secondary_emails()
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Envato purchase codes attached to this user.
     *
     * @return HasMany
     */
    public function purchase_codes()
    {
        return $this->hasMany(PurchaseCode::class)->orderBy('created_at', 'desc');
    }

    /**
     * Replies submitted by this user.
     *
     * @return HasMany
     */
    public function replies()
    {
        return $this->hasMany('App\Reply');
    }

    /**
     * Canned replies created by this user.
     *
     * @return HasMany
     */
    public function cannedReplies()
    {
        return $this->hasMany('App\CannedReply');
    }

    /**
     * Tags that are attached to the user.
     *
     * @return MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    /**
     * Search users using basic mysql LIKE query.
     *
     * @param string $query
     * @return Builder
     */
    public function basicSearch($query)
    {
        return $this->where('email', 'LIKE', "$query%")
            ->orWhere('first_name', 'LIKE', "$query%")
            ->orWhere('last_name', 'LIKE', "$query%");
    }

    /**
     * Create new envato purchase code from
     * specified details and attach it to user.
     *
     * @param array $purchases
     * @param string $envatoUsername
     */
    public function updatePurchases($purchases, $envatoUsername = null) {
        foreach ($purchases as $purchaseDetails) {
            $supportedUntil = array_get($purchaseDetails, 'supported_until');
            $this->purchase_codes()->updateOrCreate(['code' => $purchaseDetails['code']], [
                'item_name' => $purchaseDetails['item']['name'],
                'item_id'   => $purchaseDetails['item']['id'],
                'code'      => $purchaseDetails['code'],
                'supported_until' => $supportedUntil ? Carbon::parse($supportedUntil) : null,
                'url'       => array_get($purchaseDetails, 'item.url'),
                'image'     => array_get($purchaseDetails, 'item.previews.icon_preview.icon_url'),
                'envato_username' => $envatoUsername,
            ]);
        }
    }

    /**
     * Check if user is a super admin.
     *
     * @return boolean
     */
    public function isSuperAdmin()
    {
        return $this->hasPermission('superAdmin') || $this->hasPermission('admin');
    }

    /**
     * Check if user is an agent.
     *
     * @return bool
     */
    public function isAgent()
    {
        return $this->isSuperAdmin() || $this->belongsToRole('agents') || $this->hasPermission('tickets.update');
    }

    /**
     * Check if user belongs to specified group.
     *
     * @param string $name
     * @return bool
     */
    public function belongsToRole($name)
    {
        return $this->roles->contains('name', $name);
    }
}
