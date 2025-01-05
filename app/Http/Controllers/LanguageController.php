<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{

    public function __construct(ApiResponseService $apiService)
    {
        $this->apiService = $apiService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languages = Language::all();

        return view('Backend.language.all')->with('languages', $languages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $language = new Language;
        $language->name = $request->name;
        $language->save();
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Saved'
            ],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function show(Language $language)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function edit(Language $language)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Language $language)
    {
        $language = $language->find($request->id);
        $language->name = $request->name;
        $language->save();
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Updated'
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Language::destroy($request->id);
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Deleted'
            ],
        ]);
    }
}
