<div class="form-group row align-items-center" :class="{'has-danger': errors.has('role_id'), 'has-success': fields.role_id && fields.role_id.valid }">
    <label for="role_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.role-has-permission.columns.role_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.role_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('role_id'), 'form-control-success': fields.role_id && fields.role_id.valid}" id="role_id" name="role_id" placeholder="{{ trans('admin.role-has-permission.columns.role_id') }}"> --}}
        <select class="form-control" v-model="form.role_id" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('role_id'), 'form-control-success': fields.role_id && fields.role_id.valid}" id="role_id" name="role_id" placeholder="{{ trans('admin.district.columns.role_id') }}">
            <option value="">Select Roles</option> 
            @foreach ($roles as $item)
                <option value="{{ $item->id }}" >{{ $item->name }}</option>  
            @endforeach    
      </select>
        <div v-if="errors.has('role_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('role_id') }}</div>
    </div>
</div>


<div class="form-group row align-items-center" :class="{'has-danger': errors.has('permission_id'), 'has-success': fields.permission_id && fields.permission_id.valid }">
    <label for="permission_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.role-has-permission.columns.permission_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.permission_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('permission_id'), 'form-control-success': fields.permission_id && fields.permission_id.valid}" id="permission_id" name="permission_id" placeholder="{{ trans('admin.role-has-permission.columns.permission_id') }}"> --}}
        <multiselect :searchable="true" 
        track-by="id"
        v-model="form.permission_id"
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_permissions"
        label="name"
        :close-on-select="false"
        {{-- :custom-label="opt => form.all_keywords.find(x => x.id == opt).title" --}}
        open-direction="auto" 
        :multiple="true">
    </multiselect>
        <div v-if="errors.has('permission_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('permission_id') }}</div>
    </div>
</div>




