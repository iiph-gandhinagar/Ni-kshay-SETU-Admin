<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('first_name'), 'has-success': fields.first_name && fields.first_name.valid }">
    <label for="first_name" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.first_name') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <input type="text" v-model="form.first_name" v-validate="''" @input="validate($event)" class="form-control"
            :class="{
                'form-control-danger': errors.has('first_name'),
                'form-control-success': fields.first_name && fields
                    .first_name.valid
            }"
            id="first_name" name="first_name" placeholder="{{ trans('admin.admin-user.columns.first_name') }}">
        <div v-if="errors.has('first_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('first_name') }}
        </div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('last_name'), 'has-success': fields.last_name && fields.last_name.valid }">
    <label for="last_name" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.last_name') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <input type="text" v-model="form.last_name" v-validate="''" @input="validate($event)" class="form-control"
            :class="{
                'form-control-danger': errors.has('last_name'),
                'form-control-success': fields.last_name && fields
                    .last_name.valid
            }"
            id="last_name" name="last_name" placeholder="{{ trans('admin.admin-user.columns.last_name') }}">
        <div v-if="errors.has('last_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('last_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('email'), 'has-success': fields.email && fields.email.valid }">
    <label for="email" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.email') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <input type="text" v-model="form.email" v-validate="'required|email'" @input="validate($event)"
            class="form-control"
            :class="{ 'form-control-danger': errors.has('email'), 'form-control-success': fields.email && fields.email.valid }"
            id="email" name="email" placeholder="{{ trans('admin.admin-user.columns.email') }}">
        <div v-if="errors.has('email')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('email') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('password'), 'has-success': fields.password && fields.password.valid }">
    <label for="password" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.password') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <input type="password" v-model="form.password" v-validate="'min:7'" @input="validate($event)"
            class="form-control"
            :class="{
                'form-control-danger': errors.has('password'),
                'form-control-success': fields.password && fields.password
                    .valid
            }"
            id="password" name="password" placeholder="{{ trans('admin.admin-user.columns.password') }}"
            ref="password">
        <div v-if="errors.has('password')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('password') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{
        'has-danger': errors.has('password_confirmation'),
        'has-success': fields.password_confirmation && fields
            .password_confirmation.valid
    }">
    <label for="password_confirmation" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.password_repeat') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <input type="password" v-model="form.password_confirmation" v-validate="'confirmed:password|min:7'"
            @input="validate($event)" class="form-control"
            :class="{
                'form-control-danger': errors.has('password_confirmation'),
                'form-control-success': fields
                    .password_confirmation && fields.password_confirmation.valid
            }"
            id="password_confirmation" name="password_confirmation"
            placeholder="{{ trans('admin.admin-user.columns.password') }}" data-vv-as="password">
        <div v-if="errors.has('password_confirmation')" class="form-control-feedback form-text" v-cloak>
            @{{ errors.first('password_confirmation') }}</div>
    </div>
</div>

<div class="form-group row"
    :class="{ 'has-danger': errors.has('activated'), 'has-success': fields.activated && fields.activated.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-9'">
        <input class="form-check-input" id="activated" type="checkbox" v-model="form.activated" v-validate="''"
            data-vv-name="activated" name="activated_fake_element">
        <label class="form-check-label" for="activated">
            {{ trans('admin.admin-user.columns.activated') }}
        </label>
        <input type="hidden" name="activated" :value="form.activated">
        <div v-if="errors.has('activated')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('activated') }}</div>
    </div>
</div>

<div class="form-group row"
    :class="{ 'has-danger': errors.has('forbidden'), 'has-success': fields.forbidden && fields.forbidden.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-9'">
        <input class="form-check-input" id="forbidden" type="checkbox" v-model="form.forbidden" v-validate="''"
            data-vv-name="forbidden" name="forbidden_fake_element">
        <label class="form-check-label" for="forbidden">
            {{ trans('admin.admin-user.columns.forbidden') }}
        </label>
        <input type="hidden" name="forbidden" :value="form.forbidden">
        <div v-if="errors.has('forbidden')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('forbidden') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('language'), 'has-success': fields.language && fields.language.valid }">
    <label for="language" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.language') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <multiselect v-model="form.language"
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
            :options="{{ $locales->toJson() }}" open-direction="bottom"></multiselect>
        <div v-if="errors.has('language')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('language') }}
        </div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('roles'), 'has-success': fields.roles && fields.roles.valid }">
    <label for="roles" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.roles') }}</label>
        <button class="btn btn-sm btn-primary" role="button" v-tooltip="'Permissions can be effectively organized based on different roles.'"><i class="fa fa-info"></i></button>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        <multiselect v-model="form.roles" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}"
            label="name" track-by="id" :options="{{ $roles->toJson() }}" :multiple="false"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('roles')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('roles') }}</div>
    </div>
