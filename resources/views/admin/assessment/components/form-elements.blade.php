<div class="row form-inline" style="padding-bottom: 10px;" v-cloak>
    <div
        :class="{
            'col-xl-10 col-md-11 text-right': !
                isFormLocalized,
            'col text-center': isFormLocalized,
            'hidden': onSmallScreen
        }">
        <small>{{ trans('brackets/admin-ui::admin.forms.currently_editing_translation') }}<span
                v-if="!isFormLocalized && otherLocales.length > 1">
                {{ trans('brackets/admin-ui::admin.forms.more_can_be_managed') }}</span><span v-if="!isFormLocalized">
                | <a href="#"
                    @click.prevent="showLocalization">{{ trans('brackets/admin-ui::admin.forms.manage_translations') }}</a></span></small>
        <i class="localization-error" v-if="!isFormLocalized && showLocalizedValidationError"></i>
    </div>

    <div class="col text-center"
        :class="{ 'language-mobile': onSmallScreen, 'has-error': !isFormLocalized && showLocalizedValidationError }"
        v-if="isFormLocalized || onSmallScreen" v-cloak>
        <small>{{ trans('brackets/admin-ui::admin.forms.choose_translation_to_edit') }}
            <select class="form-control" v-model="currentLocale">
                <option :value="defaultLocale" v-if="onSmallScreen">@{{ defaultLocale.toUpperCase() }}</option>
                <option v-for="locale in otherLocales" :value="locale">@{{ locale.toUpperCase() }}</option>
            </select>
            <i class="localization-error" v-if="isFormLocalized && showLocalizedValidationError"></i>
            <span>|</span>
            <a href="#" @click.prevent="hideLocalization">{{ trans('brackets/admin-ui::admin.forms.hide') }}</a>
        </small>
    </div>
