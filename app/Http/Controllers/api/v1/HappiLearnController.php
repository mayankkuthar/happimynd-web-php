<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HappiLearnContent;
use App\Models\LikeHappiLearnContent;
use App\Models\RecentlyViewedHappiLearnContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class HappiLearnController extends Controller
{
    public function HappiLearnContent(Request $request)
    {
        $user = Auth::user();

        $content_type = $request->content_type ? explode(',', $request->content_type) : [];
        $profile = $request->profile ? explode(',', $request->profile) : [];
        $parameters = $request->parameters ? explode(',', $request->parameters) : [];
        $language = $request->language ? explode(',', $request->language) : ['english'];

        $cacheKey = 'happi_content_' . md5(json_encode($request->all()) . '_' . $user->id);

        $data = Cache::remember($cacheKey, 60, function () use ($request, $content_type, $profile, $parameters, $language) {

            $query = HappiLearnContent::query()
                ->select('*') // ✅ FULL DATA (fix images/content)
                ->where('is_deleted', 0)
                ->withCount('likes');

            if ($request->search) {
                $query->where('keywords', 'like', '%' . $request->search . '%');
            }

            if (!empty($content_type)) {
                $query->whereIn('type', $content_type);
            }

            if (!empty($language)) {
                $query->whereIn('language', $language);
            }

            if (!empty($profile)) {
                $query->where(function ($q) use ($profile) {
                    foreach ($profile as $p) {
                        $q->orWhere('profile', 'like', $p . '%');
                    }
                });
            }

            if (!empty($parameters)) {
                $query->where(function ($q) use ($parameters) {
                    foreach ($parameters as $param) {
                        $q->orWhere('parameters', 'like', $param . '%');
                    }
                });
            }

            return $query->orderBy('id', 'desc')->simplePaginate(10);
        });

        // Safety fallback
        $data->getCollection()->transform(function ($item) {
            $item->keywords = $item->keywords ?? '';
            return $item;
        });

        // Recently Viewed (FULL DATA)
        $recentlyViewedRaw = RecentlyViewedHappiLearnContent::where('user_id', $user->id)
            ->with(['HappiLearnContent']) // ✅ FULL RELATION
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $recentlyViewedRaw->transform(function ($item) {
            if ($item->HappiLearnContent) {
                $item->HappiLearnContent->keywords = $item->HappiLearnContent->keywords ?? '';
            }
            return $item;
        });

        $recentlyViewed = [
            'data' => $recentlyViewedRaw
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Content fetched successfully.',
            'data' => $data,
            'recentlyViewed' => $recentlyViewed
        ]);
    }


    public function HappiLearnContentById(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'content_id' => 'required|exists:happi_learn_contents,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $cacheKey = 'content_' . $request->content_id;

        $data = Cache::remember($cacheKey, 300, function () use ($request) {
            return HappiLearnContent::select('*') // ✅ FULL DATA
                ->withCount('likes')
                ->find($request->content_id);
        });

        if ($data) {
            $data->keywords = $data->keywords ?? '';
        }

        // Recently viewed update
        RecentlyViewedHappiLearnContent::updateOrCreate(
            [
                'user_id' => $user->id,
                'happi_learn_content_id' => $request->content_id
            ]
        );

        // Suggestions (FULL DATA)
        $profiles = explode(',', $data->profile);

        $suggested = HappiLearnContent::select('*') // ✅ FULL DATA
            ->where('is_deleted', 0)
            ->where('language', $data->language)
            ->where('id', '!=', $request->content_id)
            ->where(function ($q) use ($profiles) {
                foreach ($profiles as $p) {
                    $q->orWhere('profile', 'like', $p . '%');
                }
            })
            ->withCount('likes')
            ->limit(5)
            ->get();

        $suggested->transform(function ($item) {
            $item->keywords = $item->keywords ?? '';
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Content fetched successfully.',
            'data' => $data,
            'suggested_content' => $suggested
        ]);
    }


    public function likeHappiLearnPost(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'happi_learn_content_id' => 'required|exists:happi_learn_contents,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        $exists = LikeHappiLearnContent::where([
            'user_id' => $user->id,
            'happi_learn_content_id' => $request->happi_learn_content_id
        ])->exists();

        if ($exists) {
            return response()->json(['status' => 'success', 'message' => 'Already liked']);
        }

        LikeHappiLearnContent::create([
            'user_id' => $user->id,
            'happi_learn_content_id' => $request->happi_learn_content_id
        ]);

        return response()->json(['status' => 'success', 'message' => 'Liked successfully']);
    }


    public function unLikeHappiLearnPost(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'happi_learn_content_id' => 'required|exists:happi_learn_contents,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->first()], 400);
        }

        LikeHappiLearnContent::where([
            'user_id' => $user->id,
            'happi_learn_content_id' => $request->happi_learn_content_id
        ])->delete();

        return response()->json(['status' => 'success', 'message' => 'Unliked successfully']);
    }


    public function searchParameters()
    {
        return response()->json([
            'message' => 'Parameters fetched successfully.',
            'data' => [
                'Stress','Anxiety','Depression','Burn Out','Happiness',
                'Internet Addiction','Personality','Self Esteem','Resilience',
                'Job Satisfaction','Substance Abuse','Emotional Regulation',
                'Peer Pressure','Group Conformity','Gaming Disorder',
                'Attention and Concentration','Relationship Issues',
                'Body Image','Well Being'
            ]
        ]);
    }
}