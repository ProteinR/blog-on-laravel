<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function author()
    {
        return $this->hasOne(User::class);
    }

    //Prove comment
    public function allow(){
        $this->status = 1;
        $this->save();
    }

    public function disallow(){
        $this->status = 0;
        $this->save();
    }

    public function togleStatus(){
        if($this->status = 0){
            $this->allow();
        }
        $this->disAllow();
    }
}
