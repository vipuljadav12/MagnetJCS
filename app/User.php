<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use App\Traits\AuditTrail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\PasswordResetNotification;

class User extends Authenticatable
{
    use Notifiable;
    // public function __construct()
    // {
        // AuditTrailt
        // $this->catchChanges($this);
        // throw new \Exception("Error Processing Request", 1);
        
    // }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $primaryKey = "id";
    public $table = 'users';
    public $fillable = [
        'username','name', 'email', 'password','role_id','first_name','last_name','district_id','status','profile',"programs"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /*public function setNameAttribute($value)
    {
        $this->attributes['Name'] = $this->FirstName.$this->LastName;
    }*/
    public function getFullNameAttribute($value)
    {
       return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
    public function getProgramsAttribute($value)
    {
        return explode(',',$value);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }
}
