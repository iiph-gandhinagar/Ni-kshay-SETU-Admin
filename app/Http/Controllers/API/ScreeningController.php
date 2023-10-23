<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Screening;
use App\Models\Subscriber;
use App\Models\Symptom;
use App\Models\TreatmentAlgorithm;
use Validator;

class ScreeningController extends BaseController
{
    public function storeScreening(Request $request)
    {
        $lang = 'en';

        $rules = [
            'age' => 'required',
            'weight' => 'required',
            'height' => 'required',
            'symptoms_selected' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $data = explode(',', $request['symptoms_selected']);
            $countOfPTB = 0;
            $countOfPETB = 0;
            //-------get count of category--------
            foreach ($data as $item) {
                $category = Symptom::where('id', $item)->get('category');
                if ($category[0]['category'] == "1") {
                    $countOfPTB++;
                } else {
                    $countOfPETB++;
                }
            }
            //--------general report--------------------------------------
            $heightinmeter = $request['height'] * 0.01;
            $heightCmsToMeterSquare = $heightinmeter * $heightinmeter;
            $desirableWeight = 21 * $heightCmsToMeterSquare;
            $minimumAcceptableWeight = 18.5 * $heightCmsToMeterSquare;
            $desirableWeightGain = $desirableWeight - $request['weight'];
            $minimumWeightGainRequired = $minimumAcceptableWeight - $request['weight'];
            $desirableDailyCaloricIntake = $desirableWeight * 40;
            $desirableDailyProteinIntake = $desirableWeight * 1.5;
            //---end of general report---------------------------------------

            //-------tb symptoms check---------
            if ($request['age'] > 14 && $countOfPETB >= 1) {
                $detectedTB = "Presumptive Extra-Pulmonary TB";
                $nutritionTitle = "Presumptive Extra-Pulmonary TB";
                $tbId = 2;
            } elseif ($request['age'] > 14 && $countOfPTB >= 2) {
                $detectedTB = "Presumptive Pulmonary TB";
                $nutritionTitle = "Presumptive Pulmonary TB";
                $tbId = 1;
            } elseif ($request['age'] <= 14 && $countOfPETB >= 1) {
                $detectedTB = "Presumptive Extra-Pulmonary Pediatric TB";
                $nutritionTitle = "Presumptive Extra-Pulmonary Pediatric TB";
                $tbId = 2;
            } elseif ($request['age'] <= 14 && $countOfPTB >= 2) {
                $detectedTB = "Presumptive Pulmonary Pediatric TB";
                $nutritionTitle = "Presumptive Pulmonary Pediatric TB";
                $tbId = 3;
            } else {
                $detectedTB = "";
                $nutritionTitle = "Nutrition Outcome Details";
                $tbId = 0;
            }

            //-------bmi result----------
            $userBmi = $request['weight'] / ($heightinmeter * $heightinmeter);
            if ($userBmi >= 18.5 && $userBmi <= 24.9) {
                $BMI = "Normal";
                $linking_BMI = "Normal";
            } elseif ($userBmi >= 25 && $userBmi <= 29.9) {
                $BMI = "Overweight";
                $linking_BMI = "Overweight/Obese";
            } elseif ($userBmi >= 30) {
                $BMI = "Obese";
                $linking_BMI = "Overweight/Obese";
            } elseif ($userBmi >= 17.0 && $userBmi <= 18.4) {
                $BMI = "Mild Underweight";
                $linking_BMI = "Mild and Moderate Underweight";
            } elseif ($userBmi >= 16.0 && $userBmi <= 16.9) {
                $BMI = "Moderately Underweight";
                $linking_BMI = "Mild and Moderate Underweight";
            } elseif ($userBmi <= 14.0) {
                $BMI = "Extremely Underweight";
                $linking_BMI = "Extremely Underweight";
            } elseif ($userBmi <= 16.0) {
                $BMI = "Severely Underweight";
                $linking_BMI = "Severely Underweight";
            } else {
                $BMI = '';
                $linking_BMI = "";
            }

            $id = TreatmentAlgorithm::where('title->' . $lang, $linking_BMI)->get(['id']);

            $result = new \stdClass();
            $result->BMI = $BMI;
            if (count($id) > 0) {
                $result->Treatment_id = $id[0]['id'];
            } else {
                $normalBMI = TreatmentAlgorithm::where('title->' . $lang, 'Normal')->get(['id']);
                $result->Treatment_id = $normalBMI[0]['id'];
            }
            $result->heightinmeter = $heightinmeter;
            $result->currentWeight = $request['weight'];
            $result->heightCmsToMeterSquare = $heightCmsToMeterSquare;
            $result->desirableWeight = $desirableWeight;
            $result->minimumAcceptableWeight = $minimumAcceptableWeight;
            $result->desirableWeightGain = $desirableWeightGain;
            $result->minimumWeightGainRequired = $minimumWeightGainRequired;
            $result->desirableDailyCaloricIntake = $desirableDailyCaloricIntake;
            $result->desirableDailyProteinIntake = $desirableDailyProteinIntake;
            $result->user_bmi = $userBmi;


            $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
            $newRequest = $request->all();
            $newRequest['user_id'] = $user_id[0]['id'];
            $newRequest['age'] = $request['age'];
            $newRequest['weight'] = $request['weight'];
            $newRequest['height'] = $request['height'];
            $newRequest['symptoms_selected'] = $request['symptoms_selected'];
            $newRequest['is_tb'] = 0;
            $newRequest['symptoms_name'] = '';

            if ($detectedTB != "") {
                $newRequest['is_tb'] = 1;
                $newRequest['symptoms_name'] = $detectedTB;
            }
            $result->is_tb = $newRequest['is_tb'];
            $result->detected_tb = $newRequest['symptoms_name'];
            $result->nutritionTitle = $nutritionTitle;
            $result->tbId = $tbId;
            Screening::create($newRequest);
            $success = true;
            return ['status' => $success, 'data' => $result, 'code' => 200];
        }
    }

    public function getAllSymptoms(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
            app()->setLocale($lang);
            $symptoms = Symptom::with(['media'])->get();
            $success = true;
            return ['status' => $success, 'data' => $symptoms, 'code' => 200];
        }
    }
}
