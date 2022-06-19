<?php

namespace marcusvbda\uploader\Controllers;

use App\Http\Controllers\Controller;
use File;
use marcusvbda\uploader\Models\File as _Files;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Intervention\Image\Facades\Image as Image;

class UploaderController extends Controller
{
  public function getFile($slug)
  {
    if ($file = _Files::findBySlug($slug)) {
      if (file_exists($file->raw_dir)) {
        $path = $file->raw_dir;
        $response = response()->make(File::get($path));
        $response->header('content-type', File::mimeType($path));

        return $response;
      }
    }
  }

  public function getThumbFile($slug)
  {
    if ($file = _Files::findBySlug($slug)) {
      $dir = env('UPLOAD_FILE_THUMB_FOLDER', 'thumbnail').'/'.$file->id.'.'.$file->extension;
      if (file_exists($file->raw_thumbnail)) {
        $path = $file->raw_thumbnail;
        $response = response()->make(File::get($path));
        $response->header('content-type', File::mimeType($path));

        return $response;
      } else {
        if (file_exists($file->raw_dir)) {
          $path = $file->raw_dir;
          $response = response()->make(File::get($path));
          $response->header('content-type', File::mimeType($path));

          return $response;
        }
      }
    }
  }

  public static function makeThumbnail($file)
  {
    $path = env('UPLOAD_FILE_PATH', 'D:/wamp64/www/padrao/cdn').'/'.$file->dir;
    $thumbnailPath = env('UPLOAD_FILE_PATH', 'D:/wamp64/www/padrao/cdn').'/'.env('UPLOAD_FILE_THUMB_FOLDER', 'thumbnail');
    $thumbnailDir = $thumbnailPath.'/'.$file->id.'.'.$file->extension;
    if (!file_exists($thumbnailPath)) {
      mkdir($thumbnailPath, 0777, true);
    }

    Image::make($path)->resize(null, (int) env('UPLOAD_FILE_THUMB_HEIGHT', 90), function ($constraint) {
      $constraint->aspectRatio();
      $constraint->upsize();
    })->save($thumbnailDir, env('UPLOAD_FILE_THUMB_QUALITY', 70));

    return $thumbnailDir;
  }
  public static function getFileName($extension,$filename){

    if($extension == "") return $filename;

    $name_length = strlen($filename);
    $extension_length = strlen($extension) + 1;

    return substr($filename, 0, $name_length - $extension_length);
  }

  public static function upload($file, $name, $description, $set_type = null, $namespace = "")
  {
    try {
      $path = env('UPLOAD_FILE_PATH');
      if (is_string($file)) {
        $url = $file;
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $name = pathinfo($url, PATHINFO_FILENAME);
        $type = pathinfo($url, FILEINFO_MIME_TYPE);
        $slugname = SlugService::createSlug(_Files::class, 'slug', $name);
        // / $data = file_get_contents($url);
        // / $dir = $path.'/'.$slugname.'.'.$extension;
        $dir = $slugname.'.'.$extension;
        if($namespace){
          $dir = $namespace."/".$dir;
        }
        $buffer = file_get_contents($url);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $type = $finfo->buffer($buffer);
        // / Storage::put($dir, $data);
        copy($file, $path.'/'.$dir);
      } else {
        $file__path = config('filesystems.disks.cdn.api_namespace')."/".$namespace;
        $hash = md5_file($file->path()).'.'.$file->getClientOriginalExtension();


        $file->storeAs($file__path,$hash,'cdn_local');
        $extension = $file->getClientOriginalExtension();
        $name = UploaderController::getFileName($file->getClientOriginalExtension(),$file->getClientOriginalName());
        $slugname = SlugService::createSlug(_Files::class, 'slug', $name);
        $type = (($set_type != null) ? $set_type : substr($file->getMimeType(), 0, 5));
      }
      $type = explode('/', $type)[0];
      if($type == 'image'){
        $info = getimagesize($file);
        $metadata = [
          'width' => $info[0],
          'height' => $info[1],
          'all' => $info
        ];
      }
      $newFile = [
        'name' => $name,
        'dir' => $namespace."/".$hash,
        'description' => $description,
        'slug' => $slugname,
        'extension' => $extension,
        'type' => $type,
        'private' => 1,
        'metadata' => @$metadata
      ];
      $newFile = _Files::create($newFile);
      return $newFile;
    } catch (\Exception $e) {
      dd($e);
      if (isset($dir)) {
        Storage::delete($dir);
      }

      return null;
    }
  }
}
