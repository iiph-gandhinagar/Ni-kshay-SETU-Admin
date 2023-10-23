<div class="form-group row align-items-center" :class="{'has-danger': errors.has('keyword_id'), 'has-success': fields.keyword_id && fields.keyword_id.valid }">
    <label for="keyword_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword-hit.columns.keyword_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.keyword_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('keyword_id'), 'form-control-success': fields.keyword_id && fields.keyword_id.valid}" id="keyword_id" name="keyword_id" placeholder="{{ trans('admin.chat-keyword-hit.columns.keyword_id') }}">
        <div v-if="errors.has('keyword_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('keyword_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subscriber_id'), 'has-success': fields.subscriber_id && fields.subscriber_id.valid }">
    <label for="subscriber_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword-hit.columns.subscriber_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subscriber_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subscriber_id'), 'form-control-success': fields.subscriber_id && fields.subscriber_id.valid}" id="subscriber_id" name="subscriber_id" placeholder="{{ trans('admin.chat-keyword-hit.columns.subscriber_id') }}">
        <div v-if="errors.has('subscriber_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subscriber_id') }}</div>
    </div>
</div>


