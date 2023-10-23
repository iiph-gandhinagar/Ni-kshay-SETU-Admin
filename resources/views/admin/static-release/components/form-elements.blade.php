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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('features_{{ $locale }}'), 'has-success': fields.features_{{ $locale }} && fields.features_{{ $locale }}.valid }">
                <label for="features_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.static-release.columns.features') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    {{-- <input type="text" v-model="form.features.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('features_{{ $locale }}'), 'form-control-success': fields.features_{{ $locale }} && fields.features_{{ $locale }}.valid }" id="features_{{ $locale }}" name="features_{{ $locale }}" placeholder="{{ trans('admin.static-release.columns.features') }}"> --}}
                    <multiselect v-model="form.features.{{ $locale }}" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="options" :multiple="true" :taggable="true"  @tag="addTag" open-direction="bottom"  :close-on-select="false"></multiselect>
                    <div v-if="errors.has('features_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('features_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('bugs_fix_{{ $locale }}'), 'has-success': fields.bugs_fix_{{ $locale }} && fields.bugs_fix_{{ $locale }}.valid }">
                <label for="bugs_fix_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.static-release.columns.bugs_fix') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    {{-- <input type="text" v-model="form.bugs_fix.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('bugs_fix_{{ $locale }}'), 'form-control-success': fields.bugs_fix_{{ $locale }} && fields.bugs_fix_{{ $locale }}.valid }" id="bugs_fix_{{ $locale }}" name="bugs_fix_{{ $locale }}" placeholder="{{ trans('admin.static-release.columns.bugs_fix') }}"> --}}
                    <multiselect v-model="form.bugs_fix.{{ $locale }}" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="bug_options" :multiple="true" :taggable="true"  @tag="addBug" open-direction="bottom"  :close-on-select="false"></multiselect>
                    <div v-if="errors.has('bugs_fix_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('bugs_fix_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('date'), 'has-success': fields.date && fields.date.valid }">
    <label for="date" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-release.columns.date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.date" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('date'), 'form-control-success': fields.date && fields.date.valid}" id="date" name="date" placeholder="{{ trans('admin.static-release.columns.date') }}"> --}}
        <datetime v-model="form.date" :config="datePickerConfig" class="flatpickr" placeholder="Select date "></datetime>
        <div v-if="errors.has('date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('date') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-release.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.static-release.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.static-release.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>


