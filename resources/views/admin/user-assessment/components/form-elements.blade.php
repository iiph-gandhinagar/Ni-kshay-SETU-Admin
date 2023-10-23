<div class="form-group row align-items-center" :class="{'has-danger': errors.has('assessment_id'), 'has-success': fields.assessment_id && fields.assessment_id.valid }">
    <label for="assessment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.assessment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.assessment_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.user-assessment.columns.assessment_id') }}">
        <div v-if="errors.has('assessment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('assessment_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.user-assessment.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('total_marks'), 'has-success': fields.total_marks && fields.total_marks.valid }">
    <label for="total_marks" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.total_marks') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.total_marks" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('total_marks'), 'form-control-success': fields.total_marks && fields.total_marks.valid}" id="total_marks" name="total_marks" placeholder="{{ trans('admin.user-assessment.columns.total_marks') }}">
        <div v-if="errors.has('total_marks')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('total_marks') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('obtained_marks'), 'has-success': fields.obtained_marks && fields.obtained_marks.valid }">
    <label for="obtained_marks" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.obtained_marks') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.obtained_marks" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('obtained_marks'), 'form-control-success': fields.obtained_marks && fields.obtained_marks.valid}" id="obtained_marks" name="obtained_marks" placeholder="{{ trans('admin.user-assessment.columns.obtained_marks') }}">
        <div v-if="errors.has('obtained_marks')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('obtained_marks') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('attempted'), 'has-success': fields.attempted && fields.attempted.valid }">
    <label for="attempted" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.attempted') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.attempted" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('attempted'), 'form-control-success': fields.attempted && fields.attempted.valid}" id="attempted" name="attempted" placeholder="{{ trans('admin.user-assessment.columns.attempted') }}">
        <div v-if="errors.has('attempted')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('attempted') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('right_answers'), 'has-success': fields.right_answers && fields.right_answers.valid }">
    <label for="right_answers" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.right_answers') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.right_answers" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('right_answers'), 'form-control-success': fields.right_answers && fields.right_answers.valid}" id="right_answers" name="right_answers" placeholder="{{ trans('admin.user-assessment.columns.right_answers') }}">
        <div v-if="errors.has('right_answers')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('right_answers') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('wrong_answers'), 'has-success': fields.wrong_answers && fields.wrong_answers.valid }">
    <label for="wrong_answers" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.wrong_answers') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.wrong_answers" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('wrong_answers'), 'form-control-success': fields.wrong_answers && fields.wrong_answers.valid}" id="wrong_answers" name="wrong_answers" placeholder="{{ trans('admin.user-assessment.columns.wrong_answers') }}">
        <div v-if="errors.has('wrong_answers')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('wrong_answers') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('skipped'), 'has-success': fields.skipped && fields.skipped.valid }">
    <label for="skipped" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-assessment.columns.skipped') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.skipped" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('skipped'), 'form-control-success': fields.skipped && fields.skipped.valid}" id="skipped" name="skipped" placeholder="{{ trans('admin.user-assessment.columns.skipped') }}">
        <div v-if="errors.has('skipped')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('skipped') }}</div>
    </div>
</div>


