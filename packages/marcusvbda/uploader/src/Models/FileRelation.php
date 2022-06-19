<?php

namespace marcusvbda\uploader\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\uploader\Models\File as _File;

class FileRelation extends Model
{
    protected $table = '_files_relation';
    public $connection = "dashboard_server";
    protected $fillable = [
    'model_type',
    'model_id',
    'file_ref',
    'ref',
    'ordination',
  ];

    public function file()
    {
        return $this->belongsTo(_File::class);
    }
}
