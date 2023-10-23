<?php

namespace App\Exports;

use App\Models\PatientAssessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatientAssessmentsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return PatientAssessment::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.patient-assessment.columns.id'),
            trans('admin.patient-assessment.columns.nikshay_id'),
            trans('admin.patient-assessment.columns.patient_name'),
            trans('admin.patient-assessment.columns.age'),
            trans('admin.patient-assessment.columns.gender'),
            trans('admin.patient-assessment.columns.patient_selected_data'),
            trans('admin.patient-assessment.columns.PULSE_RATE'),
            trans('admin.patient-assessment.columns.TEMPERATURE'),
            trans('admin.patient-assessment.columns.BLOOD_PRESSURE'),
            trans('admin.patient-assessment.columns.RESPIRATORY_RATE'),
            trans('admin.patient-assessment.columns.OXYGEN_SATURATION'),
            trans('admin.patient-assessment.columns.TEXT_BMI'),
            trans('admin.patient-assessment.columns.TEXT_MUAC'),
            trans('admin.patient-assessment.columns.PEDAL_OEDEMA'),
            trans('admin.patient-assessment.columns.GENERAL_CONDITION'),
            trans('admin.patient-assessment.columns.TEXT_ICTERUS'),
            trans('admin.patient-assessment.columns.TEXT_HEMOGLOBIN'),
            trans('admin.patient-assessment.columns.COUNT_WBC'),
            trans('admin.patient-assessment.columns.TEXT_RBS'),
            trans('admin.patient-assessment.columns.TEXT_HIV'),
            trans('admin.patient-assessment.columns.TEXT_XRAY'),
            trans('admin.patient-assessment.columns.TEXT_HEMOPTYSIS'),
        ];
    }

    /**
     * @param PatientAssessment $patientAssessment
     * @return array
     *
     */
    public function map($patientAssessment): array
    {
        return [
            $patientAssessment->id,
            $patientAssessment->nikshay_id,
            $patientAssessment->patient_name,
            $patientAssessment->age,
            $patientAssessment->gender,
            $patientAssessment->patient_selected_data,
            $patientAssessment->PULSE_RATE,
            $patientAssessment->TEMPERATURE,
            $patientAssessment->BLOOD_PRESSURE,
            $patientAssessment->RESPIRATORY_RATE,
            $patientAssessment->OXYGEN_SATURATION,
            $patientAssessment->TEXT_BMI,
            $patientAssessment->TEXT_MUAC,
            $patientAssessment->PEDAL_OEDEMA,
            $patientAssessment->GENERAL_CONDITION,
            $patientAssessment->TEXT_ICTERUS,
            $patientAssessment->TEXT_HEMOGLOBIN,
            $patientAssessment->COUNT_WBC,
            $patientAssessment->TEXT_RBS,
            $patientAssessment->TEXT_HIV,
            $patientAssessment->TEXT_XRAY,
            $patientAssessment->TEXT_HEMOPTYSIS,
        ];
    }
}
