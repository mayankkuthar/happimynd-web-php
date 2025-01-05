<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HappiselfCourse;
use App\Models\UserLanguage;
use App\Models\HappiselfSubCourse;
use App\Models\HappiselfContent;
use App\Models\HappiselfQuestionOption;
use App\Models\HappiselfLibrary;
use App\Models\HappiselfLibraryContent;
use App\Models\HappiselfUsersLastVisitSubCourseAndContent;



use App\Imports\ImportHappiselfContent;
use App\Imports\ImportHappiselfLibraryContent;
use App\Jobs\SendNotificationJob;

use Excel;
use DB;

use App\Exports\HappiSelfExport;

class HappiselfController extends Controller
{
        
    public function dateForExportSelfdata(Request $request){
        return view('happiself/date_for_export_data');
    }


    public function getSelfdata(Request $request){
 
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        $file_name = 'HappiSELF'.date('Y_m_d_H_i_s').'.csv';
        return Excel::download(new HappiSelfExport($data) , $file_name);
       
    }


    public function happiselfCoursesList(Request $request){
        $course_list = HappiselfCourse::where('deleted_at' , null)->orderBy('id' , 'desc')->get();
        return view('happiself/happiself_courses_list')->with('course_list',$course_list);
    }

    public function addHappiselfCourses(Request $request){
        if($request->isMethod('GET')){
            $user_language = UserLanguage::get();
            return view('happiself/add_courses')->with('user_language' , $user_language);
        }
        if($request->isMethod('POST')){
            $user_language = $request->language;
            $message = 'Life is full of ups & downs. We are here to equip you to handle the downs and maintain the ups! Check out new modules added to HappiSELF today!';
            $type = 'happiself_subscribed_serives';

            dispatch(new SendNotificationJob($user_language, $message , $type));

            $data = [
                'language' => $request->language,
                'course_name' => ucfirst($request->course_name),
            ];

            $is_created = HappiselfCourse::create($data);
            if($is_created){
                return redirect('admin/happiself-courses-list')->with('success' , 'Course has been added successfully.');
            }else{
                return redirect('admin/happiself-courses-list')->with('error' , 'Unable to add course.');
            }


        }
    }




    public function editHappiselfCourse(Request $request , $id){
        if($request->isMethod('GET')){
            $course_detail = HappiselfCourse::where('id' , $id)->first();
            return view('happiself/edit-course')->with('course_detail' , $course_detail);
        }
        if($request->isMethod('POST')){
            $course_name =  ucfirst($request->course_name);

            $check_course_name_already_exist = HappiselfCourse::where('course_name' , $course_name)->where('id' , '!=' , $id)->first();
            if($check_course_name_already_exist){
                return back()->with('error' , 'Course name alraedy exist');
            }
            HappiselfCourse::where('id' , $id)->update(['course_name' => $course_name]);

            return redirect('admin/happiself-courses-list')->with('success' , 'Course has been edited successfully.');

        }
    }

    public function addSubCourse(Request $request , $id){

        if($request->isMethod('GET')){
            $couse_detail = HappiselfCourse::where('id' , $id)->first();
            $sub_course_list_of_course = HappiselfSubCourse::where('happiself_course_id' , $id)->where('deleted_at' , null)->orderBy('id','desc')->get();

            return view('happiself/add_sub_course')->with('couse_detail',$couse_detail)->with('sub_course_list_of_course',$sub_course_list_of_course);
        }
        if($request->isMethod('POST')){

            $count_of_after_sub_course =  $request->count_for_sequence;
            if($count_of_after_sub_course == null){
                $count_of_after_sub_course = 0;
            }

            $selectd_after_sub_course_details = HappiselfSubCourse::where('happiself_course_id' , $id)->where('count_for_sequence',$count_of_after_sub_course)->first();


            $count_for_new_sub_course = $count_of_after_sub_course + 1;

            $above_sub_courses = HappiselfSubCourse::where('happiself_course_id' , $id)->where('count_for_sequence' , '>' , $count_of_after_sub_course)->get();

            foreach($above_sub_courses as $row){
                $new_count = $row->count_for_sequence+1 ;
                $data = [
                    'count_for_sequence' => $new_count,
                ];
                HappiselfSubCourse::where('id' , $row->id)->update($data);
            }

            $data = [
                'happiself_course_id' => $id,
                'sub_course_name' => ucfirst($request->sub_course_name),
                'count_for_sequence' => $count_for_new_sub_course,
            ];

            HappiselfSubCourse::create($data);
            
            return redirect('admin/happiself-courses-list')->with('success' , 'Sub course has been added successfully.');

        }
    }



