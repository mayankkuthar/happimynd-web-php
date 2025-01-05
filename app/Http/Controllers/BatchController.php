<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\Category;
use App\Models\UserProfile;
use App\Services\ApiResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    private $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function getAllBatches(Request $request)
    {
        $batches = Batch::whereHas('userProfile')->with(['userProfile' => function ($query) {
            $query->withCount(['users' => function ($query) {
                $query->whereHas('assessment', function ($query) {
                    $query->whereNull('ended_at');
                });
            }]);
        }])
            ->withCount('questions')
            ->orderBy('id')->get();
        $userProfiles = UserProfile::all();
        return view('Backend.assessment.batch')->with('batches', $batches)->with('userProfiles', $userProfiles);
    }

    public function getBatchDetail(Request $request)
    {
        $batch = Batch::with(['batchCategory.category' => function ($query) {
            $query->withCount('question');
        }])->find($request->input('batch_id'));
        return $this->apiResponseService->success($batch);
    }

    public function cloneBatch(Request $request)
    {
        // dd($request->input('batch_id'));
        $batch = Batch::with(['batchCategory' => function ($query) use ($request) {
            $query->whereHas('category', function ($query) use ($request) {
                $query->whereIn('id', $request->category)->with('category');
            })->with('questions')->with('reportCharacteristic');
        }])
            ->find($request->input('batch_id'));
        try {
            DB::beginTransaction();

            //replicate batch
            if ($batch) {
                $new_batch = $batch->replicate();
                $new_batch->name = $request->input('batchName');
                $new_batch->user_profile_id = $request->input('userProfileId');
                $new_batch->push();
            }

            //replicate categories, batchCategories, reportCharacteristics
            $new_categories = [];
            $batchCategories = [];
            foreach ($batch->batchCategory as $batchCategory) {
                $new_c = $batchCategory->category->replicate();
                $new_c->setRelations([])->push();
                $new_categories[] = $new_c;
                $new_bc = BatchCategory::create([
                    'batch_id' => $new_batch->id,
                    'category_id' => $new_c->id,
                    'calculation_step_macro' => $batchCategory->calculation_step_macro,
                    'sort_order' => $batchCategory->sort_order
                ]);
                foreach ($batchCategory->questions as $question) {
                    $new_question = $question->replicate();
                    $new_question->batch_category_id = $new_bc->id;
                    $new_question->category_id = $new_c->id;
                    $new_question->save();
                    foreach ($question->optionQuestion as $optionQuestion) {
                        $new_option_question = $optionQuestion->replicate();
                        $new_option_question->question_id = $new_question->id;
                        $new_option_question->save();
                    }
                }
                foreach ($batchCategory->reportCharacteristic as $rp) {
                    $new_rp = $rp->replicate();
                    $new_rp->category_id = $new_c->id;
                    $new_rp->batch_category_id = $new_bc->id;
                    $new_rp->save();
                }
            }
            DB::commit();
        } catch (\PDOException $e) {
            \Log::critical($e->getMessage());
            DB::rollBack();
            return $this->apiResponseService->error('failed duplicating batch');
        }
        return $this->apiResponseService->success(true);
    }

    public function getAllBatchCategories(Request $request)
    {
        $batch = Batch::with('batchCategory')->find($request->batch_id);
        $batchCategories = BatchCategory::where('batch_id', '!=', $batch->id)->with(['category' => function ($query) {
            $query->withCount('question')->with('batch.userProfile');
        }])->whereHas('category.batch')->get();
        return $this->apiResponseService->success($batchCategories->pluck('category'));
    }

    public function copyCategoryIntoBatch(Request $request)
    {
        try {
            $batch = Batch::find($request->batch_id);
            $categoryIds = $request->category;
            $batchCategories = BatchCategory::whereIn('category_id', $categoryIds)->get();
            $count = $batchCategories->count();
            foreach ($batchCategories as $batchCategory) {
                $new_c = $batchCategory->category->replicate();
                $new_c->setRelations([])->push();
                $new_categories[] = $new_c;
                $new_bc = BatchCategory::create([
                    'batch_id' => $batch->id,
                    'category_id' => $new_c->id,
                    'calculation_step_macro' => $batchCategory->calculation_step_macro,
                    'sort_order' => $batchCategory->sort_order
                ]);
                foreach ($batchCategory->questions as $question) {
                    $new_question = $question->replicate();
                    $new_question->batch_category_id = $new_bc->id;
                    $new_question->category_id = $new_c->id;
                    $new_question->save();
                    foreach ($question->optionQuestion as $optionQuestion) {
                        $new_option_question = $optionQuestion->replicate();
                        $new_option_question->question_id = $new_question->id;
                        $new_option_question->save();
                    }
                }
                foreach ($batchCategory->reportCharacteristic as $rp) {
                    $new_rp = $rp->replicate();
                    $new_rp->category_id = $new_c->id;
                    $new_rp->batch_category_id = $new_bc->id;
                    $new_rp->save();
                }
            }
            DB::commit();
            return $this->apiResponseService->success("$count categories copied into $batch->name batch");
        } catch (Exception $e) {
            \Log::error($e);
            DB::rollBack();
            return $this->apiResponseService->error('failed copying Categories');
        }
    }

    public function addBatch(Request $request)
    {

        $result = Batch::create([
            'name' => $request->input('batchName'),
            'user_profile_id' => intval($request->input('userProfileId'))
        ]);
        if ($result) {
            $request->session()->flash('success', "Batch Created");
        } else {
            $request->session()->flash('error', "error adding batch");
        }
        return redirect(route('admin.getAllBatches.get'));
    }

    public function editBatch(Request $request)
    {
        return Batch::where('id', $request->input('batchId'))->update([
            'name' => $request->input('batchName'),
            'user_profile_id' => $request->input('userProfileId')
        ]);
    }

    public function deleteBatch(Request $request)
    {
        $result = Batch::destroy($request->input('batch_id'));
        $responseData = [];
        if ($result) {
            $responseData = [
                'notify' => [
                    'type' => 'success',
                    'message' => 'Batch Deleted'
                ]
            ];
        } else {
            $responseData = [
                'notify' => [
                    'type' => 'error',
                    'message' => 'Unable to delete batch'
                ]
            ];
        }
        return $this->apiResponseService->success($responseData);
    }

    public function allocateCategoryToBatch(Request $request)
    {
        $batches = Batch::whereHas('userProfile')->orderBy('id')->get();
        return view('Backend.assessment.allocateCategoryBatch')->with('batches', $batches);
    }

    public function updateBatchCategory(Request $request)
    {
        $allocatedCategoryIds = explode(',', $request->input('allocated_category'));
        $unallocatedCategoryIds = explode(',', $request->input('unallocated_category'));
        $batchId = $request->input('batch_id');
        $batchCategoryIds = BatchCategory::where('batch_id', $batchId)->get('category_id')->pluck('category_id')->toArray();
        $toDelete = array_intersect($unallocatedCategoryIds, $batchCategoryIds);
        if ($toDelete) {
            BatchCategory::whereIn('category_id', $toDelete)->delete();
        }
        $toAdd = null;
        if ($allocatedCategoryIds && $allocatedCategoryIds[0] != "") {
            $toAdd = array_diff($allocatedCategoryIds, $batchCategoryIds);
        }
        if ($toAdd) {
            $batchCategory = [];
            foreach ($toAdd as $categoryId) {
                $batchCategoryCheck = BatchCategory::withTrashed()->where('category_id', $categoryId)->where('batch_id', $batchId)->first();
                if ($batchCategoryCheck) {
                    $batchCategoryCheck->restore();
                } else {
                    array_push($batchCategory, [
                        'category_id' => $categoryId,
                        'batch_id' => $batchId
                    ]);
                }
            }
            BatchCategory::insert($batchCategory);
        }
        return $this->apiResponseService->success(
            [
                'notify' => [
                    'message' => 'saved',
                    'type' => 'success'
                ]
            ]
        );
    }

    public function getBatchCategories(Request $request)
    {
        $batchId = $request->input('batch_id');
        $batch = Batch::with(['batchCategory' => function ($query) {
            $query->whereHas('category')->orderBy('category_id', 'ASC')->with('category')->withCount('questions');
        }])
            ->whereHas('userProfile')
            ->with('userProfile')
            ->withCount('questions')
            ->where('id', $batchId)
            ->first();
        $allocatedCategoryIds = BatchCategory::get('category_id')->pluck('category_id')->toArray();
        $allocatedData = $batch;
        $unAllocatedData = "";
        if ($batch->batchCategory) {
            $unAllocatedData = Category::whereNotIn('id', $allocatedCategoryIds)->withCount('question')->get();
        }
        $preparedData = [
            'allocated' => $allocatedData,
            'unallocated' => $unAllocatedData
        ];
        return $this->apiResponseService->success($preparedData);
    }

    public function saveCalulcationStep(Request $request)
    {
        $batchCategory = BatchCategory::where('category_id', $request->input('category_id'))
            ->where('batch_id', $request->input('batch_id'))
            ->first();
        if ($batchCategory) {
            $batchCategory->calculation_step_macro = $request->input('calculation_step');
            $batchCategory->save();
            return $this->apiResponseService->success([
                'notify' => [
                    'type' => 'success',
                    'message' => 'Calculation step saved'
                ], $batchCategory
            ]);
        }
    }

    public function getBatchUniqueCategory(Request $request)
    {
        $batchId = $request->input('batch_id');
        $batch = Batch::with(['batchCategory' => function ($query) {
            $query->whereHas('category', function ($query) {
                $query->whereColumn('name', 'acronymn');
            })->with('category');
        }])->with('userProfile')->find($batchId);
        return $this->apiResponseService->success($batch);
    }
}
