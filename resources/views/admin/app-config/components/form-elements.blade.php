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
<span v-if="form.id">
    <div class="form-group row align-items-center" :class="{'has-danger': errors.has('key'), 'has-success': fields.key && fields.key.valid }">
        <label for="key" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.app-config.columns.key') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <input type="text" v-model="form.key" v-validate="''" @input="validate($event)" class="form-control" :disabled="true" :class="{'form-control-danger': errors.has('key'), 'form-control-success': fields.key && fields.key.valid}" id="key" name="key" placeholder="{{ trans('admin.app-config.columns.key') }}">
            <div v-if="errors.has('key')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('key') }}</div>
        </div>
    </div>
</span>
<span v-else>
<div class="form-group row align-items-center" :class="{'has-danger': errors.has('key'), 'has-success': fields.key && fields.key.valid }">
    <label for="key" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.app-config.columns.key') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.key" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('key'), 'form-control-success': fields.key && fields.key.valid}" id="key" name="key" placeholder="{{ trans('admin.app-config.columns.key') }}">
        <div v-if="errors.has('key')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('key') }}</div>
    </div>
</div>
</span>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('value_json_{{ $locale }}'), 'has-success': fields.value_json_{{ $locale }} && fields.value_json_{{ $locale }}.valid }">
                <label for="value_json_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.app-config.columns.value_json') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.value_json.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('value_json_{{ $locale }}'), 'form-control-success': fields.value_json_{{ $locale }} && fields.value_json_{{ $locale }}.valid }" id="value_json_{{ $locale }}" name="value_json_{{ $locale }}" placeholder="{{ trans('admin.app-config.columns.value_json') }}">
                    <div v-if="errors.has('value_json_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('value_json_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>