    public function editSubCourse(Request $request , $id){

        $sub_couse_detail = HappiselfSubCourse::where('id' , $id)->first();
        $couse_detail = HappiselfCourse::where('id' , $sub_couse_detail->happiself_course_id)->first();
        $sub_course_list_of_course = HappiselfSubCourse::where('happiself_course_id' , $sub_couse_detail->happiself_course_id)->where('deleted_at' , null)->where('id' , '!=' ,$sub_couse_detail->id)->get();

        if($request->isMethod('GET')){
            return view('happiself/edit_happiself_sub_course')->with('couse_detail',$couse_detail)->with('sub_couse_detail',$sub_couse_detail)->with('sub_course_list_of_course',$sub_course_list_of_course);
        }
        if($request->isMethod('POST')){

            if($request->count_for_sequence == null){
                $data = [
                    'sub_course_name' => ucfirst($request->sub_course_name),
                ];
                HappiselfSubCourse::where('id' , $id)->update($data);
            }else{

                $count_of_after_sub_course =  $request->count_for_sequence;

                $selectd_after_sub_course_details = HappiselfSubCourse::where('happiself_course_id' , $sub_couse_detail->happiself_course_id)->where('count_for_sequence',$count_of_after_sub_course)->first();

                $count_for_new_sub_course = $count_of_after_sub_course + 1;

                $above_sub_courses = HappiselfSubCourse::where('happiself_course_id' , $sub_couse_detail->happiself_course_id)->where('count_for_sequence' , '>' , $count_of_after_sub_course)->get();

                foreach($above_sub_courses as $row){
                    $new_count = $row->count_for_sequence+1 ;
                    $data = [
                        'count_for_sequence' => $new_count,
                    ];
                    HappiselfSubCourse::where('id' , $row->id)->update($data);
                }

                $details = [
                    'sub_course_name' => ucfirst($request->sub_course_name),
                    'count_for_sequence' => $count_for_new_sub_course,
                ];

                HappiselfSubCourse::where('id' , $id)->update($details);

            }
            
            return redirect('admin/view-sub-course'.'/'.$sub_couse_detail->happiself_course_id)->with('success' , 'Sub course has been updated successfully.');
        }
    }




    public function viewSubCourse(Request $request , $id){
        $course_id = $id;
        $sub_course_list = HappiselfSubCourse::where('deleted_at' , null)->where('happiself_course_id' , $id)->orderBy('count_for_sequence' , 'asc')->with('happiselfCourse')->get();
        return view('happiself/view_sub_courses')->with('sub_course_list',$sub_course_list)->with('course_id',$course_id);
    }



    public function deleteSubCourse(Request $request , $sub_course_id){
        // return $sub_course_id;
        HappiselfSubCourse::where('id' , $sub_course_id)->update(['deleted_at' => date('d-m-Y')]);
        return back()->with('success' , 'Sub course has been deleted successfully.');
    }


