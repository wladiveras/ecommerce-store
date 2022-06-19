<?php

namespace App\Models;

use marcusvbda\commonModels\Models\OrderComment as _OrderComment;

class OrderComment extends _OrderComment
{
    public $connection = "dashboard_server";
}
