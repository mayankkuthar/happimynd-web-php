<?php

namespace App\Services;

use App\Models\UserProfile;

class UserProfileService
{
    public function addUserProfile($data)
    {
        return UserProfile::create($data);
    }

    public function updateUserProfile($userProfileId, $data)
    {
        return UserProfile::where('id', $userProfileId)->update($data);
    }

    public function deleteUserProfile($userProfileId)
    {
        $userProfile = UserProfile::find($userProfileId);
        if ($userProfile && $userProfile->users->count() == 0) {
            return $userProfile->delete();
        }
        return false;
    }

    public function changeStatus($userProfileId, $status)
    {
        return UserProfile::where('id', $userProfileId)->update(["status" => $status]);
    }

    public function checkIfProfileExists($profileName, $id = '')
    {
        if ($id) {
            return UserProfile::where('name', 'like', $profileName)->where('id', '!=', $id)->get()->count() > 0;
        }
        return UserProfile::where('name', 'like', $profileName)->get()->count() > 0;
    }
}
