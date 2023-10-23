<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber-activity.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.subscriber-activity.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('action'), 'has-success': fields.action && fields.action.valid }">
    <label for="action" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber-activity.columns.action') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.action" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('action'), 'form-control-success': fields.action && fields.action.valid}" id="action" name="action" placeholder="{{ trans('admin.subscriber-activity.columns.action') }}">
        <div v-if="errors.has('action')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('action') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ip_address'), 'has-success': fields.ip_address && fields.ip_address.valid }">
    <label for="ip_address" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber-activity.columns.ip_address') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ip_address" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ip_address'), 'form-control-success': fields.ip_address && fields.ip_address.valid}" id="ip_address" name="ip_address" placeholder="{{ trans('admin.subscriber-activity.columns.ip_address') }}">
        <div v-if="errors.has('ip_address')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ip_address') }}</div>
    </div>
</div>