</div>
<div v-if="data.activated == 1">
    <div class="row">
        @foreach ($locales as $locale)
            <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
                <div class="form-group row align-items-center"
                    :class="{
                        'has-danger': errors.has('assessment_title{{ $locale }}'),
                        'has-success': fields
                            .assessment_title{{ $locale }} && fields.assessment_title{{ $locale }}.valid
                    }">
                    <label for="assessment_title{{ $locale }}"
                        class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment.columns.assessment_title') }}</label>
                    <div class="col-md-9" :class="{ 'col-xl-8': !isFormLocalized }">
                        <input type="text" disabled v-model="form.assessment_title.{{ $locale }}"
                            v-validate="''" @input="validate($event)" class="form-control"
                            :class="{
                                'form-control-danger': errors.has(
                                    'assessment_title{{ $locale }}'),
                                'form-control-success': fields
                                    .assessment_title{{ $locale }} && fields.assessment_title{{ $locale }}
                                    .valid
                            }"
                            id="assessment_title{{ $locale }}" name="assessment_title{{ $locale }}"
                            placeholder="{{ trans('admin.assessment.columns.assessment_title') }}">
                        <div v-if="errors.has('assessment_title{{ $locale }}')"
                            class="form-control-feedback form-text" v-cloak>
                            {{ '{{' }}errors.first('assessment_title{{ $locale }}') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group row align-items-center"
        :class="{
            'has-danger': errors.has('time_to_complete'),
            'has-success': fields.time_to_complete && fields.time_to_complete
                .valid
        }">
        <label for="time_to_complete" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.time_to_complete') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <input type="text" disabled v-model="form.time_to_complete" v-validate="'required|integer'"
                @input="validate($event)" class="form-control"
                :class="{
                    'form-control-danger': errors.has('time_to_complete'),
                    'form-control-success': fields.time_to_complete &&
                        fields.time_to_complete.valid
                }"
                id="time_to_complete" name="time_to_complete"
                placeholder="{{ trans('admin.assessment.columns.time_to_complete') }}">
            <div v-if="errors.has('time_to_complete')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('time_to_complete') }}</div>
        </div>
    </div>
    @if ($user_state == '')
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('country_id'), 'has-success': fields.country && fields.country.valid }">
            <label for="country_id" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.country_id') }}</label>
            <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
                <multiselect disabled v-model="form.country_id"
                    placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                    track-by="id" :options="{{ $country->toJson() }}" :multiple="false"
                    :close-on-select="false" :searchable="true" open-direction="auto">
                </multiselect>
                <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('country_id') }}
                </div>
            </div>
        </div>
    @endif

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
        <label for="state_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect disabled v-model="form.state_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $state->toJson() }}" :multiple="true" :close-on-select="false"
                :searchable="true" @input="getDistrictOnChangeOfState" open-direction="auto">
                <template slot="selection" slot-scope="{ state_id, search, isOpen }"><span class="multiselect__single"
                        v-if="form.state_id.length &amp;&amp; !isOpen">@{{ form.state_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button disabled type="button" class="btn btn-primary" v-on:click="selectAllStates">
                <span v-if="form.all_states.length == form.state_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
        <label for="district_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect disabled :searchable="true" v-model="form.district_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
                :options="form.district_options" label="title" track-by="id" open-direction="auto"
                :close-on-select="false" :multiple="true">
                <template slot="selection" slot-scope="{ district_id, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.district_id.length &amp;&amp; !isOpen">@{{ form.district_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button disabled type="button" class="btn btn-primary" v-on:click="selectAllDistrict">
                <span v-if="form.filtered_district.length == form.district_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    @if ($user_state == '')
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
            <label for="cadre_type" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select disabled v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                    v-validate="'required'" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.assessment.columns.cadre_type') }}">
                    <option value="">Select Options</option>
                    <option value="All">All</option>
                    <option value="State_Level">State Level</option>
                    <option value="District_Level">District Level</option>
                    <option value="Block_Level">Block Level</option>
                    <option value="Health-facility_Level">Health Facility Level</option>
                    <option value="National_Level">National Level</option>
                </select>
                <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('cadre_type') }}
                </div>
            </div>
        </div>
    @else
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
            <label for="cadre_type" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select disabled v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                    v-validate="'required'" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.assessment.columns.cadre_type') }}">
                    <option value="">Select Options</option>
                    <option value="All">All</option>
                    <option value="State_Level">State Level</option>
                    <option value="District_Level">District Level</option>
                    <option value="Block_Level">Block Level</option>
                    <option value="Health-facility_Level">Health Facility Level</option>
                </select>
                <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('cadre_type') }}
                </div>
            </div>
        </div>
    @endif

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
        <label for="cadre_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect disabled :searchable="true" v-model="form.cadre_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
                label="title" track-by="id" open-direction="auto" :multiple="true"
                :close-on-select="false">
                <template slot="selection" slot-scope="{ cadre_id, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.cadre_id.length &amp;&amp; !isOpen">@{{ form.cadre_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}
            </div>
            {{-- {{json_encode(array_keys(trans('admin.appConfig')), TRUE)}} --}}
            {{-- {{json_encode(array_values(trans('admin.appConfig')), TRUE)}} --}}
            {{-- label="title" --}}
            {{-- :value="form.cadre_id.i" --}}
            {{-- track-by="id" --}}
            {{-- :options="{{ json_encode($cadre, TRUE) }}" --}}
            {{-- :options='form.all_cadres' --}}
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button disabled type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{
            'has-danger': errors.has('assessment_type'),
            'has-success': fields.assessment_type && fields.assessment_type
                .valid
        }">
        <label for="assessment_type" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.assessment_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.assessment_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_type'), 'form-control-success': fields.assessment_type && fields.assessment_type.valid}" id="assessment_type" name="assessment_type" placeholder="{{ trans('admin.assessment.columns.assessment_type') }}"> --}}
            <select disabled class="form-control" v-model="form.assessment_type" v-validate="'required'"
                @input="validate($event)"
                :class="{
                    'form-control-danger': errors.has('assessment_type'),
                    'form-control-success': fields.assessment_type &&
                        fields.assessment_type.valid
                }"
                id="assessment_type" name="assessment_type"
                placeholder="{{ trans('admin.assessment.columns.assessment_type') }}">
                <option value="">Select Options</option>
                <option value="planned">Planned</option>
                <option value="real_time">Real Time</option>
            </select>
            <div v-if="errors.has('assessment_type')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('assessment_type') }}</div>
        </div>
    </div>

    <div v-if="form.assessment_type == 'planned'" class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('from_date'), 'has-success': fields.from_date && fields.from_date.valid }">
        <label for="from_date" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.from_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.from_date" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('from_date'), 'form-control-success': fields.from_date && fields.from_date.valid}" id="from_date" name="from_date" placeholder="{{ trans('admin.assessment.columns.from_date') }}"> --}}
            <datetime disabled v-model="form.from_date" :config="datetimePickerConfig" class="flatpickr"
                placeholder="Select date "></datetime>
            <div v-if="errors.has('from_date')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('from_date') }}
            </div>
        </div>
    </div>

    <div v-if="form.assessment_type == 'planned'" class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('to_date'), 'has-success': fields.to_date && fields.to_date.valid }">
        <label for="to_date" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.to_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.to_date" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('to_date'), 'form-control-success': fields.to_date && fields.to_date.valid}" id="to_date" name="to_date" placeholder="{{ trans('admin.assessment.columns.to_date') }}"> --}}
            {{-- <datetime
            type="datetime"
            v-model="datetimeEmpty"
            input-class="my-class"
            value-zone="America/New_York"
            zone="Asia/Shanghai"
            :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }"
            :phrases="{ok: 'Continue', cancel: 'Exit'}"
            :hour-step="2"
            :minute-step="15"
            :min-datetime="minDatetime"
            :max-datetime="maxDatetime"
            :week-start="7"
            use12-hour
            auto
        ></datetime> --}}
            <datetime disabled v-model="form.to_date" :config="datetimePickerConfig" class="flatpickr"
                placeholder="Select date "></datetime>
            <div v-if="errors.has('to_date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('to_date') }}
            </div>
        </div>
    </div>
