<div class="row form-inline" style="padding-bottom: 10px;" v-cloak>
    <div :class="{'col-xl-10 col-md-11 text-right': !isFormLocalized, 'col text-center': isFormLocalized, 'hidden': onSmallScreen }">
        <small>{{ trans('brackets/admin-ui::admin.forms.currently_editing_translation') }}<span v-if="!isFormLocalized && otherLocales.length > 1"> {{ trans('brackets/admin-ui::admin.forms.more_can_be_managed') }}</span><span v-if="!isFormLocalized"> | <a href="#" @click.prevent="showLocalization">{{ trans('brackets/admin-ui::admin.forms.manage_translations') }}</a></span></small>
        <i class="localization-error" v-if="!isFormLocalized && showLocalizedValidationError"></i>
    </div>

    <div class="col text-center" :class="{'language-mobile': onSmallScreen, 'has-error': !isFormLocalized && showLocalizedValidationError}" v-if="isFormLocalized || onSmallScreen" v-cloak>
        <small>{{ trans('brackets/admin-ui::admin.forms.choose_translation_to_edit') }}
            <select class="form-control" v-model="currentLocale">
                <option :value="defaultLocale" v-if="onSmallScreen">@{{defaultLocale.toUpperCase()}}</option>
                <option v-for="locale in otherLocales" :value="locale">@{{locale.toUpperCase()}}</option>
            </select>
            <i class="localization-error" v-if="isFormLocalized && showLocalizedValidationError"></i>
            <span>|</span>
            <a href="#" @click.prevent="hideLocalization">{{ trans('brackets/admin-ui::admin.forms.hide') }}</a>
        </small>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('patient_selected_data_{{ $locale }}'), 'has-success': fields.patient_selected_data_{{ $locale }} && fields.patient_selected_data_{{ $locale }}.valid }">
                <label for="patient_selected_data_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.patient-assessment.columns.patient_selected_data') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.patient_selected_data.{{ $locale }}" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('patient_selected_data_{{ $locale }}'), 'form-control-success': fields.patient_selected_data_{{ $locale }} && fields.patient_selected_data_{{ $locale }}.valid }" id="patient_selected_data_{{ $locale }}" name="patient_selected_data_{{ $locale }}" placeholder="{{ trans('admin.patient-assessment.columns.patient_selected_data') }}">
                    <div v-if="errors.has('patient_selected_data_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('patient_selected_data_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('nikshay_id'), 'has-success': fields.nikshay_id && fields.nikshay_id.valid }">
    <label for="nikshay_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.nikshay_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.nikshay_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('nikshay_id'), 'form-control-success': fields.nikshay_id && fields.nikshay_id.valid}" id="nikshay_id" name="nikshay_id" placeholder="{{ trans('admin.patient-assessment.columns.nikshay_id') }}">
        <div v-if="errors.has('nikshay_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('nikshay_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('PEDAL_OEDEMA'), 'has-success': fields.PEDAL_OEDEMA && fields.PEDAL_OEDEMA.valid }">
    <label for="PEDAL_OEDEMA" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.PEDAL_OEDEMA') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.PEDAL_OEDEMA" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('PEDAL_OEDEMA'), 'form-control-success': fields.PEDAL_OEDEMA && fields.PEDAL_OEDEMA.valid}" id="PEDAL_OEDEMA" name="PEDAL_OEDEMA" placeholder="{{ trans('admin.patient-assessment.columns.PEDAL_OEDEMA') }}">
        <div v-if="errors.has('PEDAL_OEDEMA')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('PEDAL_OEDEMA') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_XRAY'), 'has-success': fields.TEXT_XRAY && fields.TEXT_XRAY.valid }">
    <label for="TEXT_XRAY" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_XRAY') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_XRAY" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_XRAY'), 'form-control-success': fields.TEXT_XRAY && fields.TEXT_XRAY.valid}" id="TEXT_XRAY" name="TEXT_XRAY" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_XRAY') }}">
        <div v-if="errors.has('TEXT_XRAY')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_XRAY') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_HIV'), 'has-success': fields.TEXT_HIV && fields.TEXT_HIV.valid }">
    <label for="TEXT_HIV" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_HIV') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_HIV" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_HIV'), 'form-control-success': fields.TEXT_HIV && fields.TEXT_HIV.valid}" id="TEXT_HIV" name="TEXT_HIV" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_HIV') }}">
        <div v-if="errors.has('TEXT_HIV')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_HIV') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_RBS'), 'has-success': fields.TEXT_RBS && fields.TEXT_RBS.valid }">
    <label for="TEXT_RBS" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_RBS') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_RBS" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_RBS'), 'form-control-success': fields.TEXT_RBS && fields.TEXT_RBS.valid}" id="TEXT_RBS" name="TEXT_RBS" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_RBS') }}">
        <div v-if="errors.has('TEXT_RBS')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_RBS') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('COUNT_WBC'), 'has-success': fields.COUNT_WBC && fields.COUNT_WBC.valid }">
    <label for="COUNT_WBC" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.COUNT_WBC') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.COUNT_WBC" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('COUNT_WBC'), 'form-control-success': fields.COUNT_WBC && fields.COUNT_WBC.valid}" id="COUNT_WBC" name="COUNT_WBC" placeholder="{{ trans('admin.patient-assessment.columns.COUNT_WBC') }}">
        <div v-if="errors.has('COUNT_WBC')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('COUNT_WBC') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_HEMOGLOBIN'), 'has-success': fields.TEXT_HEMOGLOBIN && fields.TEXT_HEMOGLOBIN.valid }">
    <label for="TEXT_HEMOGLOBIN" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_HEMOGLOBIN') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_HEMOGLOBIN" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_HEMOGLOBIN'), 'form-control-success': fields.TEXT_HEMOGLOBIN && fields.TEXT_HEMOGLOBIN.valid}" id="TEXT_HEMOGLOBIN" name="TEXT_HEMOGLOBIN" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_HEMOGLOBIN') }}">
        <div v-if="errors.has('TEXT_HEMOGLOBIN')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_HEMOGLOBIN') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_ICTERUS'), 'has-success': fields.TEXT_ICTERUS && fields.TEXT_ICTERUS.valid }">
    <label for="TEXT_ICTERUS" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_ICTERUS') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_ICTERUS" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_ICTERUS'), 'form-control-success': fields.TEXT_ICTERUS && fields.TEXT_ICTERUS.valid}" id="TEXT_ICTERUS" name="TEXT_ICTERUS" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_ICTERUS') }}">
        <div v-if="errors.has('TEXT_ICTERUS')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_ICTERUS') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('GENERAL_CONDITION'), 'has-success': fields.GENERAL_CONDITION && fields.GENERAL_CONDITION.valid }">
    <label for="GENERAL_CONDITION" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.GENERAL_CONDITION') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.GENERAL_CONDITION" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('GENERAL_CONDITION'), 'form-control-success': fields.GENERAL_CONDITION && fields.GENERAL_CONDITION.valid}" id="GENERAL_CONDITION" name="GENERAL_CONDITION" placeholder="{{ trans('admin.patient-assessment.columns.GENERAL_CONDITION') }}">
        <div v-if="errors.has('GENERAL_CONDITION')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('GENERAL_CONDITION') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_BMI'), 'has-success': fields.TEXT_BMI && fields.TEXT_BMI.valid }">
    <label for="TEXT_BMI" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_BMI') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_BMI" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_BMI'), 'form-control-success': fields.TEXT_BMI && fields.TEXT_BMI.valid}" id="TEXT_BMI" name="TEXT_BMI" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_BMI') }}">
        <div v-if="errors.has('TEXT_BMI')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_BMI') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_MUAC'), 'has-success': fields.TEXT_MUAC && fields.TEXT_MUAC.valid }">
    <label for="TEXT_MUAC" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_MUAC') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_MUAC" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_MUAC'), 'form-control-success': fields.TEXT_MUAC && fields.TEXT_MUAC.valid}" id="TEXT_MUAC" name="TEXT_MUAC" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_MUAC') }}">
        <div v-if="errors.has('TEXT_MUAC')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_MUAC') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('patient_name'), 'has-success': fields.patient_name && fields.patient_name.valid }">
    <label for="patient_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.patient_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.patient_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('patient_name'), 'form-control-success': fields.patient_name && fields.patient_name.valid}" id="patient_name" name="patient_name" placeholder="{{ trans('admin.patient-assessment.columns.patient_name') }}">
        <div v-if="errors.has('patient_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('patient_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('OXYGEN_SATURATION'), 'has-success': fields.OXYGEN_SATURATION && fields.OXYGEN_SATURATION.valid }">
    <label for="OXYGEN_SATURATION" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.OXYGEN_SATURATION') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.OXYGEN_SATURATION" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('OXYGEN_SATURATION'), 'form-control-success': fields.OXYGEN_SATURATION && fields.OXYGEN_SATURATION.valid}" id="OXYGEN_SATURATION" name="OXYGEN_SATURATION" placeholder="{{ trans('admin.patient-assessment.columns.OXYGEN_SATURATION') }}">
        <div v-if="errors.has('OXYGEN_SATURATION')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('OXYGEN_SATURATION') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('RESPIRATORY_RATE'), 'has-success': fields.RESPIRATORY_RATE && fields.RESPIRATORY_RATE.valid }">
    <label for="RESPIRATORY_RATE" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.RESPIRATORY_RATE') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.RESPIRATORY_RATE" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('RESPIRATORY_RATE'), 'form-control-success': fields.RESPIRATORY_RATE && fields.RESPIRATORY_RATE.valid}" id="RESPIRATORY_RATE" name="RESPIRATORY_RATE" placeholder="{{ trans('admin.patient-assessment.columns.RESPIRATORY_RATE') }}">
        <div v-if="errors.has('RESPIRATORY_RATE')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('RESPIRATORY_RATE') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('BLOOD_PRESSURE'), 'has-success': fields.BLOOD_PRESSURE && fields.BLOOD_PRESSURE.valid }">
    <label for="BLOOD_PRESSURE" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.BLOOD_PRESSURE') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.BLOOD_PRESSURE" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('BLOOD_PRESSURE'), 'form-control-success': fields.BLOOD_PRESSURE && fields.BLOOD_PRESSURE.valid}" id="BLOOD_PRESSURE" name="BLOOD_PRESSURE" placeholder="{{ trans('admin.patient-assessment.columns.BLOOD_PRESSURE') }}">
        <div v-if="errors.has('BLOOD_PRESSURE')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('BLOOD_PRESSURE') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEMPERATURE'), 'has-success': fields.TEMPERATURE && fields.TEMPERATURE.valid }">
    <label for="TEMPERATURE" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEMPERATURE') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEMPERATURE" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEMPERATURE'), 'form-control-success': fields.TEMPERATURE && fields.TEMPERATURE.valid}" id="TEMPERATURE" name="TEMPERATURE" placeholder="{{ trans('admin.patient-assessment.columns.TEMPERATURE') }}">
        <div v-if="errors.has('TEMPERATURE')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEMPERATURE') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('PULSE_RATE'), 'has-success': fields.PULSE_RATE && fields.PULSE_RATE.valid }">
    <label for="PULSE_RATE" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.PULSE_RATE') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.PULSE_RATE" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('PULSE_RATE'), 'form-control-success': fields.PULSE_RATE && fields.PULSE_RATE.valid}" id="PULSE_RATE" name="PULSE_RATE" placeholder="{{ trans('admin.patient-assessment.columns.PULSE_RATE') }}">
        <div v-if="errors.has('PULSE_RATE')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('PULSE_RATE') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('gender'), 'has-success': fields.gender && fields.gender.valid }">
    <label for="gender" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.gender') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.gender" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('gender'), 'form-control-success': fields.gender && fields.gender.valid}" id="gender" name="gender" placeholder="{{ trans('admin.patient-assessment.columns.gender') }}">
        <div v-if="errors.has('gender')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('gender') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('age'), 'has-success': fields.age && fields.age.valid }">
    <label for="age" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.age') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.age" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('age'), 'form-control-success': fields.age && fields.age.valid}" id="age" name="age" placeholder="{{ trans('admin.patient-assessment.columns.age') }}">
        <div v-if="errors.has('age')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('age') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('TEXT_HEMOPTYSIS'), 'has-success': fields.TEXT_HEMOPTYSIS && fields.TEXT_HEMOPTYSIS.valid }">
    <label for="TEXT_HEMOPTYSIS" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.patient-assessment.columns.TEXT_HEMOPTYSIS') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.TEXT_HEMOPTYSIS" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('TEXT_HEMOPTYSIS'), 'form-control-success': fields.TEXT_HEMOPTYSIS && fields.TEXT_HEMOPTYSIS.valid}" id="TEXT_HEMOPTYSIS" name="TEXT_HEMOPTYSIS" placeholder="{{ trans('admin.patient-assessment.columns.TEXT_HEMOPTYSIS') }}">
        <div v-if="errors.has('TEXT_HEMOPTYSIS')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TEXT_HEMOPTYSIS') }}</div>
    </div>
</div>


