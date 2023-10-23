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


<div class="form-group row align-items-center" :class="{'has-danger': errors.has('level_id'), 'has-success': fields.level_id && fields.level_id.valid }">
    <label for="level_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-badge.columns.level_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.level_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('level_id'), 'form-control-success': fields.level_id && fields.level_id.valid}" id="level_id" name="level_id" placeholder="{{ trans('admin.lb-badge.columns.level_id') }}"> --}}
        <multiselect v-model="form.level_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="level" track-by="id" 
            :options="{{ $lb_level->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('level_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('level_id') }}</div>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('badge_{{ $locale }}'), 'has-success': fields.badge_{{ $locale }} && fields.badge_{{ $locale }}.valid }">
                <label for="badge_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.lb-badge.columns.badge') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.badge.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('badge_{{ $locale }}'), 'form-control-success': fields.badge_{{ $locale }} && fields.badge_{{ $locale }}.valid }" id="badge_{{ $locale }}" name="badge_{{ $locale }}" placeholder="{{ trans('admin.lb-badge.columns.badge') }}">
                    <div v-if="errors.has('badge_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('badge_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

