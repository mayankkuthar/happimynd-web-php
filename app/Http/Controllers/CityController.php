<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;

class CityController extends Controller
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
        $cities = City::all();
        return view('Backend.city.all')->with('cities', $cities);
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
        $city = new City;
        $city->name = $request->name;
        $city->save();
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $city = $city->find($request->id);
        $city->name = $request->name;
        $city->save();
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        City::destroy($request->id);
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Deleted'
            ],
        ]);
    }
}
