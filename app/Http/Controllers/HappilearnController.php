<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\HappiLearnContent;
use App\Models\User;
use App\Models\RecentlyViewedHappiLearnContent;
use App\Models\LikeHappiLearnContent;
use Illuminate\Support\Facades\Schema;
use App\Imports\ImportHappiLearnContent;
use App\Models\UserLanguage;
use Excel;
use Aws\S3\S3Client;
use App\Http\Requests\StoreImage;
use Illuminate\Support\Facades\Storage;
use App\Services\FileService;
use Illuminate\Http\File;


class HappilearnController extends Controller
{
    

    public function importHappiLearnContent(Request $request){
        if($request->isMethod('GET'))
        {
            return view('Backend.happilearn.import_happilearn_content');
        }
        if($request->isMethod('POST'))
        {
            $message = [
                    'import_happi_learn.required' => 'Please select file to import.',
                    'import_happi_learn.mimes' => 'Only .xls and .xlsx format file allowed.',
                ];

            $request->validate([
                'import_happi_learn' => 'required|mimes:xls,xlsx'
            ],$message);

            $array = Excel::toArray(new ImportHappiLearnContent, request()->file('import_happi_learn'));
            $data_file = $array[0];
            if(count($data_file) <= 0){
                  return back()->with("error" , "File is empty.");
            }

            $my_key_references = ['language','type','title' , 'profile' , 'parameters','keywords','summary','link','credit','thumbnail' , 'status'];
            $file_key_only = array_keys($data_file[0]);
            $array_difference = array_diff($my_key_references, $file_key_only);
            $array_difference = count($array_difference);
            if($array_difference > 0){
                return back()->with("error" , "Mismatch key in excel file.");
            }

            $count_file = count($data_file);
            
            $line=2;

            for($i=0 ; $i < $count_file; $i++){

                if($data_file[$i]["language"] == null){
                    return back()->with('error' ,'Please insert language at line '.$line);
                }

                $is_valid_language = UserLanguage::where('name' , strtolower($data_file[$i]["language"]))->first();
                $all_user_langauges  = UserLanguage::pluck('name');
                if(!$is_valid_language){
                    return back()->with('error' ,"Please select valid language at line ".$line.'. Language should be in '.$all_user_langauges);
                }


                if($data_file[$i]["type"] == null){
                    return back()->with('error' ,'Please insert content type at line '.$line);
                }

                $content_type = array(
                                        'image',
                                        'video',
                                        'blog',
                                        'infographics',
                                    );
                $file_content_type = strtolower($data_file[$i]["type"]);

                if (!in_array($file_content_type, $content_type)) {
                    return back()->with("error" , "Please enter valid content type at line no. ".$line.", it should be image, video, blog, infographics only.");
                }

                if($data_file[$i]["title"] == null){
                    return back()->with('error' ,'Please insert title at line '.$line);
                }
                if($data_file[$i]["profile"] == null){
                    return back()->with('error' ,'Please insert profile at line '.$line);
                }
                if($data_file[$i]["parameters"] == null){
                    return back()->with('error' ,'Please insert parameters at line '.$line);
                }
                if($data_file[$i]["keywords"] == null){
                    return back()->with('error' ,'Please insert keywords at line '.$line);
                }
                if($data_file[$i]["summary"] == null){
                    return back()->with('error' ,'Please insert summary at line '.$line);
                }
                if($data_file[$i]["credit"] == null){
                    return back()->with('error' ,'Please insert credit at line '.$line);
                }

                // if($data_file[$i]["thumbnail"] != null){
                //     $path = 'happilearn_thumbnail/'.$data_file[$i]["thumbnail"];
                //     $exists = Storage::disk('s3')->exists($path);
                    // if($exists == false){
                    //     return back()->with('error' ,'Invalid thumbnail at line '.$line);
                    // }
                // } 


                // return $data_file[$i]["status"];
                // return $line;

                if($data_file[$i]["status"] == null){
                    return back()->with('error' ,'Please enter status at line '.$line);
                }

                $status_type = array(
                                        'free',
                                        'paid',
                                    );
                $file_status_type = strtolower($data_file[$i]["status"]);

                if (!in_array($file_status_type, $status_type)) {
                    return back()->with("error" , "Please enter valid status at line no. ".$line.", it should be free or paid.");
                }


                $line ++;


            }

            for($i=0 ; $i < $count_file; $i++){

                $language = strtolower( $data_file[$i]["language"]);
                $type = strtolower( $data_file[$i]["type"]);
                $status = strtolower( $data_file[$i]["status"]);
                $title = $data_file[$i]["title"];
                $profile = strtolower($data_file[$i]["profile"]);
                $parameters = strtolower($data_file[$i]["parameters"]);
                $keywords = strtolower( $data_file[$i]["keywords"]);
                $summary = $data_file[$i]["summary"];
                $link = $data_file[$i]["link"];
                $credit = $data_file[$i]["credit"];
                $thumbnail = $data_file[$i]["thumbnail"];

                $data =[ 
                    'language' => $language,
                    'type' => $type,
                    'status' => $status,
                    'title' => $title,
                    'profile' => $profile,
                    'parameters' => $parameters,
                    'keywords' => $keywords,
                    'summary' => $summary,
                    'link' => $link,
                    'credit' => $credit,
                    'thumbnail' => $thumbnail,
                ];

                $create_question = HappiLearnContent::create($data);

            }
            return back()->with('success' ,'HappiLearn content import successfully.');

        }
    }


