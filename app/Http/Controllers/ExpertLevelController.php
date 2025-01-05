<?php

namespace App\Http\Controllers;

use App\Models\DurationType;
use App\Models\ExpertLevel;
use App\Models\Offer;
use App\Models\Package;
use App\Models\Plan;
use App\Services\ApiResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpertLevelController extends Controller
{

    public function __construct(ApiResponseService $apiService)
    {
        $this->apiService  = $apiService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expertLevels = ExpertLevel::all();
        return view('Backend.expertlevel.all')->with('expertLevels', $expertLevels);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $durations = DurationType::OfSessionType()->get();
        // dd($expertLevel);
        return view('Backend.expertlevel.add')
            ->with('durations', $durations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $expertLevel = new ExpertLevel();
            $expertLevel->name = $request->name;
            $expertLevel->save();
            $package = Package::where('name', 'HappiTALK')->first();
            foreach ($request->plans as $requestPlan) {
                $plan = new Plan();
                $plan->package_id = $package->id;
                $plan->duration_type_id = $requestPlan['duration_type_id'];
                $plan->price = $requestPlan['cost-price'];
                $plan->expert_level_id = $expertLevel->id;
                $plan->save();
                if (isset($requestPlan['selling-price']) && isset($requestPlan['cost-price-discount'])) {
                    $offer = new Offer();
                    $offer->name = "";
                    $offer->discount = $requestPlan['cost-price-discount'];
                    $offer->price = $requestPlan['selling-price'];
                    $offer->valid = 1;
                    $offer->start = Carbon::now();
                    $offer->plan_id = $plan->id;
                    $offer->save();
                }
            }
            DB::commit();
            return $this->apiService->successNotify('saved');
        } catch (Exception $e) {
            DB::rollback();
            \Log::error($e);
            return $this->apiService->contactDeveloperError();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExpertLevel  $expertLevel
     * @return \Illuminate\Http\Response
     */
    public function show(ExpertLevel $expertLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExpertLevel  $expertLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, ExpertLevel $expertLevel)
    {
        $expertLevel = $expertLevel->with('plan.duration')->find($request->id);
        $durations = DurationType::OfSessionType()->get();
        // dd($expertLevel);
        return view('Backend.expertlevel.edit')
            ->with('durations', $durations)
            ->with('expertLevel', $expertLevel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExpertLevel  $expertLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExpertLevel $expertLevel)
    {
        try {
            DB::beginTransaction();
            $expertLevel = $expertLevel->find($request->expertLevelId);
            $expertLevel->name = $request->name;
            $expertLevel->save();
            $package = Package::where('name', 'HappiTALK')->first();
            $plans = collect();
            foreach ($request->plans as $durationTypeId => $requestPlan) {
                $plan = Plan::where('duration_type_id', $durationTypeId)->where('expert_level_id', $expertLevel->id)->first();
                if (!$plan) {
                    $plan = new Plan();
                }
                $plan->package_id = $package->id;
                $plan->duration_type_id = $requestPlan['duration_type_id'];
                $plan->price = $requestPlan['cost-price'];
                $plan->expert_level_id = $expertLevel->id;
                $plan->save();
                // return $requestPlan['cost-price'];
                if (isset($requestPlan['selling-price']) && isset($requestPlan['cost-price-discount'])) {
                    $offer = new Offer();
                    if ($plan->offer) {
                        $offer = $plan->offer;
                    }
                    $offer->name = "";
                    $offer->discount = $requestPlan['cost-price-discount'];
                    $offer->price = $requestPlan['selling-price'];
                    $offer->valid = 1;
                    $offer->start = Carbon::now();
                    $offer->plan_id = $plan->id;
                    $offer->save();
                }
                $plan->load('offer');
                $plans->add($plan);
            }
            DB::commit();
            return $this->apiService->successNotify('saved', $plans);
        } catch (Exception $e) {
            DB::rollback();
            \Log::error($e);
            return $this->apiService->contactDeveloperError();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExpertLevel  $expertLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        ExpertLevel::destroy($id);
        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'Deleted'
            ],
        ]);
    }
}
