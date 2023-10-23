<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.chatbot-activity.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('action'), 'has-success': fields.action && fields.action.valid }">
    <label for="action" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.action') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.action" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('action'), 'form-control-success': fields.action && fields.action.valid}" id="action" name="action" placeholder="{{ trans('admin.chatbot-activity.columns.action') }}">
        <div v-if="errors.has('action')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('action') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('payload'), 'has-success': fields.payload && fields.payload.valid }">
    <label for="payload" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.payload') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.payload" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('payload'), 'form-control-success': fields.payload && fields.payload.valid}" id="payload" name="payload" placeholder="{{ trans('admin.chatbot-activity.columns.payload') }}">
        <div v-if="errors.has('payload')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('payload') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('plateform'), 'has-success': fields.plateform && fields.plateform.valid }">
    <label for="plateform" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.plateform') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.plateform" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('plateform'), 'form-control-success': fields.plateform && fields.plateform.valid}" id="plateform" name="plateform" placeholder="{{ trans('admin.chatbot-activity.columns.plateform') }}">
        <div v-if="errors.has('plateform')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('plateform') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ip_address'), 'has-success': fields.ip_address && fields.ip_address.valid }">
    <label for="ip_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.ip_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ip_address" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ip_address'), 'form-control-success': fields.ip_address && fields.ip_address.valid}" id="ip_address" name="ip_address" placeholder="{{ trans('admin.chatbot-activity.columns.ip_address') }}">
        <div v-if="errors.has('ip_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ip_address') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('tag_id'), 'has-success': fields.tag_id && fields.tag_id.valid }">
    <label for="tag_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.tag_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.tag_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('tag_id'), 'form-control-success': fields.tag_id && fields.tag_id.valid}" id="tag_id" name="tag_id" placeholder="{{ trans('admin.chatbot-activity.columns.tag_id') }}">
        <div v-if="errors.has('tag_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('tag_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('question_id'), 'has-success': fields.question_id && fields.question_id.valid }">
    <label for="question_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.question_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.question_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question_id'), 'form-control-success': fields.question_id && fields.question_id.valid}" id="question_id" name="question_id" placeholder="{{ trans('admin.chatbot-activity.columns.question_id') }}">
        <div v-if="errors.has('question_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('question_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('like'), 'has-success': fields.like && fields.like.valid }">
    <label for="like" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.like') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.like" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('like'), 'form-control-success': fields.like && fields.like.valid}" id="like" name="like" placeholder="{{ trans('admin.chatbot-activity.columns.like') }}">
        <div v-if="errors.has('like')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('like') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dislike'), 'has-success': fields.dislike && fields.dislike.valid }">
    <label for="dislike" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chatbot-activity.columns.dislike') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dislike" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dislike'), 'form-control-success': fields.dislike && fields.dislike.valid}" id="dislike" name="dislike" placeholder="{{ trans('admin.chatbot-activity.columns.dislike') }}">
        <div v-if="errors.has('dislike')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dislike') }}</div>
    </div>
</div>


