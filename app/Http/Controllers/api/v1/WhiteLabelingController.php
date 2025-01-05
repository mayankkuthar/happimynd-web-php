<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\UserToken;

use Auth;

class WhiteLabelingController extends Controller
{



    public function whiteLabellingStatus(Request $request){
        $user = Auth::user();

        $is_user_from_org = UserToken::where('user_id' , $user->id)->with('token')->first();
        if($is_user_from_org){
            $organization_detail = Organization::where('id' , $is_user_from_org->token->organization_id)->first();
            if($organization_detail->main_logo == 0){
                return response()->json(['status' => 'success' , 'message' => 'Logo get successfully.' , 'header' => '0' , 'footer' => '0']);

            }
            if($organization_detail->main_logo == 1){
                if($organization_detail->organization_logo == null){
                    $logo = url('assets/Frontend/images/happimynd_logo.png');
                    return response()->json(['status' => 'success' , 'message' => 'Logo get successfully.' , 'header' => $organization_detail->main_logo , 'footer' => $organization_detail->powered_by , 'logo' => $logo]);
                }else{
                    $logo =  $organization_detail->organization_logo;
                    return response()->json(['status' => 'success' , 'message' => 'Logo get successfully.' , 'header' => $organization_detail->main_logo , 'footer' => $organization_detail->powered_by , 'logo' => $logo]);
                }
            }
            
        }else{
            $logo = url('assets/Frontend/images/happimynd_logo.png');
            return response()->json(['status' => 'success' , 'message' => 'Logo get successfully.' , 'header' => '0' , 'footer' => '1']);
        }
    }




}