</div>

<div class="form-group row align-items-center"
    :class="{ 'has-danger': errors.has('role_type'), 'has-success': fields.role_type && fields.role_type.valid }"> 
    <label for="role_type" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.role_type') }}</label>
        <button class="btn btn-sm btn-primary" role="button" v-tooltip="'Data will be presented according to the role type field.'"><i class="fa fa-info"></i></button>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
        {{-- <multiselect v-model="form.role_type" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :options="{{ $role_type->toJson() }}" :multiple="false" open-direction="auto"></multiselect> --}}
        <select class="form-control" v-model="form.role_type" v-on:change="clearInputs();getCadresOnChangeOfType()" v-validate="'required'" @input="validate($event)"
            :class="{
                'form-control-danger': errors.has('role_type'),
                'form-control-success': fields.role_type && fields
                    .role_type.valid
            }"
            id="role_type" name="role_type" placeholder="{{ trans('admin.admin-user.columns.role_type') }}">
            <option value="">Select Options</option>
            <option value="country_type">Country Type</option>
            <option value="state_type">State Type</option>
            <option value="district_type">District Type</option>
        </select>
        <div v-if="errors.has('role_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('role_type') }}
        </div>
    </div>
</div>

<div v-if="form.role_type == 'country_type'">
    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('country'), 'has-success': fields.country && fields.country.valid }">
        <label for="country" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.country') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'All data will be displayed at the India level, based on the role type field.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            {{-- <input type="text" v-model="form.country" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('country'), 'form-control-success': fields.country && fields.country.valid}" id="country" name="country" placeholder="{{ trans('admin.admin-user.columns.country') }}"> --}}
            <multiselect v-model="form.country"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $country->toJson() }}" @input="selectAllStates();selectAllDistrict()" :multiple="false" open-direction="auto">
            </multiselect>
            <div v-if="errors.has('country')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country') }}
            </div>
        </div>
    </div>
    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('state'), 'has-success': fields.state && fields.state.valid }">
        <label for="state" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.state') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'All data will be displayed at the India level, based on the role type field.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect v-model="form.state" disabled
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="form.all_states" :multiple="true" :close-on-select="false"
                :searchable="true" open-direction="auto">
                <template slot="selection" slot-scope="{ state, search, isOpen }"><span class="multiselect__single"
                        v-if="form.state.length &amp;&amp; !isOpen">@{{ form.state.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('state')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state') }}
            </div>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district'), 'has-success': fields.district && fields.district.valid }">
        <label for="district" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.district') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'All data will be displayed at the India level, based on the role type field.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            {{-- <input type="text" v-model="form.district" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district'), 'form-control-success': fields.district && fields.district.valid}" id="district" name="district" placeholder="{{ trans('admin.subscriber.columns.district') }}"> --}}
            <multiselect :searchable="true" v-model="form.district" disabled
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
                :options="form.district_options" label="title" track-by="id" open-direction="auto"
                :close-on-select="false" :multiple="true">
                <template slot="selection" slot-scope="{ district, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.district.length &amp;&amp; !isOpen">@{{ form.district.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('district')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district') }}
            </div>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('cadre'), 'has-success': fields.cadre && fields.cadre.valid }">
        <label for="cadre" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.cadre') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'All data will be displayed at the India level, based on the role type field.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect :searchable="true" v-model="form.cadre"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
                label="title" track-by="id" :close-on-select="false" open-direction="auto"
                :multiple="true">
                <template slot="selection" slot-scope="{ cadre, search, isOpen }"><span class="multiselect__single"
                        v-if="form.cadre.length &amp;&amp; !isOpen">@{{ form.cadre.length }} options
                        selected</span></template>
            </multiselect>
            {{-- <select class="form-control" v-model="form.cadre" v-validate="'required'" :options="form.options" label="title" track-by="id" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre'), 'form-control-success': fields.cadre && fields.cadre.valid}" id="cadre" name="cadre" placeholder="{{ trans('admin.subscriber.columns.cadre') }}"></select> --}}
            <div v-if="errors.has('cadre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>
</div>

<div v-if="form.role_type == 'state_type'">
    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('state'), 'has-success': fields.state && fields.state.valid }">
        <label for="state" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.state') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'Data will be displayed based on the role type, specifically at the state level, encompassing all districts within that state.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect v-model="form.state"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $states->toJson() }}" :multiple="false" :close-on-select="true"
                :searchable="true" @input="getDistrictList()" open-direction="auto">
            </multiselect>
            <div v-if="errors.has('state')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state') }}
            </div>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district'), 'has-success': fields.district && fields.district.valid }">
        <label for="district" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.district') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'Data will be displayed based on the role type, specifically at the state level, encompassing all districts within that state.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            {{-- <input type="text" v-model="form.district" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district'), 'form-control-success': fields.district && fields.district.valid}" id="district" name="district" placeholder="{{ trans('admin.subscriber.columns.district') }}"> --}}
            <multiselect :searchable="true" v-model="form.district" disabled
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
                :options="form.district_options" label="title" track-by="id" open-direction="auto"
                :close-on-select="false" :multiple="true">
                <template slot="selection" slot-scope="{ district, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.district.length &amp;&amp; !isOpen">@{{ form.district.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('district')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district') }}
            </div>
        </div>
    </div>

    <div 
        class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('cadre'), 'has-success': fields.cadre && fields.cadre.valid }">
        <label for="cadre" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.cadre') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'Data will be displayed based on the role type, specifically at the state level, encompassing all districts within that state.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect :searchable="true" v-model="form.cadre"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
                label="title" track-by="id" :close-on-select="false" open-direction="auto"
                :multiple="true">
                <template slot="selection" slot-scope="{ cadre, search, isOpen }"><span class="multiselect__single"
                        v-if="form.cadre.length &amp;&amp; !isOpen">@{{ form.cadre.length }} options
                        selected</span></template>
            </multiselect>
            {{-- <select class="form-control" v-model="form.cadre" v-validate="'required'" :options="form.options" label="title" track-by="id" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre'), 'form-control-success': fields.cadre && fields.cadre.valid}" id="cadre" name="cadre" placeholder="{{ trans('admin.subscriber.columns.cadre') }}"></select> --}}
            <div v-if="errors.has('cadre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>
</div>

<div v-if="form.role_type == 'district_type'">
    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('state'), 'has-success': fields.state && fields.state.valid }">
        <label for="state" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.state') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'When the role type is set to district level, all data will be shown based on a single state and the selected district.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect v-model="form.state"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $states->toJson() }}" :multiple="false"
                :close-on-select="true" :searchable="true" @input="getDistrictForOneState"
                open-direction="auto">
                <template slot="selection" slot-scope="{ state, search, isOpen }"><span class="multiselect__single"
                        v-if="form.state.length &amp;&amp; !isOpen">@{{ form.state.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('state')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state') }}
            </div>
        </div>
    </div>

    <div class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('district'), 'has-success': fields.district && fields.district.valid }">
        <label for="district" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.district') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'When the role type is set to district level, all data will be shown based on a single state and the selected district.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            {{-- <input type="text" v-model="form.district" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('district'), 'form-control-success': fields.district && fields.district.valid}" id="district" name="district" placeholder="{{ trans('admin.subscriber.columns.district') }}"> --}}
            <multiselect :searchable="true" v-model="form.district"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}"
                :options="form.district_options" label="title" track-by="id" open-direction="auto"
                :close-on-select="false" :multiple="true">
                <template slot="selection" slot-scope="{ district, search, isOpen }"><span
                        class="multiselect__single"
                        v-if="form.district.length &amp;&amp; !isOpen">@{{ form.district.length }} options
                        selected</span></template>
            </multiselect>
            <div v-if="errors.has('district')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('district') }}
            </div>
        </div>
    </div>

    <div
        class="form-group row align-items-center"
        :class="{ 'has-danger': errors.has('cadre'), 'has-success': fields.cadre && fields.cadre.valid }">
        <label for="cadre" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-3'">{{ trans('admin.admin-user.columns.cadre') }}</label>
            <button class="btn btn-sm btn-primary" role="button" v-tooltip="'When the role type is set to district level, all data will be shown based on a single state and the selected district.'"><i class="fa fa-info"></i></button>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-7'">
            <multiselect :searchable="true" v-model="form.cadre"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" :options="form.options"
                label="title" track-by="id" :close-on-select="false" open-direction="auto"
                :multiple="true">
                <template slot="selection" slot-scope="{ cadre, search, isOpen }"><span class="multiselect__single"
                        v-if="form.cadre.length &amp;&amp; !isOpen">@{{ form.cadre.length }} options
                        selected</span></template>
            </multiselect>
            {{-- <select class="form-control" v-model="form.cadre" v-validate="'required'" :options="form.options" label="title" track-by="id" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre'), 'form-control-success': fields.cadre && fields.cadre.valid}" id="cadre" name="cadre" placeholder="{{ trans('admin.subscriber.columns.cadre') }}"></select> --}}
            <div v-if="errors.has('cadre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre') }}
            </div>
        </div>
        <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
            <button type="button" class="btn btn-primary" v-on:click="selectAll">
                <span v-if="form.filtered_cadres.length == form.cadre.length">Clear</span>
                <span v-else>All</span>
            </button>
        </div>
    </div>
</div>