    public function importHappiself(Request $request){
        if($request->isMethod('GET')){
            // return HappiselfContent::with('option')->get();
            return view('happiself/import_happiself');
        }
        if($request->isMethod('POST')){
            $message = [
                    'import_happiself.required' => 'Please select file to import.',
                    'import_happiself.mimes' => 'Only .xls and .xlsx format file allowed.',
                ];

            $request->validate([
                'import_happiself' => 'required|mimes:xls,xlsx'
            ],$message);

            $array = Excel::toArray(new ImportHappiselfContent, request()->file('import_happiself'));
            $data_file = $array[0];
            if(count($data_file) <= 0){
                  return back()->with("error" , "File is empty.");
            }

            $my_key_references = ['course_id','sub_course_id','title','description','content_type' , 'content','media','is_media_downloadable','correct_answer','option1','option2','option3','option4','option5','option6','option7'];
            $file_key_only = array_keys($data_file[0]);
            $array_difference = array_diff($my_key_references, $file_key_only);
            $array_difference = count($array_difference);
            if($array_difference > 0){
                return back()->with("error" , "Mismatch key in excel file.");
            }

            $count_file = count($data_file);

            $line=2;

            $content_type = array(
                                'audio',
                                'video',
                                'question_mcq',
                                'question_checkbox',
                                'question_match',
                                'text',
                                'short_answer',
                                'linear_scale'
                            );

            for($i=0 ; $i < $count_file; $i++){

                $course_id = $data_file[$i]["course_id"];

                if($course_id == null){
                    return back()->with("error" , "Please enter course ID at line no.".$line);
                }
                $is_course_id_exist = HappiselfCourse::where('id',$course_id)->where('deleted_at' , null)->first();
                if($is_course_id_exist == null){
                    return back()->with("error" , "Please enter valid course ID at line no.".$line);
                }



                $sub_course_id = $data_file[$i]["sub_course_id"];
                
                if($sub_course_id == null){
                    return back()->with("error" , "Please enter sub course ID at line no.".$line);
                }
                $is_sub_course_id_exist = HappiselfSubCourse::where('id',$sub_course_id)->where('deleted_at' , null)->first();
                if($is_sub_course_id_exist == null){
                    return back()->with("error" , "Please enter valid sub course ID at line no.".$line);
                }
                if($is_sub_course_id_exist->happiself_course_id != $course_id){
                    return back()->with("error" , "Sub course ID not belongs to provided course ID at line no.".$line);
                }



                $file_content_type = strtolower($data_file[$i]["content_type"]);
                if($file_content_type == null){
                    return back()->with("error" , "Please enter content type at line no.".$line);
                }
                if (!in_array($file_content_type, $content_type)) {
                    return back()->with("error" , "Please enter valid content type at line no. ".$line.", it should be audio, video, question_checkbox, 'question_mcq', 'question_match, 'text','short_answer', 'linear_scale' only.");
                }



                $content = strtolower($data_file[$i]["content"]);
                if($content == null){
                    return back()->with("error" , "Please enter content at line no.".$line);
                }



                if($data_file[$i]["content_type"] == 'question_checkbox' || $data_file[$i]["content_type"] == 'question_mcq'){
                    if($data_file[$i]["option1"] == null || $data_file[$i]["option2"] == null){
                        return back()->with("error" , "Optio1 and Option2 are mandatory for question at line no.".$line);
                    }
                    if($data_file[$i]["content_type"] == 'question_mcq' && $data_file[$i]["correct_answer"] == null){
                        return back()->with("error" , "Please enter answer for question at line no.".$line);
                    }
                }


                if($data_file[$i]["content_type"] == 'question_match'){
                    if($data_file[$i]["option1"] == null){
                        return back()->with("error" , "Option1 is mandatory for question at line no.".$line);
                    }
                }



                if($data_file[$i]["is_media_downloadable"] != '' && strtolower($data_file[$i]["is_media_downloadable"]) != 'yes'){
                        return back()->with("error" , "Is media downloadable should be null or yes only at line no.".$line);
                }




                $line = $line+1;
            }


            for($i=0 ; $i < $count_file; $i++){


                if(strtolower($data_file[$i]["is_media_downloadable"] == 'yes')){
                    $is_media_downloadable = 1;
                }else{
                    $is_media_downloadable = 0;
                }



                if($data_file[$i]["title"] != ''){
                    $title = $data_file[$i]["title"];
                }else{
                    $title = null;
                }

                if($data_file[$i]["description"] != ''){
                    $description = $data_file[$i]["description"];
                }else{
                    $description = null;
                }



                if($data_file[$i]["content_type"] == 'question_mcq' || $data_file[$i]["content_type"] == 'question_checkbox'){
                    $data  = [
                        'happiself_course_id' => $data_file[$i]["course_id"],
                        'happiself_sub_course_id' => $data_file[$i]["sub_course_id"],
                        'title' => $title,
                        'description' => $description,
                        'content_type' => $data_file[$i]["content_type"],
                        'content' => $data_file[$i]["content"],
                        'media' => $data_file[$i]["media"],
                        'is_media_downloadable' => $is_media_downloadable,
                        'correct_answer' => $data_file[$i]["correct_answer"],
                    ];
                    $is_created = HappiselfContent::create($data);

                    for ($j=1; $j <= 4; $j++) { 

                        $option = $data_file[$i]["option".$j];
                        
                        if($option != null){ 
                            
                            $data = [
                                'happiself_content_id' => $is_created->id,
                                'question_type'        => 'normal',
                                'option'               => $option,
                            ];
                            HappiselfQuestionOption::create($data);
                        }

                    }
                }
                else if($data_file[$i]["content_type"] == 'question_match'){

                    $data  = [
                        'happiself_course_id' => $data_file[$i]["course_id"],
                        'happiself_sub_course_id' => $data_file[$i]["sub_course_id"],
                        'title' => $title,
                        'description' => $description,
                        'content_type' => $data_file[$i]["content_type"],
                        'content' => $data_file[$i]["content"],
                        'media' => $data_file[$i]["media"],
                        'is_media_downloadable' => $is_media_downloadable,
                    ];
                    $is_created = HappiselfContent::create($data);

                    for ($j=1; $j <= 1; $j++) { 

                        $option = $data_file[$i]["option".$j];
                        
                        if($option != null){ 
                            

                            $option_with_correct_answer =[];
                            $explode_matcch_options = explode(',',$option);
                            $count = count($explode_matcch_options);
                            for ($j=0; $j <$count ; $j++) { 
                                $explode_pair_option = explode('-',$explode_matcch_options[$j]);
                                $data = ['option'=>$explode_pair_option[0],'correct_answer'=>$explode_pair_option[1]];
                                array_push($option_with_correct_answer , $data);
                            }
                            // return $correct_option;

                            $data = [
                                'happiself_content_id' => $is_created->id,
                                'question_type'        => 'match',
                                'option'               => json_encode($option_with_correct_answer),
                            ];
                            HappiselfQuestionOption::create($data);
                        }

                    }
                }

                else if($data_file[$i]["content_type"] == 'linear_scale'){
                    $data  = [
                        'happiself_course_id' => $data_file[$i]["course_id"],
                        'happiself_sub_course_id' => $data_file[$i]["sub_course_id"],
                        'title' => $title,
                        'description' => $description,
                        'content_type' => $data_file[$i]["content_type"],
                        'content' => $data_file[$i]["content"],
                        'media' => $data_file[$i]["media"],
                        'is_media_downloadable' => $is_media_downloadable,
                        'correct_answer' => $data_file[$i]["correct_answer"],
                    ];
                    $is_created = HappiselfContent::create($data);

                    for ($j=1; $j <= 7; $j++) { 

                        $option = $data_file[$i]["option".$j];
                        
                        if($option != null){ 
                            
                            $data = [
                                'happiself_content_id' => $is_created->id,
                                'question_type'        => 'linear_scale',
                                'option'               => $option,
                            ];
                            HappiselfQuestionOption::create($data);
                        }

                    }
                }
                else{
                    $data  = [
                        'happiself_course_id' => $data_file[$i]["course_id"],
                        'happiself_sub_course_id' => $data_file[$i]["sub_course_id"],
                        'title' => $title,
                        'description' => $description,
                        'content_type' => $data_file[$i]["content_type"],
                        'content' => $data_file[$i]["content"],
                        'media' => $data_file[$i]["media"],
                        'is_media_downloadable' => $is_media_downloadable,
                    ];
                    $is_created = HappiselfContent::create($data);
                }

            }

            return back()->with('success' , 'Content has been added successfully.');

        }
    }



