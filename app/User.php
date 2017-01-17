<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\PasswordResetNotification;
use App\Notifications\ApprovedNotification;
use App\Notifications\RequestApprovalNotification;
use App\Notifications\WelcomeNotification;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendApprovedNotification()
    {
        $this->notify(new ApprovedNotification());
    }

    public function sendRequestApprovalNotification($user)
    {
        if ($this->can_admin) {
            $this->notify(new RequestApprovalNotification($user));
        }
    }

    public function sendWelcomeNotification()
    {
        $this->notify(new WelcomeNotification());
    }
}
