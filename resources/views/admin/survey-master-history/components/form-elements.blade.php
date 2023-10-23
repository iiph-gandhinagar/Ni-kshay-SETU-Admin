<div class="form-group row align-items-center" :class="{'has-danger': errors.has('answer'), 'has-success': fields.answer && fields.answer.valid }">
    <label for="answer" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-history.columns.answer') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.answer" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('answer'), 'form-control-success': fields.answer && fields.answer.valid}" id="answer" name="answer" placeholder="{{ trans('admin.survey-master-history.columns.answer') }}">
        <div v-if="errors.has('answer')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('answer') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('survey_id'), 'has-success': fields.survey_id && fields.survey_id.valid }">
    <label for="survey_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-history.columns.survey_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.survey_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('survey_id'), 'form-control-success': fields.survey_id && fields.survey_id.valid}" id="survey_id" name="survey_id" placeholder="{{ trans('admin.survey-master-history.columns.survey_id') }}">
        <div v-if="errors.has('survey_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('survey_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('survey_question_id'), 'has-success': fields.survey_question_id && fields.survey_question_id.valid }">
    <label for="survey_question_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-history.columns.survey_question_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.survey_question_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('survey_question_id'), 'form-control-success': fields.survey_question_id && fields.survey_question_id.valid}" id="survey_question_id" name="survey_question_id" placeholder="{{ trans('admin.survey-master-history.columns.survey_question_id') }}">
        <div v-if="errors.has('survey_question_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('survey_question_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-history.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.survey-master-history.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>


