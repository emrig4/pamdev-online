<?php
namespace App\Modules\Admin\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles;

    protected $guard_name = 'web';
    protected $table = 'users';
    protected $fillable = ['first_name','last_name','username','email','password'];
    protected $hidden = ['password','remember_token'];
}
