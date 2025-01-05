<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\BundleStatus;
use App\Models\City;
use App\Models\Duration;
use App\Models\DurationType;
use App\Models\ExpertLevel;
use App\Models\Language;
use App\Models\Psychologist;
use App\Models\PsychologistAppointment;
use App\Models\PsychologistAvailability;
use App\Models\PsychologistDateTimeSlots;
use App\Models\User;
use App\Models\Specialization;
use App\Models\Receipt;
use App\Services\ApiResponseService;
use App\Services\BitrixService;
use App\Services\FileService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PsychoLogistResource;
use Hash;

class PsychologistController extends Controller
{

    public function __construct(ApiResponseService $apiService, BitrixService $bitrixService)
    {
        $this->apiService = $apiService;
        $this->bitrix = $bitrixService;
    }

    public function getPsychologistView(Request $request)
    {
        $psychologists = new Psychologist();
        $shouldGetAll = true;
        $psychologists = $psychologists->whereNotNull('slot1');
        if ($request->has('search')) {
            $shouldGetAll = false;
            $psychologists = $psychologists
                ->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhereHas('language', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('expertLevel', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('specialization', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        });
                });
        }
        if ($request->has('city')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('city', function ($query) use ($request) {
                $query->where('name', $request->city);
            });
        }
        if ($request->has('expert_category')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('expertLevel', function ($query) use ($request) {
                $query->where('name', $request->expert_category);
            });
        }
        if ($request->has('specialization')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('specialization', function ($query) use ($request) {
                $query->where('name', $request->specialization);
            });
        }
        if ($request->has('language')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('language', function ($query) use ($request) {
                $query->where('name', $request->language);
            });
        }
        if ($shouldGetAll) {
            $psychologists = Psychologist::with('language:id,name', 'expertLevel:id,name', 'city:id,name', 'specialization:id,name', 'customPrice')->whereNotNull('slot1');
        }
        // DB::enableQueryLog();
        $psychologists = $psychologists->with('customPrice')->where('deleted_at' , null)->take(10)->get();
        $testData = PsychoLogistResource::collection($psychologists);
        // dd($psychologists);
        // dd(DB::getQueryLog());
        $specializations = Specialization::all();
        $expertLevels = ExpertLevel::all();
        $languages = Language::all();
        $cities = City::all();
        return view('Frontend/psychologist/psychologist')
            ->with('specializations', $specializations)
            ->with('expertLevels', $expertLevels)
            ->with('languages', $languages)
            ->with('cities', $cities)
            ->with('psychologists', $psychologists)
            ->with('testData', $testData);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $psychologists = Psychologist::where('deleted_at' , null)->with('language:id,name', 'expertLevel:id,name', 'city:id,name', 'specialization:id,name')->get();
        return view('Backend.psychologist.all')
            ->with('psychologists', $psychologists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::orderBy('name', 'ASC')->get();
        $specializations = Specialization::all();
        $languages = Language::all();
        $expertLevels = ExpertLevel::whereHas('plan')->get();
        return view('Backend.psychologist.add')
            ->with('cities', $cities)
            ->with('specializations', $specializations)
            ->with('languages', $languages)
            ->with('expertLevels', $expertLevels);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $message = [
              'username.required' => 'Username is required.',
              'username.unique' => 'Username must be Unique',
          ];
      $request->validate([
          'username' => 'required|unique:psychologists,username',
          'commission_percentage' => 'required|gt:1|lt:100',
          'price_per_session' => 'required|gt:1|lt:5000',
          'tds_percentage' => 'required|gt:1|lt:5000'
      ],$message); 

        $psychologist = new Psychologist();
        $psychologist->first_name = $request->first_name;
        $psychologist->last_name = $request->last_name;
        $psychologist->username = $request->username;
        $psychologist->email = $request->email;
        $psychologist->password = Hash::make($request->password);
        $psychologist->summary = $request->summary;
        $psychologist->city_id = $request->city_id;
        $psychologist->expert_level_id = $request->expert_level_id;

        $psychologist->commission_percentage = $request->commission_percentage;
        $psychologist->price_per_session = $request->price_per_session;
        $psychologist->tds_percentage = $request->tds_percentage;

        
        $psychologist->profile_picture = "";
        if ($request->hasFile('picture')) {
            $fileService = new FileService;
            $psychologist->profile_picture =
                $fileService->saveAsAsset('psychologist.profilePicture', 'picture');
        }
        if ($psychologist->save()) {
            $psychologist->specialization()->sync($request->specialization_id);
            $psychologist->language()->sync($request->language_id);
            try {
                  \Mail::to($psychologist->email)->send(new \App\Mail\RegistrationSucessMail($request));
                } catch (\Exception $e) {
                  // echo $e->getMessage();
                  // die();
                }
        }

        return redirect()->back()->with('Success', 'Saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Psychologist  $psychologist
     * @return \Illuminate\Http\Response
     */
    public function show(Psychologist $psychologist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Psychologist  $psychologist
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Psychologist $psychologist)
    {
        $psychologist = $psychologist->with('customPrice.duration', 'expertLevel.plan.duration')->find($request->id);
        if ($psychologist) {
            $psychologist->load('language', 'specialization', 'city', 'expertLevel', 'availability');
        }
        $slots = $psychologist->availability->groupBy('date')->toArray();
        // dd(array_column($slots['2021-06-08'], 'time'));
        // dd($psychologist->availability()->get());
        $customPlans = $psychologist->customPrice->keyBy('duration_type_id');
        $psychologistPlans = $psychologist->expertLevel->plan->keyBy('duration_type_id');
        $cities = City::all();
        $specializations = Specialization::all();
        $languages = Language::all();
        $expertLevels = ExpertLevel::all();
        $durations = DurationType::ofSessionType()->get();
        return view('Backend.psychologist.edit')
            ->with('psychologist', $psychologist)
            ->with('customPlans', $customPlans)
            ->with('psychologistPlans', $psychologistPlans)
            ->with('cities', $cities)
            ->with('durations', $durations)
            ->with('specializations', $specializations)
            ->with('languages', $languages)
            ->with('expertLevels', $expertLevels)
            ->with('slots', $slots);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Psychologist  $psychologist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Psychologist $psychologist)
    {

        $message = [
              'commission_percentage.required' => 'Commission percentage is required.',
              'commission_percentage.min' => 'Commission percentage must be greater than 1.',
              'commission_percentage.max' => 'Commission percentage must be less than 100.',
        ];
        $request->validate([
              'commission_percentage' => 'required|gt:1|lt:100',
              'price_per_session' => 'required|gt:1|lt:5000'
        ],$message);

        try {
            DB::beginTransaction();
            $psychologist = $psychologist->find($request->id);

            // return $request->email;


            $is_username_already_exist = Psychologist::where('username', $request->username)->where('id','!=',$psychologist->id)->first();
            if($is_username_already_exist){
                return back()->with('error' , 'Username is already exist');
            }

            $is_email_already_exist = Psychologist::where('email', $request->email)->where('id','!=',$psychologist->id)->first();
            if($is_email_already_exist){
                return back()->with('error' , 'Email address is already exist');
            }

            if (!$request->password) {
              $request->merge(['password' => $psychologist->password]);
            }
            $psychologist->first_name = $request->first_name;
            $psychologist->last_name = $request->last_name;

            $psychologist->username = $request->username;
            $psychologist->email = $request->email;

            $psychologist->summary = $request->summary;
            $psychologist->city_id = $request->city_id;
            $psychologist->expert_level_id = $request->expert_level_id;
            $psychologist->password = $request->password;

            $psychologist->commission_percentage = $request->commission_percentage;
            $psychologist->price_per_session = $request->price_per_session;
            $psychologist->tds_percentage = $request->tds_percentage;


            if ($request->hasFile('picture')) {
                $fileService = new FileService;
                $fileService->deleteAssetFile('psychologist.profilePicture', $psychologist->profile_picture);
                $psychologist->profile_picture =
                    $fileService->saveAsAsset('psychologist.profilePicture', 'picture');
            }
            if ($psychologist->save()) {
                if ($request->plans) {
                    $customPlans = array();
                    $psychologist->customPrice()->detach();
                    foreach ($request->plans as $duration_id => $requestPlan) {
                        if (isset($requestPlan['custom-price'])) {
                            $plan = $psychologist->expertLevel->plan;
                            $plan = $plan->where('duration_type_id', $duration_id)->first();
                            $psychologist->customPrice()->attach(
                                [
                                    $plan->id => [
                                        'selling_price' => $requestPlan['selling-price'],
                                        'discount' => $requestPlan['cost-price-discount'],
                                        'cost_price' => $requestPlan['cost-price']
                                    ]
                                ]
                            );
                        }
                    }
                }
                $psychologist->specialization()->sync($request->specialization_id);
                $psychologist->language()->sync($request->language_id);
            }
            DB::commit();
            return redirect()->back()->with('Success', 'Saved');
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Psychologist  $psychologist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $psychologist = Psychologist::find($request->id);
        // $psychologist->delete();
        Psychologist::where('id' , $request->id)->update(['deleted_at' => Date('Y-m-d h:i:s')]);
        $request->session()->flash('success', 'Deleted');
        return redirect()->back();
    }

    public function addDates(Request $request)
    {
        $psychologist_ids = explode(',', $request->psychologist_ids);
        // return $request->date;
        $dayOfWeeks = [
            "SUN" => "Sunday",
            "MON" => "Monday",
            "TUE" => "Tuesday",
            "WED" => "Wednesday",
            "THU" => "Thursday",
            "FRI" => "Friday",
            "SAT" => "Saturday"
        ];
        $count = 0;
        $slot1 = '';
        $slot1time = '';
        $slot2time = '';
        $slot2 = '';
        $availabilities = collect();
        $availabilities_data = [];
        $i = 0;
        foreach ($request->section as $section) {
            if (isset($section['days']) && isset($section['time'])) {
                $days = $section["days"];
                if ($i == 0) {
                    $slot1 = implode(' | ', $days);
                    $slot1time = $section['time'];
                } elseif ($i == 1) {
                    $slot2 = implode(' | ', $days);
                    $slot2time = $section['time'];
                }
                $includeDays = [];
                foreach ($days as $day) {
                    array_push($includeDays, $dayOfWeeks[$day]);
                }
                $dates = explode('to', $section['date']);
                $dates[0] = Carbon::parse(trim($dates[0]));
                $dates[1] = Carbon::parse(trim($dates[1]));
                $slots = $section['slots'];
                $dateRange = CarbonPeriod::create($dates[0], $dates[1]);
                $i = 0;
                $c = 0;
                foreach ($dateRange as $date) {
                    if (in_array($date->format('l'), $includeDays)) {
                        // echo $dayOfWeeks[$includeDays[$i - 1]] . ",";
                        foreach ($slots as $slot) {
                            $availability = PsychologistDateTimeSlots::firstOrCreate(['date' => $date->format('Y-m-d'), 'time' => $slot]);
                            $availabilities->push($availability);
                            array_push($availabilities_data, [
                                'availability_id' => $availability->id,
                                'psychologist_id' => $psychologist_ids[0]
                            ]);
                            if (count($psychologist_ids) > 1) {
                                array_push($availabilities_data, [
                                    'availability_id' => $availability->id,
                                    'psychologist_id' => $psychologist_ids[1]
                                ]);
                            }
                        }
                    }
                }
            }
            $i++;
        }
        foreach ($psychologist_ids as $psychologist_id) {
            if (!empty($slot1) && !empty($slot1time)) {
                $psychologist = Psychologist::find($psychologist_id);
                $psychologist->slot1 = ['days' => $slot1, 'time' => $slot1time];
                if (!empty($slot2) && !empty($slot2time)) {
                    $psychologist->slot2 = ['days' => $slot2, 'time' => $slot2time];
                }
                $psychologist->save();
                $psychologist->availability()->sync($availabilities->pluck('id'));
            }
        }

        return $this->apiService->success([
            'notify' => [
                'type' => 'success',
                'message' => 'time slots saved'
            ],
        ]);
    }

    public function getPsychologistAvailableDates(Request $request)
    {
        $appointment = PsychologistAppointment::where('user_id', auth('user')->user()->id)->first();
        if ($appointment) {
            $psychologist = $appointment->psychologist;
            $availableSlots = $psychologist->availability->groupBy('date')->map(function ($slots) {
                $times = [];
                foreach ($slots as $slot) {
                    array_push($times, $slot->time);
                }
                return $times;
            });
            return $this->apiService->success($availableSlots);
        }
    }

    public function updatePsychologistAppointment(Request $request)
    {
        try {
            $data = [];
            DB::beginTransaction();
            $user = User::find(auth('user')->user()->id);
            $slot = $request->slot;
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $appointment = PsychologistAppointment::where('user_id', $user->id)->first();
            $appointment->date = $date;
            $appointment->time_slot = $slot;
            $appointment->appointment_status = "Booked";
            $is_saved = false;
            if ($appointment->save()) {
                $datetime_slot = PsychologistDateTimeSlots::where('date', $date)->where('time', $slot)->first();
                $psychologist_slot = PsychologistAvailability::where('psychologist_id', $appointment->psychologist_id)->where('psychologist_slot_id', $datetime_slot->id)->whereNull('user_id')->first();
                $psychologist_slot->user_id = $user->id;
                $is_saved = $psychologist_slot->save();
            }
            DB::commit();
            if ($is_saved) {
                $base_price = $appointment->baseSessionCost();
                $receipt = Receipt::where([['user_id', '=', $user->id], ['status', '=', '1']])->first();
                $paid_amount = 0;
                if ($receipt) {
                    $paid_amount = $receipt->amount;
                }
                if (config('constants.bitrix')) {
                    $data['dealCategory'] = "HappiTalkCoordinator";
                    $data['session_appointed'] = $appointment->sessions;
                    $data['session_availed'] = 0;
                    $data['session_remaining'] = $appointment->sessions;
                    $data['base_price'] = $base_price;
                    $data['booked_psychologist_name'] = $appointment->psychologist->full_name;
                    $data['slot'] = $request->slot;
                    $data['booking_date'] = Carbon::parse($appointment->date)->toDateTimeString();
                    $addDealBitrixResponse = $this->bitrix->addDeal($data);
                    if ($addDealBitrixResponse->result) {
                        $appointment = PsychologistAppointment::where('user_id', $user->id)->first();
                        $appointment->dealId = $addDealBitrixResponse->result;
                        $appointment->save();
                        $updateContactResponse =  $this->bitrix->addOrUpdateContactForDeal($addDealBitrixResponse->result, $user);
                        if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                            $bitrixResponse = $this->bitrix->updateDeal(
                                $addDealBitrixResponse->result,
                                $user,
                                array('contactId' => $updateContactResponse->result)
                            );
                        }
                    }
                }
                return $this->apiService->success('appointment booked');
            }
            return $this->apiService->error('unable to book slot');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
            return $this->apiService->error('unable to book slot');
        }
    }

    public function allPsychologistAppointment(Request $request)
    {
        $appointments = PsychologistAppointment::with('user')->with('psychologist')->get();
        $today = Carbon::now();
        $futureAppointments = $appointments->filter(function ($appointment) use ($today) {
            if ($appointment->date > $today)
                return true;
            return false;
        });
        $pastAppointments =
            $appointments->filter(function ($appointment) use ($today) {
                if ($appointment->date < $today)
                    return true;
                return false;
            });
        return view('Backend.psychologist.allAppointments')
            ->with('futureAppointments', $futureAppointments)
            ->with('pastAppointments', $pastAppointments);
    }

    public function getPsychologists(Request $request)
    {
        $psychologists = new Psychologist();
        $shouldGetAll = true;
        $no = isset($request['page_number']) ? $request['page_number'] : 0;
        $nos = 10;
        $skip = $no * $nos;
        $psychologists = $psychologists->whereNotNull('slot1');
        if ($request->has('search')) {
            $shouldGetAll = false;
            $psychologists = $psychologists
                ->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhereHas('language', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('expertLevel', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        })
                        ->orWhereHas('specialization', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        });
                });
        }
        if ($request->has('city')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('city', function ($query) use ($request) {
                $query->where('name', $request->city);
            });
        }
        if ($request->has('expert_category')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('expertLevel', function ($query) use ($request) {
                $query->where('name', $request->expert_category);
            });
        }
        if ($request->has('specialization')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('specialization', function ($query) use ($request) {
                $query->where('name', $request->specialization);
            });
        }
        if ($request->has('language')) {
            $shouldGetAll = false;
            $psychologists = $psychologists->whereHas('language', function ($query) use ($request) {
                $query->where('name', $request->language);
            });
        }
        if ($shouldGetAll) {
            $psychologists = Psychologist::with('language:id,name', 'expertLevel:id,name', 'city:id,name', 'specialization:id,name')->whereNotNull('slot1');
        }
        // DB::enableQueryLog();
        $psychologists = $psychologists->with('customPrice');
        $psychologists = $psychologists->skip($skip)->take(10)->get();
        // dd($psychologists);
        // dd(DB::getQueryLog());
        $specializations = Specialization::all();
        $expertLevels = ExpertLevel::all();
        $languages = Language::all();
        $cities = City::all();
        return response(
            json_encode(array(
                'status' => '1',
                'error_code' => '0',
                'msg' => 'success',
                'psychologists' => PsychoLogistResource::collection($psychologists)
            )),
            200
        );
    }
}
