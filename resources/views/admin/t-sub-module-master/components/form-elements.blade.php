<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-sub-module-master.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.t-sub-module-master.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('module_id'), 'has-success': fields.module_id && fields.module_id.valid }">
    <label for="module_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-sub-module-master.columns.module_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.module_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :close-on-select="false" :options="{{ $modules->toJson() }}" :multiple="false" open-direction="auto"></multiselect>
        <div v-if="errors.has('module_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('module_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('existing_module_ref'), 'has-success': fields.existing_module_ref && fields.existing_module_ref.valid }">
    <label for="existing_module_ref" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-sub-module-master.columns.existing_module_ref') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.existing_module_ref" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('existing_module_ref'), 'form-control-success': fields.existing_module_ref && fields.existing_module_ref.valid}" id="existing_module_ref" name="existing_module_ref" placeholder="{{ trans('admin.t-sub-module-master.columns.existing_module_ref') }}">
        <div v-if="errors.has('existing_module_ref')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('existing_module_ref') }}</div>
    </div>
</div>


