<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Cviebrock\EloquentSluggable\Sluggable;
// use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\Wallet\Models\SubscriptionWallet;
use App\Modules\Wallet\Models\CreditWallet;
use Digikraaft\PaystackSubscription\Billable;

use App\Modules\Account\Models\AccountFollow;
use App\Modules\Account\Models\AccountFavorite;
use App\Modules\Account\Models\Account;





class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Sluggable;
    // use HasRolesAndAbilities;
    use Billable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'title',
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($user) {
            // $user->name = $user->first_name . ' ' . $user->last_name;
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'username' => [
                'source' => ['first_name', 'last_name']
            ]
        ];
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function getNameAttribute(){
        return $this->first_name . ' ' . $this->last_name;
    }


    // relationships
    public function account()
    {
        return $this->hasOne(Account::class);
    }

    public function favoriteResources()
    {
        return $this->hasMany(AccountFavorite::class);
    }


    public function followers()
    {
        return $this->hasMany(AccountFollow::class, 'followee_id', 'id');
    }

    public function followings()
    {
        return $this->hasMany(AccountFollow::class, 'follower_id', 'id');
    }


    public static function whereInMultiple(array $columns, $value)
    {
        // $values = array_map(function (array $value) {
        //     return "('".implode($value, "', '")."')"; 
        // }, $values);

        return static::query()->whereRaw(
            '(  '. $value .  ') in ('  .  implode(', ', $columns, ) .  ')'
        );
    }


    // interface provided by puysub
    public function paystackEmail(): string {
        return 'user@example.com';
    }

    public function invoiceMailables(): array {
        return [
            'user@example.com'
        ];
    }

    public function SubscriptionWallet(){
        return $this->hasOne(SubscriptionWallet::class);
    }

    public function CreditWallet(){
        return $this->hasOne(CreditWallet::class);
    }


     /**
     * Enter your own logic (e.g. if ($this->id === 1) to
     *   enable this user to be able to add/edit blog posts
     *
     * @return bool - true = they can edit / manage blog posts,
     *        false = they have no access to the blog admin panel
     */
    public function canManageBinshopsBlogPosts()
    {
        // Enter the logic needed for your app.
        // Maybe you can just hardcode in a user id that you
        //   know is always an admin ID?

        if ($this->id == 1 || $this->id == 2){
           return true;
        }

        // otherwise return false, so they have no access
        // to the admin panel (but can still view posts)

        return false;
    }
}
