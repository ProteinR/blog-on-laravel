<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    //
    use Sluggable;

    const IS_DRAFT = 0;
    const IS_PUBLIC = 1;


    protected $fillable = ['title', 'content', 'date'];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function author(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(){
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id'
        );
    }

    public function sluggable(){
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields){
        $post = new static;
        $post->fill($fields);
        $post->user_id = 1;
        $post->save();

        return $post;
    }

    public function edit($fields){
        $this->fill($fields);
        $this->save();
    }

    public function remove(){
        $this->removeImage();
        $this->delete();
    }

    public function removeImage(){ //проверка на наличие и удаление картинки
        if($this->image != null) {
            Storage::delete('uploads/' . $this->image);
        }
    }

    public function uploadImage($image){
        if($image == null) { //если картинка не зашла - выйти
            return;
        }
        $this->removeImage();
        $filename = str_random(10) . '.' . $image->extension();
        $image->storeAS('uploads', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function setCategory($id){
        if ($id == null) {
            return;
        }
        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids){
        if ($ids == null){
            return;
        }
        $this->tags()->sync($ids);
    }

    public function setDraft(){
        $this->status = Post::IS_DRAFT;
        $this->save();
    }

    public function setPublic(){
        $this->status = Post::IS_PUBLIC;
        $this->save();
    }

    public function toggleStatus($value){
        if($value == null){
            return $this->setDraft();
        }
        return $this->setPublic();
    }

    public function setFeatured(){
        $this->is_featured = 1;
        $this->save();
    }

    public function setStandart(){
        $this->is_featured = 0;
        $this->save();
    }

    public function toggleFeatured($value){
        if($value == null){
            return $this->setStandart();
        }
        return $this->setFeatured();
    }

    public function getImage(){
        if ($this->image == null){
            return '/img/no-image.png';
        }
        return '/uploads/' . $this->image;
    }

    public function setDateAttribute($value) {
        $date = Carbon::createFromFormat('d/m/y', $value)->format('Y-m-d');
        $this->attributes['date'] = $date;
//        echo date("H:i:s");
    }

    public function getDateAttribute($value){
        $date = Carbon::createFromFormat('Y-m-d', $value)->format('d/m/y');
        return $date;
    }

    public function getCategoryTitle() {
        if($this->category != null) {
            return $this->category->title;
        } else {
            return 'Нет категории';
        }
    }

    public function getTagsTitles(): String {
        if(!($this->tags->isEmpty())) { //если теги есть - возвращаем
            return implode(', ', $this->tags->pluck('title')->all());
        } else {
            return 'Нет тегов';
        }
    }


}
