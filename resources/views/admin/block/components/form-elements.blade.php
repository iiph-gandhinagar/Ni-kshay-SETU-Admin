<div class="form-group row align-items-center" :class="{'has-danger': errors.has('country_id'), 'has-success': fields.country_id && fields.country_id.valid }">
    <label for="country_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.state.columns.country_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.country_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country_id'), 'form-control-success': fields.country_id && fields.country_id.valid}" id="country_id" name="country_id" placeholder="{{ trans('admin.subscriber.columns.country_id') }}"> --}}
        <multiselect v-model="form.country_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" 
            :options="{{ $country->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="false"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.block.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.state_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.block.columns.state_id') }}"> --}}
        <select class="form-control" v-model="form.state_id" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('state_id'), 'form-control-success': fields.state_id && fields.state_id.valid}" id="state_id" name="state_id" placeholder="{{ trans('admin.block.columns.state_id') }}">
            <option value="">Select State</option> 
            @foreach ($state as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select>
        <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
    <label for="district_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.block.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.district_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.block.columns.district_id') }}"> --}}
        <select class="form-control" v-model="form.district_id" v-validate="'required'" @input="validate($event)"  :class="{'form-control-danger': errors.has('district_id'), 'form-control-success': fields.district_id && fields.district_id.valid}" id="district_id" name="district_id" placeholder="{{ trans('admin.block.columns.district_id') }}">
            <option value="">Select district</option> 
            @foreach ($district as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select>
        <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.block.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.block.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>


