{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('api_token'), 'has-success': fields.api_token && fields.api_token.valid }">
    <label for="api_token" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.api_token') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.api_token" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('api_token'), 'form-control-success': fields.api_token && fields.api_token.valid}" id="api_token" name="api_token" placeholder="{{ trans('admin.subscriber.columns.api_token') }}">
        <div v-if="errors.has('api_token')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('api_token') }}</div>
    </div>
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" disabled=true @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.subscriber.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('phone_no'), 'has-success': fields.phone_no && fields.phone_no.valid }">
    <label for="phone_no" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.phone_no') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.phone_no" disabled=true v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('phone_no'), 'form-control-success': fields.phone_no && fields.phone_no.valid}" id="phone_no" name="phone_no" placeholder="{{ trans('admin.subscriber.columns.phone_no') }}">
        <div v-if="errors.has('phone_no')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('phone_no') }}</div>
    </div>
</div>

{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
    <label for="password" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.password') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="password" v-model="form.password" v-validate="'min:7'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('password'), 'form-control-success': fields.password && fields.password.valid}" id="password" name="password" placeholder="{{ trans('admin.subscriber.columns.password') }}" ref="password">
        <div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('password_confirmation'), 'has-success': fields.password_confirmation && fields.password_confirmation.valid }">
    <label for="password_confirmation" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.password_repeat') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="password" v-model="form.password_confirmation" v-validate="'confirmed:password|min:7'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('password_confirmation'), 'form-control-success': fields.password_confirmation && fields.password_confirmation.valid}" id="password_confirmation" name="password_confirmation" placeholder="{{ trans('admin.subscriber.columns.password') }}" data-vv-as="password">
        <div v-if="errors.has('password_confirmation')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password_confirmation') }}</div>
    </div>
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
    <label for="cadre_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.cadre_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <select  v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre_type'), 'form-control-success': fields.cadre_type && fields.cadre_type.valid}" id="cadre_type" name="cadre_type" placeholder="{{ trans('admin.subscriber.columns.cadre_type') }}">
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
{{-- 
<div class="form-check row" :class="{'has-danger': errors.has('is_verified'), 'has-success': fields.is_verified && fields.is_verified.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_verified" type="checkbox" v-model="form.is_verified" v-validate="''" data-vv-name="is_verified"  name="is_verified_fake_element">
        <label class="form-check-label" for="is_verified">
            {{ trans('admin.subscriber.columns.is_verified') }}
        </label>
        <input type="hidden" name="is_verified" :value="form.is_verified">
        <div v-if="errors.has('is_verified')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_verified') }}</div>
    </div>
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
    <label for="cadre_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.cadre_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <multiselect 
                :searchable="true"
                v-model="form.cadre_id" 
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
                :options="form.options" 
                label="title" 
                track-by="id"
                :close-on-select="false"
                open-direction="auto" 
                :multiple="false"></multiselect>
            {{-- <select class="form-control" v-model="form.cadre_id" v-validate="'required'" :options="form.options" label="title" track-by="id" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre_id'), 'form-control-success': fields.cadre_id && fields.cadre_id.valid}" id="cadre_id" name="cadre_id" placeholder="{{ trans('admin.subscriber.columns.cadre_id') }}"></select> --}}
        <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}</div>
    </div>
</div>

<div v-if="form.cadre_type == 'National_Level'" class="form-group row align-items-center" :class="{'has-danger': errors.has('country_id'), 'has-success': fields.country_id && fields.country_id.valid }">
    <label for="country_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.country_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.country_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country_id'), 'form-control-success': fields.country_id && fields.country_id.valid}" id="country_id" name="country_id" placeholder="{{ trans('admin.subscriber.columns.country_id') }}"> --}}
        <multiselect v-model="form.country_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="{{ $country->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="false"
            @input="getDistrictOnChangeOfState"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country_id') }}</div>
    </div>
</div>

<div v-if="form.cadre_type == 'State_Level' || form.cadre_type == 'District_Level' || form.cadre_type == 'Block_Level' || form.cadre_type == 'Health-facility_Level'" class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.state_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.subscriber.columns.state_id') }}"> --}}
        <multiselect v-model="form.state_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="{{ $state->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="false"
            @input="getDistrictOnChangeOfState"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
    </div>
</div>

<div v-if="form.cadre_type == 'District_Level' || form.cadre_type == 'Block_Level' || form.cadre_type == 'Health-facility_Level'" class="form-group row align-items-center" :class="{'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
    <label for="district_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.district_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.subscriber.columns.district_id') }}"> --}}
        <multiselect 
            :searchable="true"
            v-model="form.district_id" 
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
            :options="form.district_options" 
            label="title" 
            track-by="id"
            open-direction="auto" 
            :close-on-select="false"
            @input="getBlockOnChangeOfDistrict"
            :multiple="false"></multiselect>
        <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}</div>
    </div>
</div>

<div v-if="form.cadre_type == 'Block_Level' || form.cadre_type == 'Health-facility_Level'" class="form-group row align-items-center" :class="{'has-danger': errors.has('block_id'), 'has-success': fields.block_id && fields.block_id.valid }">
    <label for="block_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.block_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.block_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('block_id'), 'form-control-success': fields.block_id && fields.block_id.valid}" id="block_id" name="block_id" placeholder="{{ trans('admin.subscriber.columns.block_id') }}"> --}}
        {{-- <select class="form-control" v-model="form.block_id"  v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('block_id'), 'form-control-success': fields.block_id && fields.block_id.valid}" id="block_id" name="block_id" placeholder="{{ trans('admin.subscriber.columns.block_id') }}">
            <option value="0">Select Options</option> 
            @foreach ($block as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}
      <multiselect 
            :searchable="true"
            v-model="form.block_id" 
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
            :options="form.block_options" 
            label="title" 
            track-by="id"
            open-direction="auto" 
            :close-on-select="false"
            @input="getHealthFacilityOnChangeOfBlock"
            :multiple="false"></multiselect>
        <div v-if="errors.has('block_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('block_id') }}</div>
    </div>
</div>

<div v-if="form.cadre_type == 'Health-facility_Level'" class="form-group row align-items-center" :class="{'has-danger': errors.has('health_facility_id'), 'has-success': fields.health_facility_id && fields.health_facility_id.valid }">
    <label for="health_facility_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.subscriber.columns.health_facility_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.health_facility_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('health_facility_id'), 'form-control-success': fields.health_facility_id && fields.health_facility_id.valid}" id="health_facility_id" name="health_facility_id" placeholder="{{ trans('admin.subscriber.columns.health_facility_id') }}"> --}}
        {{-- <select class="form-control" v-model="form.health_facility_id"  v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('health_facility_id'), 'form-control-success': fields.health_facility_id && fields.health_facility_id.valid}" id="health_facility_id" name="health_facility_id" placeholder="{{ trans('admin.subscriber.columns.health_facility_id') }}">
            <option value="0">Select Options</option> 
            @foreach ($health_facility as $item)
                <option value="{{ $item->id }}" >{{ $item->health_facility_code }}</option>  
            @endforeach    
      </select> --}}
      <multiselect 
            :searchable="true"
            v-model="form.health_facility_id" 
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
            :options="form.health_facility_options" 
            label="health_facility_code" 
            track-by="id"
            open-direction="auto"
            :close-on-select="false"
            :multiple="false"></multiselect>
        <div v-if="errors.has('health_facility_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('health_facility_id') }}</div>
    </div>
</div>


