<div class="form-group row align-items-center"
    :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.title') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required|max:65'" @input="validate($event)"
            class="form-control"
            :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}"
            id="title" name="title" placeholder="{{ trans('admin.user-notification.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{'has-danger': errors.has('description'), 'has-success': fields.description && fields.description.valid }">
    <label for="description" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.description') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            {{-- <wysiwyg v-model="form.description" v-validate="'required'" id="description" name="description" :config="mediaWysiwygConfig"></wysiwyg> --}}
            <textarea class="form-control" v-model="form.description" v-validate="'required|max:240'" id="description"
                name="description">
            </textarea>
        </div>
        <div v-if="errors.has('description')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('description') }}
        </div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{'has-danger': errors.has('type'), 'has-success': fields.type && fields.type.valid }">
    <label for="type" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.type') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <select class="form-control" v-model="form.type" v-validate="'required'" @input="validate($event)"
            :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}"
            id="type" name="type" placeholder="{{ trans('admin.cadre.columns.type') }}">
            <option value="">Select Options</option>
            <option value="public">Public</option>
            <option value="user-specific">User Specific</option>
            <option value="multiple-filters">Multiple Filters</option>

        </select>
        <div v-if="errors.has('type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type') }}</div>
    </div>
</div>

<div v-if="form.type == 'user-specific'" class="form-group row align-items-center"
    :class="{'has-danger': errors.has('user_id'), 'has-success': fields.user_id && fields.user_id.valid }">
    <label for="user_id" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.user_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
        <multiselect :searchable="true" track-by="id" v-model="form.user_id"
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
            :options="form.all_subscriber" label="name" :close-on-select="false" 
            :custom-label="nameWithPhoneNumber"
            {{-- :custom-label="opt => form.all_subscriber.find(x => x.id == opt).name" --}}
            open-direction="auto" :multiple="true">
            <template slot="selection" slot-scope="{ user_id, search, isOpen }"><span class="multiselect__single" v-if="form.user_id.length &amp;&amp; !isOpen">@{{ form.user_id.length }} options selected</span></template></multiselect>
            </multiselect>
        {{-- <search-select v-model="form.user_id"></search-select> --}}
        <div v-if="errors.has('user_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('user_id') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAll">
            <span v-if="form.all_subscriber.length == form.user_id.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>

@if ($user_state == '')
    <div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center" :class="{'has-danger': errors.has('country_id'), 'has-success': fields.country && fields.country.valid }">
        <label for="country_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.country_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect v-model="form.country_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $country->toJson() }}" :multiple="false" :close-on-select="false"
                :searchable="true" open-direction="auto">
                </multiselect>
            <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country_id') }}
            </div>
            <div class="form-control-feedback form-text" style="color:red" v-clock>Select if you want to send notification to National level Subscriber</div>
        </div>
    </div>
@endif

<div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center"
    :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.state_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
        <multiselect v-model="form.state_id"
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id"
            :options="{{ $state->toJson() }}" :multiple="true" :searchable="true" :close-on-select="false"
            @input="getDistrictOnChangeOfState" open-direction="auto">
            <template slot="selection" slot-scope="{ state_id, search, isOpen }"><span class="multiselect__single" v-if="form.state_id.length &amp;&amp; !isOpen">@{{ form.state_id.length }} options selected</span></template></multiselect>
            </multiselect>
        <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
        <div class="form-control-feedback form-text" style="color:red" v-clock>Select if you want to send notification to State level Subscriber</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAllStates">
            <span v-if="form.all_states.length == form.state_id.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>

<div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center"
    :class="{'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
    <label for="district_id" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.district_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
        <multiselect :searchable="true" v-model="form.district_id"
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
            :options="form.district_options" label="title" track-by="id" open-direction="auto"
            :close-on-select="false" :multiple="true">
            <template slot="selection" slot-scope="{ district_id, search, isOpen }"><span class="multiselect__single" v-if="form.district_id.length &amp;&amp; !isOpen">@{{ form.district_id.length }} options selected</span></template></multiselect></multiselect>
        <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}
        </div>
        <div class="form-control-feedback form-text" style="color:red" v-clock>Select if you want to send notification to District level Subscriber</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAllDistrict">
            <span v-if="form.filtered_district.length == form.district_id.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>

@if ($user_state == '')
    <div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center"
        :class="{'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
        <label for="cadre_type" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.cadre_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <select v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                v-validate="'required'" @input="validate($event)"
                :class="{'form-control-danger': errors.has('cadre_type'), 'form-control-success': fields.cadre_type && fields.cadre_type.valid}"
                id="cadre_type" name="cadre_type"
                placeholder="{{ trans('admin.user-notification.columns.cadre_type') }}">
                <option value="">Select Options</option>
                <option value="All">All</option>
                <option value="State_Level">State Level</option>
                <option value="District_Level">District Level</option>
                <option value="Block_Level">Block Level</option>
                <option value="Health-facility_Level">Health Facility Level</option>
                <option value="National_Level">National Level</option>
            </select>
            <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_type') }}
            </div>
        </div>
    </div>
@else
    <div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center"
        :class="{'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
        <label for="cadre_type" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.cadre_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <select v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                v-validate="'required'" @input="validate($event)"
                :class="{'form-control-danger': errors.has('cadre_type'), 'form-control-success': fields.cadre_type && fields.cadre_type.valid}"
                id="cadre_type" name="cadre_type"
                placeholder="{{ trans('admin.user-notification.columns.cadre_type') }}">
                <option value="">Select Options</option>
                <option value="All">All</option>
                <option value="State_Level">State Level</option>
                <option value="District_Level">District Level</option>
                <option value="Block_Level">Block Level</option>
                <option value="Health-facility_Level">Health Facility Level</option>
                <option value="National_Level">National Level</option>
            </select>
            <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_type') }}
            </div>
        </div>
    </div>
