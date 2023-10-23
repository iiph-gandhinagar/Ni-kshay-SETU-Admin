<div class="row form-inline" style="padding-bottom: 10px;" v-cloak>
    <div :class="{'col-xl-10 col-md-11 text-right': !isFormLocalized, 'col text-center': isFormLocalized, 'hidden': onSmallScreen }">
        <small>{{ trans('brackets/admin-ui::admin.forms.currently_editing_translation') }}<span v-if="!isFormLocalized && otherLocales.length > 1"> {{ trans('brackets/admin-ui::admin.forms.more_can_be_managed') }}</span><span v-if="!isFormLocalized"> | <a href="#" @click.prevent="showLocalization">{{ trans('brackets/admin-ui::admin.forms.manage_translations') }}</a></span></small>
        <i class="localization-error" v-if="!isFormLocalized && showLocalizedValidationError"></i>
    </div>

    <div class="col text-center" :class="{'language-mobile': onSmallScreen, 'has-error': !isFormLocalized && showLocalizedValidationError}" v-if="isFormLocalized || onSmallScreen" v-cloak>
        <small>{{ trans('brackets/admin-ui::admin.forms.choose_translation_to_edit') }}
            <select class="form-control" v-model="currentLocale">
                <option :value="defaultLocale" v-if="onSmallScreen">@{{defaultLocale.toUpperCase()}}</option>
                <option v-for="locale in otherLocales" :value="locale">@{{locale.toUpperCase()}}</option>
            </select>
            <i class="localization-error" v-if="isFormLocalized && showLocalizedValidationError"></i>
            <span>|</span>
            <a href="#" @click.prevent="hideLocalization">{{ trans('brackets/admin-ui::admin.forms.hide') }}</a>
        </small>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title_{{ $locale }}'), 'has-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }">
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.survey-master.columns.title') }}">
                    <div v-if="errors.has('title_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

    @if ($user_state == '')
        <div class="form-group row align-items-center"
            :class="{ 'has-danger': errors.has('country_id'), 'has-success': fields.country && fields.country.valid }">
            <label for="country_id" class="col-form-label text-md-right"
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.country_id') }}</label>
            <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
                <multiselect v-model="form.country_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" :options="{{ $country->toJson() }}" :multiple="false" :close-on-select="false" :searchable="true" open-direction="auto">
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
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect v-model="form.state_id"
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
            <button type="button" class="btn btn-primary" v-on:click="selectAllStates">
                <span v-if="form.all_states.length == form.state_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district_id'), 'has-success': fields.district_id && fields.district_id.valid }">
        <label for="district_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.district_id') }}</label>
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
            <div v-if="errors.has('district_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district_id') }}
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
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select  v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.survey-master.columns.cadre_type') }}">
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
                :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.cadre_type') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
                <select  v-on:change="getCadresOnChangeOfType" class="form-control" v-model="form.cadre_type"
                    v-validate="'required'" @input="validate($event)"
                    :class="{
                        'form-control-danger': errors.has('cadre_type'),
                        'form-control-success': fields.cadre_type && fields
                            .cadre_type.valid
                    }"
                    id="cadre_type" name="cadre_type"
                    placeholder="{{ trans('admin.survey-master.columns.cadre_type') }}">
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
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.cadre_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect  :searchable="true" v-model="form.cadre_id"
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
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre_id.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>


<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.survey-master.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.survey-master.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>


