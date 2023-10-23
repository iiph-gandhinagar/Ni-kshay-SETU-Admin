<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-app-version.columns.user_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_id'), 'form-control-success': fields.user_id && fields.user_id.valid}" id="user_id" name="user_id" placeholder="{{ trans('admin.user-app-version.columns.user_id') }}">
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('user_name'), 'has-success': fields.user_name && fields.user_name.valid }">
    <label for="user_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-app-version.columns.user_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.user_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('user_name'), 'form-control-success': fields.user_name && fields.user_name.valid}" id="user_name" name="user_name" placeholder="{{ trans('admin.user-app-version.columns.user_name') }}">
        <div v-if="errors.has('user_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('app_version'), 'has-success': fields.app_version && fields.app_version.valid }">
    <label for="app_version" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-app-version.columns.app_version') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.app_version" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('app_version'), 'form-control-success': fields.app_version && fields.app_version.valid}" id="app_version" name="app_version" placeholder="{{ trans('admin.user-app-version.columns.app_version') }}">
        <div v-if="errors.has('app_version')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('app_version') }}</div>
    </div>
</div>


