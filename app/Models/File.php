<?php

namespace App\Models;

use marcusvbda\uploader\Models\File as _File;
class File extends _File
{
  public $connection = "dashboard_server";
}
