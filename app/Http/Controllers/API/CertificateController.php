<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\UserAssessment;
use PDF;


class CertificateController extends BaseController
{
    public function getAllCertificateDetails(Request $request)
    {
        $id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
        $certificate = UserAssessment::with(['assessment_with_trashed'])->where('user_id', $id[0]['id'])->get();
        $success = true;
        return ['status' => $success, 'data' => $certificate, 'code' => 200];
    }

    public function getCertificate(Request $request, $assessment_id)
    {
        $id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
        $certificate = UserAssessment::with(['assessment_with_trashed', 'user', 'user.cadre', 'assessment_with_trashed.assessment_certificate', 'assessment_with_trashed.assessment_certificate.media'])->where('user_id', $id[0]['id'])->where('assessment_id', $assessment_id)->get();
        if (count($certificate) > 0) {
            $media_obj = $certificate[0]['assessment_with_trashed']['assessment_certificate']['media'][0];
            // Log::info('media/' . $media_obj['id'] . '/' . $media_obj['file_name']);
            $data = [
                'full_name' => $certificate[0]['user']['name'],
                'assessment_name' => $certificate[0]['assessment_with_trashed']['assessment_title'],
                'cadre' => $certificate[0]['user']['cadre']['title'],
                'percentage' => isset($certificate[0]['total_marks']) && $certificate[0]['total_marks'] != 0 ? round(($certificate[0]['obtained_marks'] / $certificate[0]['total_marks']) * 100, 2) : 0,
                'date' => $certificate[0]['created_at'],
                'certificate_url' => 'media/' . $media_obj['id'] . '/' . $media_obj['file_name'],
                'top' => $certificate[0]['assessment_with_trashed']['assessment_certificate']['top'],
                'left' => $certificate[0]['assessment_with_trashed']['assessment_certificate']['left'],
            ];

            $pdf = PDF::loadView('pdf-download', $data);
            header('Access-Control-Allow-Origin: *');
            $content = $pdf->stream("certificate(" . $certificate[0]['user']['name'] . ").pdf");
            $success = true;
        } else {
            $success = false;
            $content = [];
        }

        return ['status' => $success, 'data' => $content, 'code' => 200];
    }
}
