<?php

namespace App\Models;

use marcusvbda\commonModels\Models\HelpCenterTopic as _HelpCenterTopic;

class HelpCenterTopic extends _HelpCenterTopic
{
    public $connection = "dashboard_server";
}
