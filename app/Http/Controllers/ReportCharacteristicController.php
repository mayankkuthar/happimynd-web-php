<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\RatingPictures;
use App\Models\ReportCharacteristic;
use App\Services\ApiResponseService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportCharacteristicController extends Controller
{

    private $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function scoreCalculationView(Request $request)
    {
    }

    public function getAllRatingImages(Request $request)
    {
        $pictures = RatingPictures::all();
        return view('Backend.assessment.pictures')->with('pictures', $pictures);
    }

    public function scoreRatingPictureUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            //TODO: store on s3
            $fileService = new FileService;
            $fileName =  $fileService->saveAsAsset('ratingPicture', 'file');
            if ($fileName) {
                RatingPictures::create(['name' => $fileName]);
            }
            return $request->file('file')->storeAs('assets/Frontend/images/reportemoji', $request->file('file')->getClientOriginalName(), ['disk' => 'public']);
        }
    }

    public function deleteRatingImage(Request $request)
    {
        $ratingPicture = RatingPictures::find($request->id);
        $fileService = new FileService;
        if ($fileService->deleteAssetFile('ratingPicture', $ratingPicture->name)) {
            $ratingPicture->delete();
        }
        return redirect()->back();
    }

    public function scoreCalculation(Request $request)
    {
        $batches = Batch::whereHas('batchCategory.category')->get();
        $pictures = RatingPictures::all();
        return view('Backend.assessment.scoreCalculation')
            ->with('batches', $batches)
            ->with('ratingPictures', $pictures);
    }

    public function getBatchCategoryReportCharacteristics(Request $request)
    {
        $batchCategoryId = BatchCategory::where('category_id', $request->input('category_id'))
            ->where('batch_id', $request->input('batch_id'))
            ->first(['id', 'calculation_step_macro']);
        if ($batchCategoryId) {
            $reportCharacteristics = ReportCharacteristic::where('batch_category_id', $batchCategoryId->id)->with('emoji')->get();
            return $this->apiResponseService->success(['calculation_step' => $batchCategoryId->calculation_step_macro, 'characteristics' => $reportCharacteristics]);
        } else {
            return $this->apiResponseService->error(
                [
                    'notify' => [
                        'message' => 'error occured, try refreshing page.'
                    ]
                ]
            );
        }
    }

    public function saveReportCharacteristic(Request $request)
    {
        $batchCategoryId = BatchCategory::where('category_id', $request->input('category_id'))
            ->where('batch_id', $request->input('batch_id'))
            ->first(['id'])->id;
        $reportCharacteristic = null;
        if (str_contains($request->input('report_characteristic_id'), "new")) {
            //new record to be added
            $reportCharacteristic = new ReportCharacteristic;
            $reportCharacteristic->minimum_score = $request->input('min-score');
            $reportCharacteristic->maximum_score = $request->input('max-score');
            $reportCharacteristic->meter_scale_level_name = $request->input('meter_scale_level_name');
            $reportCharacteristic->summary = $request->input('summary');
            $reportCharacteristic->WOL_fill_area = $request->input('WOL_fill_area');
            $reportCharacteristic->included_in_report = ($request->input('included_in_report') == "on") ? true : false;
            $reportCharacteristic->category_id = $request->input('category_id');
            $reportCharacteristic->show_meter_scale = true;
            $reportCharacteristic->batch_category_id = $batchCategoryId;
            $reportCharacteristic->rating_picture_id = $request->input('rating_picture_id');
            $reportCharacteristic->save();
        } else {
            //update existing record
            $reportCharacteristic = ReportCharacteristic::find($request->input('report_characteristic_id'));
            $reportCharacteristic->minimum_score = $request->input('min-score');
            $reportCharacteristic->maximum_score = $request->input('max-score');
            $reportCharacteristic->meter_scale_level_name = $request->input('meter_scale_level_name');
            $reportCharacteristic->summary = $request->input('summary');
            $reportCharacteristic->WOL_fill_area = $request->input('WOL_fill_area');
            $reportCharacteristic->included_in_report = ($request->input('included_in_report') == "on") ? true : false;
            $reportCharacteristic->category_id = $request->input('category_id');
            $reportCharacteristic->show_meter_scale = true;
            $reportCharacteristic->batch_category_id = $batchCategoryId;
            $reportCharacteristic->rating_picture_id = $request->input('rating_picture_id');
            $reportCharacteristic->save();
        }
        if ($reportCharacteristic) {
            return $this->apiResponseService->success(
                [
                    'notify' => [
                        'type' => 'success',
                        'message' => 'Saved'
                    ],
                    'id' => $reportCharacteristic->id
                ]
            );
        }
        return $this->apiResponseService->error(
            [
                'notify' => [
                    'message' => 'error occured, try refreshing page.'
                ]
            ]
        );
    }

    public function deleteReportCharacteristic(Request $request)
    {
        $result = ReportCharacteristic::destroy($request->input('id'));
        return $this->apiResponseService->success(
            [
                'notify' => [
                    'type' => 'success',
                    'message' => 'Deleted'
                ]
            ]
        );
    }

    public function reportOrder(Request $request)
    {
        $batches = Batch::all();
        return view('Backend.assessment.reportorder')->with('batches', $batches);
    }

    public function saveReportOrder(Request $request)
    {
        $categoryIdList = $request->input('category_id');
        $batch_id = $request->input('batch_id');
        $batch = Batch::with('batchCategory')->find($batch_id);
        if ($batch) {
            foreach ($batch->batchCategory as $batchCategory) {
                $orderId = array_search($batchCategory->category_id, $categoryIdList) + 1;
                $batchCategory->sort_order = $orderId;
                $batchCategory->save();
            }
        }
        return $this->apiResponseService->success([
            'notify' => [
                'title' => 'success',
                'type' => 'success',
                'message' => 'Order updated'
            ],
        ]);
    }
}
