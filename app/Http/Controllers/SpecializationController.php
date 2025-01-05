<?php

namespace App\Http\Controllers;

use App\Models\Duration;
use App\Models\Specialization;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;

class SpecializationController extends Controller
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
        $specializations = Specialization::all();
        return view('Backend.specialization.all')->with('specializations', $specializations);
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
        $specialization = new Specialization();
        $specialization->name = $request->name;
        $specialization->save();
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
     * @param  \App\Models\Specialization  $specialization
     * @return \Illuminate\Http\Response
     */
    public function show(Specialization $specialization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Specialization  $specialization
     * @return \Illuminate\Http\Response
     */
    public function edit(Specialization $specialization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Specialization  $specialization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Specialization $specialization)
    {
        $specialization = $specialization->find($request->id);
        $specialization->name = $request->name;
        $specialization->save();
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
     * @param  \App\Models\Specialization  $specialization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Specialization::destroy($request->id);
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Deleted'
            ],
        ]);
    }
}
