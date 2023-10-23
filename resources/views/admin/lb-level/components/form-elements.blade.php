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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('level_{{ $locale }}'), 'has-success': fields.level_{{ $locale }} && fields.level_{{ $locale }}.valid }">
                <label for="level_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.lb-level.columns.level') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.level.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('level_{{ $locale }}'), 'form-control-success': fields.level_{{ $locale }} && fields.level_{{ $locale }}.valid }" id="level_{{ $locale }}" name="level_{{ $locale }}" placeholder="{{ trans('admin.lb-level.columns.level') }}">
                    <div v-if="errors.has('level_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('level_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('content_{{ $locale }}'), 'has-success': fields.content_{{ $locale }} && fields.content_{{ $locale }}.valid }">
                <label for="content_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.lb-level.columns.content') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.content.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('content_{{ $locale }}'), 'form-control-success': fields.content_{{ $locale }} && fields.content_{{ $locale }}.valid }" id="content_{{ $locale }}" name="content_{{ $locale }}" placeholder="{{ trans('admin.lb-level.columns.content') }}">
                    <div v-if="errors.has('content_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('content_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>


