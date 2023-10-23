<div class="form-group row align-items-center" :class="{'has-danger': errors.has('assessment_id'), 'has-success': fields.assessment_id && fields.assessment_id.valid }">
    <label for="assessment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-enrollment.columns.assessment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.assessment_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.assessment-enrollment.columns.assessment_id') }}">
        <div v-if="errors.has('assessment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('assessment_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-enrollment.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.assessment-enrollment.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('response'), 'has-success': fields.response && fields.response.valid }">
    <label for="response" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-enrollment.columns.response') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.response" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('response'), 'form-control-success': fields.response && fields.response.valid}" id="response" name="response" placeholder="{{ trans('admin.assessment-enrollment.columns.response') }}">
        <div v-if="errors.has('response')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('response') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('send_inital_invitation'), 'has-success': fields.send_inital_invitation && fields.send_inital_invitation.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="send_inital_invitation" type="checkbox" v-model="form.send_inital_invitation" v-validate="''" data-vv-name="send_inital_invitation"  name="send_inital_invitation_fake_element">
        <label class="form-check-label" for="send_inital_invitation">
            {{ trans('admin.assessment-enrollment.columns.send_inital_invitation') }}
        </label>
        <input type="hidden" name="send_inital_invitation" :value="form.send_inital_invitation">
        <div v-if="errors.has('send_inital_invitation')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('send_inital_invitation') }}</div>
    </div>
</div>


