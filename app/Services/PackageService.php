<?php

namespace App\Services;

use App\Models\BundleStatus;

class PackageService
{
    //TODO: thrive code assign logic to be done here from usercontroller

    public function bundlePlanCompleted($bundleStatusId, $percentage_covered = 100.00)
    {
        BundleStatus::where('id', $bundleStatusId)->update(['percentage_covered' => $percentage_covered]);
    }
}
