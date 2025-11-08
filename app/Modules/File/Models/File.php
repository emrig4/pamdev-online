<?php

namespace App\Modules\File\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Modules\File\Helpers\FilesIcon;



class File extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($file) {
            Storage::disk($file->disk)->delete($file->getOriginal('path'));
        });
    }
    
    public static function findById($id)
    {
        return static::where('id', $id)->first();
    }
    
    public static function findByIds($ids)
    {
        return static::whereIn('id', $ids)->get();
    }
    
    public function uploader()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    
    public function icon()
    {
        return FilesIcon::getIcon($this->mime);
    }
    
    

    public function url(){
        $path = rawurlencode($this->path);
        $fileLink =  Storage::disk($this->disk)->url($path);
        return $fileLink;
        // return $this->path;
    }

    

    public function isImage()
    {
        return strtok($this->mime, '/') === 'image';
    }
    
    public function isVideo()
    {
        return strtok($this->mime, '/') === 'video';
    }
    
}
