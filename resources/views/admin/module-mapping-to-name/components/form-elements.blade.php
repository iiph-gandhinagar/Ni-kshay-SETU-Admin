<div class="form-group row align-items-center" :class="{'has-danger': errors.has('module_name'), 'has-success': fields.module_name && fields.module_name.valid }">
    <label for="module_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.module-mapping-to-name.columns.module_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.module_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('module_name'), 'form-control-success': fields.module_name && fields.module_name.valid}" id="module_name" name="module_name" placeholder="{{ trans('admin.module-mapping-to-name.columns.module_name') }}">
        <div v-if="errors.has('module_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('module_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mapping_name'), 'has-success': fields.mapping_name && fields.mapping_name.valid }">
    <label for="mapping_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.module-mapping-to-name.columns.mapping_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mapping_name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mapping_name'), 'form-control-success': fields.mapping_name && fields.mapping_name.valid}" id="mapping_name" name="mapping_name" placeholder="{{ trans('admin.module-mapping-to-name.columns.mapping_name') }}">
        <div v-if="errors.has('mapping_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mapping_name') }}</div>
    </div>
</div>


