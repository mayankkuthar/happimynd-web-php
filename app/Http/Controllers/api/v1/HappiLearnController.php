<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HappiLearnContent;
use App\Models\LikeHappiLearnContent;
use App\Models\RecentlyViewedHappiLearnContent;
use Auth;
use Validator;

class HappiLearnController extends Controller
{
    public function HappiLearnContent(Request $request){

        $user = Auth::user();

        $content_type = $request->content_type;
        $explode_content_type = explode(',',$content_type);

        
        $profile = $request->profile;
        $explode_profile = explode(',',$profile);

        $parameters = $request->parameters;
        $explode_parameters = explode(',',$parameters);


        $language = $request->language;
        $explode_language = explode(',', $language);

        if($language == null){
            $explode_language = ['english'];
        }

        if($request->search){

            $data = HappiLearnContent::where('keywords', 'like', '%'. $request->search . '%')
                        ->where('is_deleted' , 0)
                        ->orderBy('id' , 'desc')
                        ->withCount('likes');

            if($content_type){
                $data = $data->whereIn('type' , $explode_content_type);
            }
            if($explode_language){
                        $data = $data->whereIn('language' , $explode_language);
            }
            if($profile){
                $data->where(function($query) use($explode_profile) {
                        foreach($explode_profile as $single_profile_name) {
                            $query->orWhere('profile', 'like', "%$single_profile_name%");
                        };
                });
            }
            if($parameters){
                $data->where(function($query) use($explode_parameters) {
                        foreach($explode_parameters as $single_parameter_name) {
                            $query->orWhere('parameters', 'like', "%$single_parameter_name%");
                        };
                });
            }
        }
        else{

            $data = HappiLearnContent::orderBy('id','desc') 
                        ->where('is_deleted' , 0)
                        ->withCount('likes');

            if($content_type){ 
                $data->whereIn('type',$explode_content_type);
            }
            if($explode_language){
                $data->whereIn('language' , $explode_language);
            }
            if($profile){
                $data->where(function($query) use($explode_profile) {
                        foreach($explode_profile as $single_profile_name) {
                            $query->orWhere('profile', 'like', "%$single_profile_name%");
                        };
                });
            }
            if($parameters){
                $data->where(function($query) use($explode_parameters) {
                        foreach($explode_parameters as $single_parameter_name) {
                            $query->orWhere('parameters', 'like', "%$single_parameter_name%");
                        };
                });
            }
        }

                        
        $data = $data->paginate(10);

        $recently_viewed_content = RecentlyViewedHappiLearnContent::select('happi_learn_content_id')->where('user_id' , $user->id)->with('HappiLearnContent')->orderBy('id' , 'desc')->paginate(10);

        return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $data , 'recently_viewed' => $recently_viewed_content]);
        // return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $data]);

    }






    public function HappiLearnContentById(Request $request){
        $user = Auth::user();

        $message = [
            'content_id.required'      =>  'Please enter content ID',
            'content_id.exists'      =>  'Please enter valid content ID',
        ];
        $validator = Validator::make($request->all(), [
            'content_id'   => 'required|exists:happi_learn_contents,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = HappiLearnContent::where('id',$request->content_id)->withCount('likes')->first();

        $delete_From_recently_viewed  = RecentlyViewedHappiLearnContent::where(['user_id' => $user->id , 'happi_learn_content_id' => $request->content_id])->delete();
        $insert_in_recent_viewed_table = RecentlyViewedHappiLearnContent::create(['user_id' => $user->id , 'happi_learn_content_id' => $request->content_id]);


        $profiles_of_content  = $data->profile;
        $explode_profiles_of_content = explode(',' , $profiles_of_content);
        $suggestion_based_on_profile = HappiLearnContent::where('is_deleted' , 0)->where('language' , $data->language)->where('id' , '!=' , $request->content_id)->orderBy('id' , 'desc');
        $suggestion_based_on_profile->where(function($query) use($explode_profiles_of_content) {
                foreach($explode_profiles_of_content as $single_profile_name) {
                    $query->orWhere('profile', 'like', "%$single_profile_name%");
                };
        });
        $suggestion_based_on_profile = $suggestion_based_on_profile->withCount('likes')->get(5);


        return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $data , 'suggested_content' => $suggestion_based_on_profile]);
        // return response()->json(['status' => 'success' , 'message' => 'Content get successfully.' , 'data' => $data]);



    }



    public function likeHappiLearnPost(Request $request){

        $user = Auth::user();

        $message = [
            'happi_learn_content_id.required'      =>  'Please enter content ID',
            'happi_learn_content_id.exists'      =>  'Please enter valid content ID',
        ];
        $validator = Validator::make($request->all(), [
            'happi_learn_content_id'   => 'required|exists:happi_learn_contents,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $data = [
            'user_id' => $user->id,
            'happi_learn_content_id' => $request->happi_learn_content_id,
        ];

        $is_already_like = LikeHappiLearnContent::where('user_id',$user->id)
                                ->where('happi_learn_content_id',$request->happi_learn_content_id)
                                ->first();

        if($is_already_like){
            return response()->json(['status' => 'success' , 'message' => 'Post already liked.']);
        }else{
            LikeHappiLearnContent::create($data);
            return response()->json(['status' => 'success' , 'message' => 'Post like successfully.']);
        }
        

    }


    public function unLikeHappiLearnPost(Request $request){
        
        $user = Auth::user();

        $message = [
            'happi_learn_content_id.required'      =>  'Please enter content ID',
            'happi_learn_content_id.exists'      =>  'Please enter valid content ID',
        ];
        $validator = Validator::make($request->all(), [
            'happi_learn_content_id'   => 'required|exists:happi_learn_contents,id',

        ],$message);

        if($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()],400);
        }

        $is_liked = LikeHappiLearnContent::where('user_id',$user->id)
                             ->where('happi_learn_content_id',$request->happi_learn_content_id)
                             ->first();
        if($is_liked){
            $is_liked->delete();
            return response()->json(['status' => 'success' , 'message' => 'Post unlike successfully.']);
        }else{
            return response()->json(['status' => 'success' , 'message' => 'Post already unlike.']);
        }

    }



    public function searchParameters(Request $request){
        $parameters = [
                'Stress',
                'Anxiety',
                'Depression',
                'Burn Out',
                'Happiness',
                'Internet Addiction',
                'Personality',
                'Self Esteem',
                'Resilience',
                'Job Satisfaction',
                'Substance Abuse',
                'Emotional Regulation',
                'Peer Pressure',
                'Group Conformity',
                'Gaming Disorder',
                'Attention and Concentration',
                'Relationship Issues',
                'Body Image',
                'Well Being',
        ];

        return response()->json(['message' => 'Parameters get sucessfully.' , 'data' => $parameters]);
    }


}



