<div class="form-group row align-items-center" :class="{'has-danger': errors.has('question_id'), 'has-success': fields.question_id && fields.question_id.valid }">
    <label for="question_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question-hit.columns.question_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.question_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question_id'), 'form-control-success': fields.question_id && fields.question_id.valid}" id="question_id" name="question_id" placeholder="{{ trans('admin.chat-question-hit.columns.question_id') }}">
        <div v-if="errors.has('question_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('question_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subscriber_id'), 'has-success': fields.subscriber_id && fields.subscriber_id.valid }">
    <label for="subscriber_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question-hit.columns.subscriber_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subscriber_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subscriber_id'), 'form-control-success': fields.subscriber_id && fields.subscriber_id.valid}" id="subscriber_id" name="subscriber_id" placeholder="{{ trans('admin.chat-question-hit.columns.subscriber_id') }}">
        <div v-if="errors.has('subscriber_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subscriber_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('session_token'), 'has-success': fields.session_token && fields.session_token.valid }">
    <label for="session_token" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question-hit.columns.session_token') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.session_token" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('session_token'), 'form-control-success': fields.session_token && fields.session_token.valid}" id="session_token" name="session_token" placeholder="{{ trans('admin.chat-question-hit.columns.session_token') }}">
        <div v-if="errors.has('session_token')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('session_token') }}</div>
    </div>
</div>


