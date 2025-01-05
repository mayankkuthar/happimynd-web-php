<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataContent;
use App\Models\DataGroup;
use App\Models\OfferScreenContent;



class FaqTermController extends Controller
{
    

    public function generalFaqs(Request $request){
        $general_faqs = DataContent::where('data_group_id' , 4)->where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'General faqs get successfully.' , 'general_faqs' => $general_faqs]);
    }



    public function orgFaqs(Request $request){
        $organization_faqs = DataContent::where('data_group_id' , 5)->where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Organization faqs get successfully.' , 'organization_faqs' => $organization_faqs]);
    }



    public function privacyPolicy(Request $request){
        // $privacy_policy = $dataContent = DataGroup::where('name', 'terms-and-services')->with('content')->first();
        $privacy_policy = DataContent::where('data_group_id' , 3)->where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Privacy policy get successfully.' , 'data' => $privacy_policy]);


    }



    public function termConditions(Request $request){
        // $term_condition = DataGroup::with('content')->where('name', 'termsandservices')->first();
        $term_condition = DataContent::where('data_group_id' , 6)->where('deleted_at' , null)->get();
        return response()->json(['status' => 'success' , 'message' => 'Terma nd conditions get successfully.' , 'data' => $term_condition]);


    }


    public function offerScreenContent(){

        $data = OfferScreenContent::first();

        return response()->json(['status' => 'success' , 'message' => 'Offer Content get successfully.' , 'data' => $data ]);
    }


}
