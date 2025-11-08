<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\LoginRateLimiter;
use App\Modules\Subscription\Http\Traits\SubscriptionTrait;
use Digikraaft\Paystack\Subscription;
use App\Modules\Wallet\Models\SubscriptionWallet;
use App\Modules\Wallet\Models\CreditWallet;



class PrepareAuthenticatedSession
{
    /**
     * The login rate limiter instance.
     *
     * @var \Laravel\Fortify\LoginRateLimiter
     */
    protected $limiter;

    /**
     * Create a new class instance.
     *
     * @param  \Laravel\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(LoginRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $request->session()->regenerate();

        $this->limiter->clear($request);

       // try {
       //      $customer = auth()->user()->createOrGetPaystackCustomer(['email' => auth()->user()->email]);
       //      $subscriptions = Subscription::list(['email' => $customer->data->email ])->data;
       //      SubscriptionTrait::syncSubscriptions($subscriptions);
           
       // } catch (Exception $e) {
           
       // }


       SubscriptionWallet::updateOrCreate([
             'user_id' =>  auth()->user()->id,
         ]);

        CreditWallet::updateOrCreate([
             'user_id' =>  auth()->user()->id,
        ]);



        return $next($request);
    }
}
