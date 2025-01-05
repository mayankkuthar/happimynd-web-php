<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\UserProfile;
use App\Services\ApiResponseService;
use App\Services\UserProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{

    private $apiResponseService, $userProfileService;

    public function __construct(ApiResponseService $apiResponseService, UserProfileService $userProfileService)
    {
        $this->apiResponseService = $apiResponseService;
        $this->userProfileService = $userProfileService;
    }

    public function addUserProfile(Request $request)
    {
        if (!$this->userProfileService->checkIfProfileExists($request->input('userProfileName'))) {
            $result = $this->userProfileService->addUserProfile([
                'name' => $request->input('userProfileName'),
            ]);
            if ($result) {
                $request->session()->flash('success', "Profile Added");
            } else {
                $request->session()->flash('error', "Some problem occured");
            }
        } else {
            $request->session()->flash('danger', "Profile already exists");
        }
        return redirect(route('admin.addUserProfile.get'));
    }

    public function changeUserProfileStatus(Request $request)
    {
        return $this->userProfileService->changeStatus($request->input('userProfileId'), $request->input('status'));
    }

    public function addUserProfileView(Request $request)
    {
        if (!auth('admin')->user()->hasAnyRole(['super-admin', 'admin'])) {
            return redirect(route('admin.dashboard'));
        }
        $roles = "";
        $userProfiles = UserProfile::withCount('users')->get();
        if (auth('admin')->check()) {
            $roles = auth('admin')->user()->roles;
        }
        return view('Backend.userprofile.add')->with('roles', $roles)->with('userProfiles', $userProfiles);
    }

    public function updateUserProfile(Request $request)
    {
        if (!$this->userProfileService->checkIfProfileExists($request->input('userProfileName'), $request->input('userProfileId'))) {
            $this->userProfileService->updateUserProfile($request->input('userProfileId'), [
                'name' => $request->input('userProfileName'),
                'status' => $request->input('userProfileStatus') ?? true,
            ]);
            return $this->apiResponseService->success(true);
        } else {
            return $this->apiResponseService->error(['profile-name' => 'Profile name already exists']);
        }
    }

    public function deleteUserProfile(Request $request)
    {
        try {
            DB::beginTransaction();
            $status = $this->userProfileService->deleteUserProfile($request->input('userProfileId'));
            DB::commit();
            if ($status) {
                return $this->apiResponseService->success('');
            } else {
                return $this->apiResponseService->error(['notify' => [
                    'type' => 'danger',
                    'message' => 'cannot delete this profile',
                ]]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error($e);
            return $this->apiResponseService->error('some problem occurred contact developer');
        }
    }
}
