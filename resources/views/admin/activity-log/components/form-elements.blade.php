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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('properties_{{ $locale }}'), 'has-success': fields.properties_{{ $locale }} && fields.properties_{{ $locale }}.valid }">
                <label for="properties_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.activity-log.columns.properties') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.properties.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('properties_{{ $locale }}'), 'form-control-success': fields.properties_{{ $locale }} && fields.properties_{{ $locale }}.valid }" id="properties_{{ $locale }}" name="properties_{{ $locale }}" placeholder="{{ trans('admin.activity-log.columns.properties') }}">
                    <div v-if="errors.has('properties_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('properties_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('log_name'), 'has-success': fields.log_name && fields.log_name.valid }">
    <label for="log_name" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.log_name') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.log_name" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('log_name'), 'form-control-success': fields.log_name && fields.log_name.valid}" id="log_name" name="log_name" placeholder="{{ trans('admin.activity-log.columns.log_name') }}">
        <div v-if="errors.has('log_name')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('log_name') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('description'), 'has-success': fields.description && fields.description.valid }">
    <label for="description" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.description') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <wysiwyg v-model="form.description" v-validate="'required'" id="description" name="description" :config="mediaWysiwygConfig"></wysiwyg>
        </div>
        <div v-if="errors.has('description')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('description') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subject_type'), 'has-success': fields.subject_type && fields.subject_type.valid }">
    <label for="subject_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.subject_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subject_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subject_type'), 'form-control-success': fields.subject_type && fields.subject_type.valid}" id="subject_type" name="subject_type" placeholder="{{ trans('admin.activity-log.columns.subject_type') }}">
        <div v-if="errors.has('subject_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subject_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subject_id'), 'has-success': fields.subject_id && fields.subject_id.valid }">
    <label for="subject_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.subject_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subject_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subject_id'), 'form-control-success': fields.subject_id && fields.subject_id.valid}" id="subject_id" name="subject_id" placeholder="{{ trans('admin.activity-log.columns.subject_id') }}">
        <div v-if="errors.has('subject_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subject_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('causer_type'), 'has-success': fields.causer_type && fields.causer_type.valid }">
    <label for="causer_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.causer_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.causer_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('causer_type'), 'form-control-success': fields.causer_type && fields.causer_type.valid}" id="causer_type" name="causer_type" placeholder="{{ trans('admin.activity-log.columns.causer_type') }}">
        <div v-if="errors.has('causer_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('causer_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('causer_id'), 'has-success': fields.causer_id && fields.causer_id.valid }">
    <label for="causer_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.activity-log.columns.causer_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.causer_id" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('causer_id'), 'form-control-success': fields.causer_id && fields.causer_id.valid}" id="causer_id" name="causer_id" placeholder="{{ trans('admin.activity-log.columns.causer_id') }}">
        <div v-if="errors.has('causer_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('causer_id') }}</div>
    </div>
</div>


