<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function toggleStatus(){
        if($this->status == 0){
            $this->allow();
        }else {
            $this->disAllow();
        }
    }
}
