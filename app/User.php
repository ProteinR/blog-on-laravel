<?php

namespace App;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class User extends Authenticatable
{
    use Notifiable;

    const IS_BANNED = 1;
    const IS_ACTIVE = 0;


    protected $fillable = [
        'name', 'email',
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
//        $user->password = bcrypt($fields['password']);
        $user->save();
        return $user;
    }

    public function edit($fields){
        $this->fill($fields); //name, email
        if($fields['password'] != null) {
            $this->password = bcrypt($fields['password']);
        }
        $this->save();
    }

    public function generatePassword($password){
        if($password != null) {
            $this->password = bcrypt($password);
            $this->save();
        }
    }


    public function remove(){
        $this->removeAvatar();
        $this->delete();
    }

    public function removeAvatar(){
        if($this->avatar != null) {
            Storage::delete('uploads/' . $this->avatar);
        }
    }

    //загрузка аватара. Если есть старый - удаляем и обновляем
    public function uploadAvatar($image){
        if($image == null) { //если картинка не зашла - выйти
            return;
        }
        $this->removeAvatar(); //удаляем старую аву
        $filename = str_random(10) . '.' . $image->extension();
        $imageSrc = 'uploads/'.$filename;
        //обрезаем по картинку по ширине с пропорциями и сохраняем
        Image::make($image)->resize(109, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($imageSrc);

        $this->avatar = $filename; //назначаем аватаром
        $this->save();
    }

    public function getAvatar(){
        if ($this->avatar == null){
            return '/img/no-user-image.png';
        }
        return '/uploads/' . $this->avatar;
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
