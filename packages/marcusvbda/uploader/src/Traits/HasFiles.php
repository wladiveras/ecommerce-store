<?php

namespace marcusvbda\uploader\Traits;

use marcusvbda\uploader\Models\File as _File;
use  marcusvbda\uploader\Models\FileRelation;

trait HasFiles
{
    public function addFile(_File $file, $ref = null)
    {
        $model = $this->getMorphClass();
        $model = strtolower(class_basename($model));
        $ordination = FileRelation::where('model_id', $this->id)->where('model_type', $model)->max('ordination') + 1;

        $relation = [
            'model_type' => $model,
            'model_id' => $this->id,
            'ref' => $ref,
            'file_ref' => $file->ref,
            'ordination' => $ordination,
        ];

        return FileRelation::create($relation);
    }

    public function files($ref = ['image'])
    {
        $model = $this->getMorphClass();
        $model = strtolower(class_basename($model));
        $relation = $this->belongsToMany(_File::class, '_files_relation', 'model_id', 'file_ref', null, 'ref');
        $relation = $relation->where('model_type', $model)->whereIn('_files_relation.ref', $ref)->orderBy('ordination');

        return $relation;
    }

    public function removeFile(_File $file)
    {
        $model = $this->getMorphClass();

        return FileRelation::where('model_type', $model)->where('model_id', $this->id)->where('ref_id', $file->ref)->delete();
    }

    public function reorderFiles($rows)
    {
        $model = $this->getMorphClass();
        foreach ($rows as $row) {
            FileRelation::where('model_id', $this->id)->where('model_type', $model)->where('ref_id', $row['ref'])->update(['ordination' => $row['ordination']]);
        }
    }
}