    public function happiselfLibraryList(Request $request){
        $library_list = HappiselfLibrary::where('deleted_at' , null)->get();
        return view('happiself/happiself_library_list')->with('library_list',$library_list);
    }



    public function addHappiselflibrary(Request $request){
        if($request->isMethod('GET')){
            $user_language = UserLanguage::get();
            return view('happiself/add_library')->with('user_language' , $user_language);
        }
        if($request->isMethod('POST')){
            $data = [
                'language' => $request->language,
                'library_name' => ucfirst($request->library_name),
            ];

            $is_created = HappiselfLibrary::create($data);
            if($is_created){
                return redirect('admin/happiself-library-list')->with('success' , 'Library has been added successfully.');
            }else{
                return redirect('admin/happiself-library-list')->with('error' , 'Unable to add library.');
            }
        }
    }



    public function importHappiselfLibraryContent(Request $request){
        if($request->isMethod('GET')){
            return view('happiself/import_happiself_library');
        }
        if($request->isMethod('POST')){
            $message = [
                    'import_happiself_library.required' => 'Please select file to import.',
                    'import_happiself_library.mimes' => 'Only .xls and .xlsx format file allowed.',
                ];

            $request->validate([
                'import_happiself_library' => 'required|mimes:xls,xlsx'
            ],$message);


            $array = Excel::toArray(new ImportHappiselfContent, request()->file('import_happiself_library'));
            $data_file = $array[0];
            if(count($data_file) <= 0){
                  return back()->with("error" , "File is empty.");
            }

            $my_key_references = ['library_id','content_type' , 'content'];
            $file_key_only = array_keys($data_file[0]);
            $array_difference = array_diff($my_key_references, $file_key_only);
            $array_difference = count($array_difference);
            if($array_difference > 0){
                return back()->with("error" , "Mismatch key in excel file.");
            }

            $count_file = count($data_file);

            $line=2;

            $content_type = array(
                                'audio',
                                'video',
                            );


            for($i=0 ; $i < $count_file; $i++){

                $library_id = $data_file[$i]["library_id"];

                if($library_id == null){
                    return back()->with("error" , "Please enter library ID at line no.".$line);
                }
                $is_library_id_exist = HappiselfLibrary::where('id',$library_id)->where('deleted_at' , null)->first();
                if($is_library_id_exist == null){
                    return back()->with("error" , "Please enter valid library ID at line no.".$line);
                }

 


                $file_content_type = strtolower($data_file[$i]["content_type"]);
                if($file_content_type == null){
                    return back()->with("error" , "Please enter content type at line no.".$line);
                }
                if (!in_array($file_content_type, $content_type)) {
                    return back()->with("error" , "Please enter valid content type at line no. ".$line.", it should be audio, video only.");
                }



                $content = strtolower($data_file[$i]["content"]);
                if($content == null){
                    return back()->with("error" , "Please enter content at line no.".$line);
                }

                $line = $line+1;
            }


            for($i=0 ; $i < $count_file; $i++){

                $data = [
                    'happiself_library_id' => $data_file[$i]["library_id"],
                    'content_type' => $data_file[$i]["content_type"],
                    'content' => $data_file[$i]["content"],
                ];

                HappiselfLibraryContent::create($data);

            }

            return back()->with('success' , 'Happiself library content has been upload successfully.');
        }
    }




