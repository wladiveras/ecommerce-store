<?php

namespace marcusvbda\uploader\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Support\Facades\Storage;
use Auth;

class File extends Model
{
    use Sluggable,SluggableScopeHelpers;

    protected $table = '_files';
    protected $appends = ['url', 'raw_dir', 'raw_url', 'raw_thumbnail', 'thumbnail','width','height'];
    public $connection = "dashboard_server";
    public $casts = [
      'metadata' => 'array'
    ];
    protected $fillable = [
        'id',
        'name',
        'dir',
        'description',
        'extension',
        'size',
        'type',
        'slug',
        'ref',
        'user_type',
        'user_id',
        'metadata',
        'private',
    ];

    public function scopeVisible($query)
    {
        $user_type = str_replace('\\', '\\\\', Auth::user()->getMorphClass());
        $user_id = Auth::user()->id;

        return $query->Where('private', 0)->orWhereRaw("(private=1 and user_type='{$user_type}' and user_id='{$user_id}')");
    }

    public function scopePrivate($query)
    {
        $user_type = str_replace('\\', '\\\\', Auth::user()->getMorphClass());
        $user_id = Auth::user()->id;

        return $query->Where('private', 1)->where('user_type', $user_type)->where('user_id', $user_id);
    }

    public function scopePublic($query)
    {
        $user_type = str_replace('\\', '\\\\', Auth::user()->getMorphClass());
        $user_id = Auth::user()->id;

        return $query->Where('private', 0)->where('user_type', null)->where('user_id', null);
    }

    public function getRawThumbnailAttribute()
    {
        $url = env('UPLOAD_FILE_CDN_ROUTE', 'http://padrao.cdn').'/'.env('UPLOAD_FILE_THUMB_FOLDER', 'thumbnail').'/'.$this->id.'.'.$this->extension;
        
        return $url;
    }

    public function getWidthAttribute(){
      $data = $this->metadata;
    //   if($data){
    //     return $data['width'];
    //   }
      return 0;
    }

    public function getHeightAttribute(){
      $data = $this->metadata;
    //   if($data){
    //     return $data['height'];
    //   }
      return 0;
    }

    public function getRawUrlAttribute()
    {
        $url = config('filesystems.disks.cdn.full_url', 'http://padrao.cdn').'/'.$this->dir;

        return $url;
    }

    public function getRawDirAttribute()
    {
        $url = env('UPLOAD_FILE_PATH', 'D:/wamp64/www/padrao/cdn').'/'.$this->slug.'.'.$this->extension;

        return $url;
    }

    public function getThumbnailAttribute()
    {
        $url = url('files/get').'/'.env('UPLOAD_FILE_THUMB_FOLDER', 'thumbnail').'/'.$this->slug.'.'.$this->extension;

        return $url;
    }

    public function getUrlAttribute()
    {
        $url = config('filesystems.disks.cdn.full_url');

        $url .= '/'.$this->name.'.'.$this->extension;

        return $url;
    }

    public function sluggable()
    {
        return
        [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(FileCategory::class, '_files_categories_relation', 'file_id', 'file_category_id');
    }

    public function delete()
    {
        if (env('UPLOAD_FILE_CASCADE', false)) {
            FileRelation::where('file_id', $this->id)->delete();
            Storage::delete($this->dir);

            return parent::delete();
        } else {
            if (FileRelation::where('file_id', $this->id)->count() == 0) {
                Storage::delete($this->dir);

                return parent::delete();
            }
        }

        return false;
    }

    public function setPrivate()
    {
        $user_type = Auth::user()->getMorphClass();
        $user_id = Auth::user()->id;

        return $this->update(['user_type' => $user_type, 'user_id' => $user_id, 'private' => 1]);
    }

    public function setPublic()
    {
        return $this->update(['user_type' => null, 'user_id' => null, 'private' => 0]);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['ref'] = uniqid();
    }
}