</div>
<div v-else>
    <div class="row">
        @foreach ($locales as $locale)
            <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
                <div class="form-group row align-items-center"
                    :class="{
                        'has-danger': errors.has('assessment_title{{ $locale }}'),
                        'has-success': fields
                            .assessment_title{{ $locale }} && fields.assessment_title{{ $locale }}.valid
                    }">
                    <label for="assessment_title{{ $locale }}"
                        class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment.columns.assessment_title') }}</label>
                    <div class="col-md-9" :class="{ 'col-xl-8': !isFormLocalized }">
                        <input type="text" v-model="form.assessment_title.{{ $locale }}" v-validate="''"
                            @input="validate($event)" class="form-control"
                            :class="{
                                'form-control-danger': errors.has(
                                    'assessment_title{{ $locale }}'),
                                'form-control-success': fields
                                    .assessment_title{{ $locale }} && fields.assessment_title{{ $locale }}
                                    .valid
                            }"
                            id="assessment_title{{ $locale }}" name="assessment_title{{ $locale }}"
                            placeholder="{{ trans('admin.assessment.columns.assessment_title') }}">
                        <div v-if="errors.has('assessment_title{{ $locale }}')"
                            class="form-control-feedback form-text" v-cloak>{{ '{{' }}
                            errors.first('assessment_title{{ $locale }}') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="form-group row align-items-center"
        :class="{
            'has-danger': errors.has('time_to_complete'),
            'has-success': fields.time_to_complete && fields.time_to_complete
                .valid
        }">
        <label for="time_to_complete" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.time_to_complete') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <input type="text" v-model="form.time_to_complete" v-validate="'required|integer'"
                @input="validate($event)" class="form-control"
                :class="{
                    'form-control-danger': errors.has('time_to_complete'),
                    'form-control-success': fields.time_to_complete &&
                        fields.time_to_complete.valid
                }"
                id="time_to_complete" name="time_to_complete"
                placeholder="{{ trans('admin.assessment.columns.time_to_complete') }}">
            <div v-if="errors.has('time_to_complete')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('time_to_complete') }}</div>
        </div>
    </div>

    @if ($user_state == '')
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('country_id'), 'has-success': fields.country && fields.country.valid }">
            <label for="country_id" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.country_id') }}</label>
            <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
                <multiselect v-model="form.country_id"
                    placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                    track-by="id" :options="{{ $country->toJson() }}" :multiple="false"
                    :close-on-select="false" :searchable="true" open-direction="auto">
                </multiselect>
                <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('country_id') }}
                </div>
            </div>
        </div>
    @endif

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
        <label for="state_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect v-model="form.state_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $state->toJson() }}" :multiple="true" :close-on-select="false"
                :searchable="true" @input="getDistrictOnChangeOfState" open-direction="auto">
                <template slot="selection" slot-scope="{ state_id, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.state_id.length &amp;&amp; !isOpen">@{{ form.state_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAllStates">
                <span v-if="form.all_states.length == form.state_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
        <label for="district_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.district_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect :searchable="true" v-model="form.district_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
                :options="form.district_options" label="title" track-by="id" open-direction="auto"
                :close-on-select="false" :multiple="true">
                <template slot="selection" slot-scope="{ district_id, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.district_id.length &amp;&amp; !isOpen">@{{ form.district_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('district_id') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAllDistrict">
                <span v-if="form.filtered_district.length == form.district_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    @if ($user_state == '')
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
            <label for="cadre_type" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                    v-validate="'required'" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.assessment.columns.cadre_type') }}">
                    <option value="">Select Options</option>
                    <option value="All">All</option>
                    <option value="State_Level">State Level</option>
                    <option value="District_Level">District Level</option>
                    <option value="Block_Level">Block Level</option>
                    <option value="Health-facility_Level">Health Facility Level</option>
                    <option value="National_Level">National Level</option>
                </select>
                <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('cadre_type') }}
                </div>
            </div>
        </div>
    @else
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('cadre_type'), 'has-success': fields.cadre_type && fields.cadre_type.valid }">
            <label for="cadre_type" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                    v-validate="'required'" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.assessment.columns.cadre_type') }}">
                    <option value="">Select Options</option>
                    <option value="All">All</option>
                    <option value="State_Level">State Level</option>
                    <option value="District_Level">District Level</option>
                    <option value="Block_Level">Block Level</option>
                    <option value="Health-facility_Level">Health Facility Level</option>
                </select>
                <div v-if="errors.has('cadre_type')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('cadre_type') }}
                </div>
            </div>
        </div>
    @endif

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
        <label for="cadre_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.cadre_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect :searchable="true" v-model="form.cadre_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
                label="title" track-by="id" open-direction="auto" :multiple="true"
                :close-on-select="false">
                <template slot="selection" slot-scope="{ cadre_id, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.cadre_id.length &amp;&amp; !isOpen">@{{ form.cadre_id.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}
            </div>
            {{-- {{json_encode(array_keys(trans('admin.appConfig')), TRUE)}} --}}
            {{-- {{json_encode(array_values(trans('admin.appConfig')), TRUE)}} --}}
            {{-- label="title" --}}
            {{-- :value="form.cadre_id.i" --}}
            {{-- track-by="id" --}}
            {{-- :options="{{ json_encode($cadre, TRUE) }}" --}}
            {{-- :options='form.all_cadres' --}}
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{
            'has-danger': errors.has('assessment_type'),
            'has-success': fields.assessment_type && fields.assessment_type
                .valid
        }">
        <label for="assessment_type" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.assessment_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.assessment_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_type'), 'form-control-success': fields.assessment_type && fields.assessment_type.valid}" id="assessment_type" name="assessment_type" placeholder="{{ trans('admin.assessment.columns.assessment_type') }}"> --}}
            <select class="form-control" v-model="form.assessment_type" v-validate="'required'"
                @input="validate($event)"
                :class="{
                    'form-control-danger': errors.has('assessment_type'),
                    'form-control-success': fields.assessment_type &&
                        fields.assessment_type.valid
                }"
                id="assessment_type" name="assessment_type"
                placeholder="{{ trans('admin.assessment.columns.assessment_type') }}">
                <option value="">Select Options</option>
                <option value="planned">Planned</option>
                <option value="real_time">Real Time</option>
            </select>
            <div v-if="errors.has('assessment_type')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('assessment_type') }}</div>
        </div>
    </div>

    <div v-if="form.assessment_type == 'planned'" class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('from_date'), 'has-success': fields.from_date && fields.from_date.valid }">
        <label for="from_date" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.from_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.from_date" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('from_date'), 'form-control-success': fields.from_date && fields.from_date.valid}" id="from_date" name="from_date" placeholder="{{ trans('admin.assessment.columns.from_date') }}"> --}}
            <datetime v-model="form.from_date" :config="datetimePickerConfig" class="flatpickr"
                placeholder="Select date "></datetime>
            <div v-if="errors.has('from_date')" class="form-control-feedback form-text" v-cloak>
                @{{ errors.first('from_date') }}
            </div>
        </div>
    </div>

    <div v-if="form.assessment_type == 'planned'" class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('to_date'), 'has-success': fields.to_date && fields.to_date.valid }">
        <label for="to_date" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.to_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            {{-- <input type="text" v-model="form.to_date" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('to_date'), 'form-control-success': fields.to_date && fields.to_date.valid}" id="to_date" name="to_date" placeholder="{{ trans('admin.assessment.columns.to_date') }}"> --}}
            {{-- <datetime
            type="datetime"
            v-model="datetimeEmpty"
            input-class="my-class"
            value-zone="America/New_York"
            zone="Asia/Shanghai"
            :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit', timeZoneName: 'short' }"
            :phrases="{ok: 'Continue', cancel: 'Exit'}"
            :hour-step="2"
            :minute-step="15"
            :min-datetime="minDatetime"
            :max-datetime="maxDatetime"
            :week-start="7"
            use12-hour
            auto
        ></datetime> --}}
            <datetime v-model="form.to_date" :config="datetimePickerConfig" class="flatpickr"
                placeholder="Select date "></datetime>
            <div v-if="errors.has('to_date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('to_date') }}
            </div>
        </div>
    </div>
</div>

<div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('certificate_type'), 'has-success': fields.country && fields.certificate_type.valid }">
            <label for="certificate" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment.columns.certificate_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
                <multiselect v-model="form.certificate_type"
                    placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                    track-by="id" :options="{{ $certificate->toJson() }}" :multiple="false"
                    :close-on-select="true" :searchable="true" open-direction="auto">
                </multiselect>
                <div v-if="errors.has('certificate_type')" class="form-control-feedback form-text" v-cloak>
                    @{{ errors.first('certificate_type') }}
                </div>
            </div>
        </div>


<div class="form-check row"
    :class="{ 'has-danger': errors.has('activated'), 'has-success': fields.activated && fields.activated.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="activated" type="checkbox" v-model="form.activated" v-validate="''"
            data-vv-name="activated" name="activated_fake_element">
        <label class="form-check-label" for="activated">
            {{ trans('admin.assessment.columns.activated') }}
        </label>
        <input type="hidden" name="activated" :value="form.activated">
        <div v-if="errors.has('activated')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('activated') }}
        </div>
    </div>
</div>
