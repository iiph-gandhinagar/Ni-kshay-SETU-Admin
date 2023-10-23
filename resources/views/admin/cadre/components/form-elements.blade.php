<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
    <label for="cadre_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cadre.columns.cadre_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <!-- <input type="text" v-model="form.cadre_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('cadre_type'), 'form-control-success': fields.cadre_type && fields.cadre_type.valid}" id="cadre_type" name="cadre_type" placeholder="{{ trans('admin.cadre.columns.cadre_type') }}"> -->
        <select class="form-control" v-model="form.cadre_type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre_type'), 'form-control-success': fields.cadre_type && fields.cadre_type.valid}" id="cadre_type" name="cadre_type" placeholder="{{ trans('admin.cadre.columns.cadre_type') }}">
            <option value="">Select Options</option>                              
            <option value="State_Level">State Level</option>
            <option value="District_Level">District Level</option>
            <option value="Block_Level">Block Level</option>
            <option value="Health-facility_Level">Health Facility Level</option>
            <option value="National_Level">National Level</option>
            
        </select>
        <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cadre.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.cadre.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>
