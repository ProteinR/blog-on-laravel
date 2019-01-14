<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;


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

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public static function add($fields) {
        $user = new static();
        $user->fill($fields);
        $user->password->bcrypt($fields['password']);
        $user->save();
        return $user;
    }

    public function edit($fields){
        $this->fill($fields);
        $this->password->bcrypt($fields['password']);
        $this->save();
    }

    public function remove(){
        Storage::delete('uploads/' . $this->image);
        $this->delete();
    }

    public function uploadAvatar($image){
        if($image == null) { //если картинка не зашла - выйти
            return;
        }
        Storage::delete('uploads/' . $this->image);
        $filename = str_random(10) . '.' . $image->extension();
        $image->saveAS('uploads', '$filename');
        $this->image = $filename;
        $this->save();
    }

    public function getAvatar(){
        if ($this->image == null){
            return '/img/no-user-image.png';
        }
        return '/uploads/' . $this->image;
    }


    //Admin privilege
    public function makeAdmin(){
        $this->is_admin = 1;
        $this->save();
    }

    public function makeNormal(){
        $this->is_admin = 0;
        $this->save();
    }

    public function togleAdmin($value){
        if($value == null){
            return $this->makeNormal();
        }
        return $this->makeAdmin();
    }

    //BAN
    public function ban(){
        $this->status = User::IS_BANNED;
        $this->save();
    }

    public function unban(){
        $this->status = User::IS_ACTIVE;
        $this->save();
    }

    public function togleBan($value){
        if($value == null){
            return $this->unban();
        }
        return $this->ban();
    }

}
