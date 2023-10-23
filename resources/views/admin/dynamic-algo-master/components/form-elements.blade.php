<div class="form-group row align-items-center" :class="{'has-danger': errors.has('name'), 'has-success': fields.name && fields.name.valid }">
    <label for="name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algo-master.columns.name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.name" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('name'), 'form-control-success': fields.name && fields.name.valid}" id="name" name="name" placeholder="{{ trans('admin.dynamic-algo-master.columns.name') }}">
        <div v-if="errors.has('name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('section'), 'has-success': fields.section && fields.section.valid }">
    <label for="section" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algo-master.columns.section') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.section" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('section'), 'form-control-success': fields.section && fields.section.valid}" id="section" name="section" placeholder="{{ trans('admin.dynamic-algo-master.columns.section') }}"> --}}
        <select class="form-control" v-model="form.section" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('section'), 'form-control-success': fields.section && fields.section.valid}" id="section" name="section" placeholder="{{ trans('admin.dynamic-algo-master.columns.section') }}">
            <option value="">Select Section</option>
            <option value="Learn About Case Findings">Learn About Case Findings</option>
            <option value="Patient Management Tool">Patient Management Tool</option>
      </select>
        <div v-if="errors.has('section')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('section') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" >
    <label for="node_icon" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algo-master.columns.node_icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('node_icon') }}</div>
        @if(isset($dynamicAlgoMaster))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\DynamicAlgoMaster::class)->getMediaCollection('node_icon'),
                'media' => $dynamicAlgoMaster->getThumbs200ForCollection('node_icon'),
                'Label' => "node_icon"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\DynamicAlgoMaster::class)->getMediaCollection('node_icon'),
                'Label' => "node_icon"
            ])
        @endif
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.dynamic-algo-master.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>