    public function happiLearnContentList(Request $request){
        // $content = HappiLearnContent::orderBy('id','desc')->get();
        $perPage = $request->get('per_page', 10);
        $content = HappiLearnContent::where('is_deleted' , 0)->orderBy('id','desc')->paginate($perPage)
            ->appends($request->except('page'));

        return view('Backend.happilearn.happilearn_content_list')->with('content' , $content);
    }

    /**
     * Users who are using HappiLEARN, with how much content they opened / liked.
     */
    public function happiLearnUserList(Request $request){
        if (!auth('admin')->user()->hasAnyRole(['admin', 'super-admin', 'HappiLEARN'])) {
            return redirect(route('admin.dashboard'));
        }

        $perPage = $request->get('per_page', 10);
        $hasViewCount = $this->hasViewCountColumn();

        // Same definition as the sidebar / dashboard HappiLEARN count (UserProductService)
        $query = User::has('happilearnViews')
            ->with('userToken.token.organization')
            ->withCount(['happilearnViews as contents_viewed_count', 'happilearnLikes as contents_liked_count'])
            ->withMax('happilearnViews as last_viewed_at', 'updated_at');

        if ($hasViewCount) {
            $query->withSum('happilearnViews as total_views_count', 'view_count');
        }

        if ($request->filled('username')) {
            $search = $request->input('username');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                    ->orWhere('nickname', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderByDesc('last_viewed_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->except('page'));

        $summary = [
            'users'       => User::has('happilearnViews')->count(),
            'views'       => $hasViewCount
                                ? (int)RecentlyViewedHappiLearnContent::sum('view_count')
                                : RecentlyViewedHappiLearnContent::count(),
            'likes'       => LikeHappiLearnContent::count(),
            'contents'    => RecentlyViewedHappiLearnContent::distinct('happi_learn_content_id')->count('happi_learn_content_id'),
        ];

        return view('Backend.happilearn.happilearn_user_list')
            ->with('users', $users)
            ->with('summary', $summary)
            ->with('hasViewCount', $hasViewCount);
    }

    /**
     * What a single user has used in HappiLEARN.
     */
    public function happiLearnUserDetail(Request $request, $user_id){
        if (!auth('admin')->user()->hasAnyRole(['admin', 'super-admin', 'HappiLEARN'])) {
            return redirect(route('admin.dashboard'));
        }

        $user = User::findOrFail($user_id);

        $views = RecentlyViewedHappiLearnContent::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->get();
        $likedIds = LikeHappiLearnContent::where('user_id', $user->id)
            ->pluck('happi_learn_content_id')
            ->all();

        $contentIds = $views->pluck('happi_learn_content_id')->merge($likedIds)->unique();
        // include deleted content too, so history stays complete
        $contents = HappiLearnContent::whereIn('id', $contentIds)->get()->keyBy('id');

        $rows = $views->map(function ($view) use ($contents, $likedIds) {
            $content = $contents->get($view->happi_learn_content_id);
            return (object)[
                'content'     => $content,
                'views'       => (int)($view->view_count ?? 1),
                'first_viewed'=> $view->created_at,
                'last_viewed' => $view->updated_at,
                'liked'       => in_array($view->happi_learn_content_id, $likedIds),
            ];
        });

        // liked but never opened (e.g. liked from the list)
        $viewedIds = $views->pluck('happi_learn_content_id')->all();
        foreach (array_diff($likedIds, $viewedIds) as $contentId) {
            $rows->push((object)[
                'content'     => $contents->get($contentId),
                'views'       => 0,
                'first_viewed'=> null,
                'last_viewed' => null,
                'liked'       => true,
            ]);
        }

        return view('Backend.happilearn.happilearn_user_detail')
            ->with('user', $user)
            ->with('rows', $rows)
            ->with('hasViewCount', $this->hasViewCountColumn());
    }

    private function hasViewCountColumn(): bool
    {
        return Schema::hasColumn('recently_viewed_happi_learn_contents', 'view_count');
    }
 



    public function uploadLearnMedia(Request $request){
        
        if($request->isMethod('GET')){
            return view('Backend.happilearn.upload_learn_media');
        }
        if($request->isMethod('post')){

            $request->validate([
                // 'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'file' => 'required|file|mimes:jpeg,png,jpg',
                'media_type' => 'required',
            ]);


            if($request->media_type == 'content'){
                $assetName = "happiLearn_content";                
            }else{
                $assetName = "happiLearn_thumbnail";                
            }

            $destinationPath  = config('constants.mediaAssets.' . $assetName . '.folderName');

            $fileName = request()->file('file')->getClientOriginalName();

            if (request()->hasFile('file')) {
                request()->file('file')->storeAs($destinationPath, $fileName, 's3');
            }

            return back()->with('success' , 'Media imported successfully.');

        }

    }
 


    public function deleteHappiLearnContent(Request $request , $id){
        HappiLearnContent::where('id', $id)->update(["is_deleted" => 1]);
        return back()->with('success' , 'Delete successfully.');
    }





}