    public function contentList(Request $request , $sub_course_id){
            
        $content = HappiselfContent::where('happiself_sub_course_id' , $sub_course_id)->where('deleted_at' , null)->with('courseName' , 'subCourseName')->get();
        return view('happiself/content_list')->with('content' , $content);
    }



    public function deleteSelfContent(Request $request , $content_id){
        HappiselfContent::where('id' , $content_id)->update(['deleted_at' => Date('h:i:s')]);
        return back()->with('success' , 'Content has been deleted Successfully.');
    }



    public function deleteCourse(Request $request , $course_id){
        HappiselfCourse::where('id' , $course_id)->update(['deleted_at' => date('d-m-Y')]);
        return back()->with('success' , 'Course has been deleted Successfully.');
    }




    public function deleteLibrary(Request $request , $library_id){
        HappiselfLibrary::where('id' , $library_id)->update(['deleted_at' => date('d-m-Y')]);
        return back()->with('success' , 'Library has been deleted Successfully.');
    }




    public function editLibrary(Request $request , $library_id){
        if($request->isMethod('GET')){
            $library_details = HappiselfLibrary::where('id' , $library_id)->first();
            return view('happiself/edit_library')->with('library_details' , $library_details);
        }
        if($request->isMethod('POST')){ 
            HappiselfLibrary::where('id' , $library_id)->update(['library_name' => $request->library_name]);
            return redirect('admin/happiself-library-list')->with('success' , 'Library has been edited successfully.');
        }

    }




    public function viewLibraryContent(Request $request , $library_id){
        // $library_content_list = HappiselfLibraryContent::where('happiself_library_id' , $library_id)->get();
        $library_content_list = DB::table('happiself_library_contents')->where('happiself_library_id' , $library_id)->where('deleted_at',null)->get();

        return view('happiself/view_library_content')->with('library_content_list' , $library_content_list);
    }




    public function deleteLibraryContent(Request $request , $library_content_id){
        HappiselfLibraryContent::where('id' , $library_content_id)->update(['deleted_at' => date('d-m-Y')]);
        return back()->with('success' , 'Library Content has been deleted Successfully.');
    }




    public function uploadSelfMedia(Request $request){
        
        if($request->isMethod('GET')){
            return view('happiself.upload_self_media');
        }
        if($request->isMethod('post')){

            $request->validate([
                // 'file' => 'required|file|mimes:jpeg,png,jpg',
                'file' => 'required',
                'media_type' => 'required',
            ]);


            if($request->media_type == 'happiself_course'){
                $assetName = "happiself_course";                
            }
            else if($request->media_type == 'happiself_course_media'){
                $assetName = "happiself_course_media";                
            }
            else{
                $assetName = "happiself_library";                
            }

            $destinationPath  = config('constants.mediaAssets.' . $assetName . '.folderName');

            $fileName = request()->file('file')->getClientOriginalName();

            if (request()->hasFile('file')) {
                request()->file('file')->storeAs($destinationPath, $fileName, 's3');
            }

            return back()->with('success' , 'Media imported successfully.');

        }

    }
 



}










