{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.state_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.health-facility.columns.state_id') }}">
        <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
    <label for="district_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.district_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.health-facility.columns.district_id') }}">
        <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}</div>
    </div>
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country_id'), 'has-success': fields.country_id && fields.country_id.valid }">
    <label for="country_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.state.columns.country_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.country_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country_id'), 'form-control-success': fields.country_id && fields.country_id.valid}" id="country_id" name="country_id" placeholder="{{ trans('admin.subscriber.columns.country_id') }}"> --}}
        <multiselect v-model="form.country_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="{{ $country->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.state_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.health-facility.columns.state_id') }}"> --}}
        {{-- <select class="form-control" v-model="form.state_id" v-validate="'required'" @input="validate($event);getStateDistrict();" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.health-facility.columns.state_id') }}">
            <option value="">Select State</option> 
            @foreach ($state as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}

        <multiselect v-model="form.state_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="{{ $state }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto" @input="getClearData();"></multiselect>
        <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
    <label for="district_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.district_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.health-facility.columns.district_id') }}"> --}}
        {{-- <select class="form-control" v-model="form.district_id" v-validate="'required'" @input="validate($event)"  :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.health-facility.columns.district_id') }}">
            <option value="">Select district</option> 
            @foreach ($district as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}
        <multiselect v-model="form.district_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="form.filter_districts" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto" @input="getClearBlock();"></multiselect>
        <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('block_id'), 'has-success': fields.block_id && fields.block_id.valid }">
    <label for="block_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.block_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.block_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('block_id'), 'form-control-success': fields.block_id && fields.block_id.valid}" id="block_id" name="block_id" placeholder="{{ trans('admin.health-facility.columns.block_id') }}"> --}}
        {{-- <select class="form-control" v-model="form.block_id" v-validate="'required|integer'" @input="validate($event)"  :class="{'form-control-danger': errors.has('block_id'), 'form-control-success': fields.block_id && fields.block_id.valid}" id="block_id" name="block_id" placeholder="{{ trans('admin.health-facility.columns.block_id') }}">
            <option value="">Select Block</option> 
            @foreach ($taluka as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}
        <multiselect v-model="form.block_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="form.filter_block" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('block_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('block_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('health_facility_code'), 'has-success': fields.health_facility_code && fields.health_facility_code.valid }">
    <label for="health_facility_code" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.health_facility_code') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.health_facility_code" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('health_facility_code'), 'form-control-success': fields.health_facility_code && fields.health_facility_code.valid}" id="health_facility_code" name="health_facility_code" placeholder="{{ trans('admin.health-facility.columns.health_facility_code') }}">
        <div v-if="errors.has('health_facility_code')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('health_facility_code') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('longitude'), 'has-success': fields.longitude && fields.longitude.valid }">
    <label for="longitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.longitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.longitude" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('longitude'), 'form-control-success': fields.longitude && fields.longitude.valid}" id="longitude" name="longitude" placeholder="{{ trans('admin.health-facility.columns.longitude') }}">
        <div v-if="errors.has('longitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('longitude') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('latitude'), 'has-success': fields.latitude && fields.latitude.valid }">
    <label for="latitude" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.health-facility.columns.latitude') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.latitude" v-validate="'required|decimal'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('latitude'), 'form-control-success': fields.latitude && fields.latitude.valid}" id="latitude" name="latitude" placeholder="{{ trans('admin.health-facility.columns.latitude') }}">
        <div v-if="errors.has('latitude')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('latitude') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('ANC_Clinic'), 'has-success': fields.ANC_Clinic && fields.ANC_Clinic.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="ANC_Clinic" type="checkbox" v-model="form.ANC_Clinic" v-validate="''" data-vv-name="ANC_Clinic"  name="ANC_Clinic_fake_element">
        <label class="form-check-label" for="ANC_Clinic">
            {{ trans('admin.health-facility.columns.ANC_Clinic') }}
        </label>
        <input type="hidden" name="ANC_Clinic" :value="form.ANC_Clinic">
        <div v-if="errors.has('ANC_Clinic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ANC_Clinic') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('Pediatric_Care_Facility'), 'has-success': fields.Pediatric_Care_Facility && fields.Pediatric_Care_Facility.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="Pediatric_Care_Facility" type="checkbox" v-model="form.Pediatric_Care_Facility" v-validate="''" data-vv-name="Pediatric_Care_Facility"  name="Pediatric_Care_Facility_fake_element">
        <label class="form-check-label" for="Pediatric_Care_Facility">
            {{ trans('admin.health-facility.columns.Pediatric_Care_Facility') }}
        </label>
        <input type="hidden" name="Pediatric_Care_Facility" :value="form.Pediatric_Care_Facility">
        <div v-if="errors.has('Pediatric_Care_Facility')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('Pediatric_Care_Facility') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('IRL'), 'has-success': fields.IRL && fields.IRL.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="IRL" type="checkbox" v-model="form.IRL" v-validate="''" data-vv-name="IRL"  name="IRL_fake_element">
        <label class="form-check-label" for="IRL">
            {{ trans('admin.health-facility.columns.IRL') }}
        </label>
        <input type="hidden" name="IRL" :value="form.IRL">
        <div v-if="errors.has('IRL')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('IRL') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('NODAL_DRTB_CENTER'), 'has-success': fields.NODAL_DRTB_CENTER && fields.NODAL_DRTB_CENTER.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="NODAL_DRTB_CENTER" type="checkbox" v-model="form.NODAL_DRTB_CENTER" v-validate="''" data-vv-name="NODAL_DRTB_CENTER"  name="NODAL_DRTB_CENTER_fake_element">
        <label class="form-check-label" for="NODAL_DRTB_CENTER">
            {{ trans('admin.health-facility.columns.NODAL_DRTB_CENTER') }}
        </label>
        <input type="hidden" name="NODAL_DRTB_CENTER" :value="form.NODAL_DRTB_CENTER">
        <div v-if="errors.has('NODAL_DRTB_CENTER')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('NODAL_DRTB_CENTER') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('District_DRTB_Centre'), 'has-success': fields.District_DRTB_Centre && fields.District_DRTB_Centre.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="District_DRTB_Centre" type="checkbox" v-model="form.District_DRTB_Centre" v-validate="''" data-vv-name="District_DRTB_Centre"  name="District_DRTB_Centre_fake_element">
        <label class="form-check-label" for="District_DRTB_Centre">
            {{ trans('admin.health-facility.columns.District_DRTB_Centre') }}
        </label>
        <input type="hidden" name="District_DRTB_Centre" :value="form.District_DRTB_Centre">
        <div v-if="errors.has('District_DRTB_Centre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('District_DRTB_Centre') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('ART_Centre'), 'has-success': fields.ART_Centre && fields.ART_Centre.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="ART_Centre" type="checkbox" v-model="form.ART_Centre" v-validate="''" data-vv-name="ART_Centre"  name="ART_Centre_fake_element">
        <label class="form-check-label" for="ART_Centre">
            {{ trans('admin.health-facility.columns.ART_Centre') }}
        </label>
        <input type="hidden" name="ART_Centre" :value="form.ART_Centre">
        <div v-if="errors.has('ART_Centre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ART_Centre') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('De_addiction_centres'), 'has-success': fields.De_addiction_centres && fields.De_addiction_centres.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="De_addiction_centres" type="checkbox" v-model="form.De_addiction_centres" v-validate="''" data-vv-name="De_addiction_centres"  name="De_addiction_centres_fake_element">
        <label class="form-check-label" for="De_addiction_centres">
            {{ trans('admin.health-facility.columns.De_addiction_centres') }}
        </label>
        <input type="hidden" name="De_addiction_centres" :value="form.De_addiction_centres">
        <div v-if="errors.has('De_addiction_centres')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('De_addiction_centres') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('Nutritional_Rehabilitation_centre'), 'has-success': fields.Nutritional_Rehabilitation_centre && fields.Nutritional_Rehabilitation_centre.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="Nutritional_Rehabilitation_centre" type="checkbox" v-model="form.Nutritional_Rehabilitation_centre" v-validate="''" data-vv-name="Nutritional_Rehabilitation_centre"  name="Nutritional_Rehabilitation_centre_fake_element">
        <label class="form-check-label" for="Nutritional_Rehabilitation_centre">
            {{ trans('admin.health-facility.columns.Nutritional_Rehabilitation_centre') }}
        </label>
        <input type="hidden" name="Nutritional_Rehabilitation_centre" :value="form.Nutritional_Rehabilitation_centre">
        <div v-if="errors.has('Nutritional_Rehabilitation_centre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('Nutritional_Rehabilitation_centre') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('Tobacco_Cessation_clinic'), 'has-success': fields.Tobacco_Cessation_clinic && fields.Tobacco_Cessation_clinic.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="Tobacco_Cessation_clinic" type="checkbox" v-model="form.Tobacco_Cessation_clinic" v-validate="''" data-vv-name="Tobacco_Cessation_clinic"  name="Tobacco_Cessation_clinic_fake_element">
        <label class="form-check-label" for="Tobacco_Cessation_clinic">
            {{ trans('admin.health-facility.columns.Tobacco_Cessation_clinic') }}
        </label>
        <input type="hidden" name="Tobacco_Cessation_clinic" :value="form.Tobacco_Cessation_clinic">
        <div v-if="errors.has('Tobacco_Cessation_clinic')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('Tobacco_Cessation_clinic') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('CONFIRMATION_CENTER'), 'has-success': fields.CONFIRMATION_CENTER && fields.CONFIRMATION_CENTER.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="CONFIRMATION_CENTER" type="checkbox" v-model="form.CONFIRMATION_CENTER" v-validate="''" data-vv-name="CONFIRMATION_CENTER"  name="CONFIRMATION_CENTER_fake_element">
        <label class="form-check-label" for="CONFIRMATION_CENTER">
            {{ trans('admin.health-facility.columns.CONFIRMATION_CENTER') }}
        </label>
        <input type="hidden" name="CONFIRMATION_CENTER" :value="form.CONFIRMATION_CENTER">
        <div v-if="errors.has('CONFIRMATION_CENTER')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CONFIRMATION_CENTER') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('LPA_Lab'), 'has-success': fields.LPA_Lab && fields.LPA_Lab.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="LPA_Lab" type="checkbox" v-model="form.LPA_Lab" v-validate="''" data-vv-name="LPA_Lab"  name="LPA_Lab_fake_element">
        <label class="form-check-label" for="LPA_Lab">
            {{ trans('admin.health-facility.columns.LPA_Lab') }}
        </label>
        <input type="hidden" name="LPA_Lab" :value="form.LPA_Lab">
        <div v-if="errors.has('LPA_Lab')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('LPA_Lab') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('ICTC'), 'has-success': fields.ICTC && fields.ICTC.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="ICTC" type="checkbox" v-model="form.ICTC" v-validate="''" data-vv-name="ICTC"  name="ICTC_fake_element">
        <label class="form-check-label" for="ICTC">
            {{ trans('admin.health-facility.columns.ICTC') }}
        </label>
        <input type="hidden" name="ICTC" :value="form.ICTC">
        <div v-if="errors.has('ICTC')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ICTC') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('X_RAY'), 'has-success': fields.X_RAY && fields.X_RAY.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="X_RAY" type="checkbox" v-model="form.X_RAY" v-validate="''" data-vv-name="X_RAY"  name="X_RAY_fake_element">
        <label class="form-check-label" for="X_RAY">
            {{ trans('admin.health-facility.columns.X_RAY') }}
        </label>
        <input type="hidden" name="X_RAY" :value="form.X_RAY">
        <div v-if="errors.has('X_RAY')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('X_RAY') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('CBNAAT'), 'has-success': fields.CBNAAT && fields.CBNAAT.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="CBNAAT" type="checkbox" v-model="form.CBNAAT" v-validate="''" data-vv-name="CBNAAT"  name="CBNAAT_fake_element">
        <label class="form-check-label" for="CBNAAT">
            {{ trans('admin.health-facility.columns.CBNAAT') }}
        </label>
        <input type="hidden" name="CBNAAT" :value="form.CBNAAT">
        <div v-if="errors.has('CBNAAT')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('CBNAAT') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('TRUNAT'), 'has-success': fields.TRUNAT && fields.TRUNAT.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="TRUNAT" type="checkbox" v-model="form.TRUNAT" v-validate="''" data-vv-name="TRUNAT"  name="TRUNAT_fake_element">
        <label class="form-check-label" for="TRUNAT">
            {{ trans('admin.health-facility.columns.TRUNAT') }}
        </label>
        <input type="hidden" name="TRUNAT" :value="form.TRUNAT">
        <div v-if="errors.has('TRUNAT')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('TRUNAT') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('DMC'), 'has-success': fields.DMC && fields.DMC.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="DMC" type="checkbox" v-model="form.DMC" v-validate="''" data-vv-name="DMC"  name="DMC_fake_element">
        <label class="form-check-label" for="DMC">
            {{ trans('admin.health-facility.columns.DMC') }}
        </label>
        <input type="hidden" name="DMC" :value="form.DMC">
        <div v-if="errors.has('DMC')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('DMC') }}</div>
    </div>
</div>



