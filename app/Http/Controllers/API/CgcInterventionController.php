<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\CgcIntervention;
use App\Models\Subscriber;

class CgcInterventionController extends BaseController
{
    public function getAllchapters()
    {
        $chapters = CgcIntervention::get(['id', 'chapter_title']);
        $success = true;
        return ['status' => $success, 'data' => $chapters, 'code' => 200];
    }

    public function getChaptersById($chapterId, Request $request)
    {
        $userId = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id']);
        $chapters = CgcIntervention::where('id', $chapterId)->with(['media', 'assessment' => function ($q) use ($userId) {
            $q->whereRaw("find_in_set('" . $userId[0]['cadre_id'] . "',cadre_id)");
        }])->get();
        $success = true;
        return ['status' => $success, 'data' => $chapters, 'code' => 200];
    }
}