@endif

<div v-if="form.type == 'multiple-filters'" class="form-group row align-items-center"
    :class="{'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
    <label for="cadre_id" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.cadre_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
        <multiselect :searchable="true" v-model="form.cadre_id"
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
            label="title" track-by="id" open-direction="auto" :close-on-select="false" :multiple="true">
            <template slot="selection" slot-scope="{ cadre_id, search, isOpen }"><span class="multiselect__single" v-if="form.cadre_id.length &amp;&amp; !isOpen">@{{ form.cadre_id.length }} options selected</span></template></multiselect></multiselect>
            </multiselect>
        <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAllCadres">
            <span v-if="form.filtered_cadres.length == form.cadre_id.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>


<div class="form-check row" :class="{'has-danger': errors.has('is_deeplinking'), 'has-success': fields.is_deeplinking && fields.is_deeplinking.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_deeplinking" type="checkbox" v-model="form.is_deeplinking" v-validate="''" data-vv-name="is_deeplinking"  name="is_deeplinking_fake_element">
        <label class="form-check-label" for="is_deeplinking">
            {{ trans('admin.user-notification.columns.is_deeplinking') }}
        </label>
        <input type="hidden" name="is_deeplinking" :value="form.is_deeplinking">
        <div v-if="errors.has('is_deeplinking')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_deeplinking') }}</div>
    </div>
</div>

<div v-if="form.is_deeplinking == 1 && {{$user_role}} != 10" class="form-group row align-items-center" :class="{'has-danger': errors.has('automatic_notification_type'), 'has-success': fields.automatic_notification_type && fields.automatic_notification_type.valid }">
    <label for="automatic_notification_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.automatic_notification_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.automatic_notification_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('automatic_notification_type'), 'form-control-success': fields.automatic_notification_type && fields.automatic_notification_type.valid}" id="automatic_notification_type" name="automatic_notification_type" placeholder="{{ trans('admin.user-notification.columns.automatic_notification_type') }}"> --}}
        <select v-on:change="getTitleListing" class="form-control" v-model="form.automatic_notification_type" @input="validate($event)"
                :class="{'form-control-danger': errors.has('automatic_notification_type'), 'form-control-success': fields.automatic_notification_type && fields.automatic_notification_type.valid}"
                id="automatic_notification_type" name="automatic_notification_type"
                placeholder="{{ trans('admin.user-notification.columns.automatic_notification_type') }}">
                <option value="">Select Options</option>
                <option value="Miscellaneous">Miscellaneous</option>
                <option value="Assessment">Assessment</option>
                <option value="Resource Material">Resource Material</option>
                <option value="Case Definitions">Case Definitions</option>
                <option value="Diagnosis Algorithms">Diagnosis Algorithms</option>
                <option value="Treatment Algorithms">Treatment Algorithms</option>
                <option value="Guidance On Adverse Drug Reactions">Guidance On Adverse Drug Reactions</option>
                <option value="PMTPT">PMTPT</option>
                <option value="Differential Care Algorithms">Differential Care Algorithms</option>
                <option value="NTEP Interventions Algorithms">NTEP Interventions Algorithms</option>
                <option value="Dynamic Algorithm">Dynamic Algorithm</option>
            </select>
        <div v-if="errors.has('automatic_notification_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('automatic_notification_type') }}</div>
    </div>
</div>

<div v-else-if="form.is_deeplinking == 1 && {{$user_role}} == 10" class="form-group row align-items-center" :class="{'has-danger': errors.has('automatic_notification_type'), 'has-success': fields.automatic_notification_type && fields.automatic_notification_type.valid }">
    <label for="automatic_notification_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.automatic_notification_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <select v-on:change="getTitleListing" class="form-control" v-model="form.automatic_notification_type" @input="validate($event)"
                :class="{'form-control-danger': errors.has('automatic_notification_type'), 'form-control-success': fields.automatic_notification_type && fields.automatic_notification_type.valid}"
                id="automatic_notification_type" name="automatic_notification_type"
                placeholder="{{ trans('admin.user-notification.columns.automatic_notification_type') }}">
                <option value="">Select Options</option>
                <option value="Assessment">Assessment</option>
                <option value="Resource Material">Resource Material</option>
            </select>
        <div v-if="errors.has('automatic_notification_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('automatic_notification_type') }}</div>
    </div>
</div>

<div v-if="form.is_deeplinking == 1" class="form-group row align-items-center" :class="{'has-danger': errors.has('type_title'), 'has-success': fields.type_title && fields.type_title.valid }">
    <label for="type_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-notification.columns.type_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.type_title" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('type_title'), 'form-control-success': fields.type_title && fields.type_title.valid}" id="type_title" name="type_title" placeholder="{{ trans('admin.user-notification.columns.type_title') }}"> --}}
        <multiselect v-model="form.type_title"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}"
                track-by="id"  
                label="title"
                :options="form.list_title" 
                {{-- :custom-label="opt => form.list_title.find(x => x.id == opt).title" --}}
                :multiple="false" :close-on-select="true"
                :searchable="true" open-direction="auto">
                </multiselect>
        <div v-if="errors.has('type_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type_title') }}</div>
    </div>
</div>