<?php

namespace App\Providers;

use App\User;
use Mail;
use App\Mail\ApprovedMail;
use App\Mail\RequestApprovalMail;
use App\Mail\WelcomeMail;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::created(function ($user) {
            //Mail::to($user->email)->send(new WelcomeMail($user));
            $user->sendWelcomeNotification();

            if (env("APPROVE_MEMBER_EMAIL")) {
                $admins = User::where("can_admin", 1)->get();

                if (!empty($admins)) {
                    foreach ($admins as $admin) {
                        $admin->sendRequestApprovalNotification($user);
                    }
                }
                //Mail::to(env('APPROVE_MEMBER_EMAIL'))->send(new RequestApprovalMail($user));
            }
        });

        User::saved(function ($user) {
            if ($user->getOriginal("approved") == 0 && $user->approved == 1) {
                $user->sendApprovedNotification();
                //Mail::to($user->email)->send(new ApprovedMail($user));
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
