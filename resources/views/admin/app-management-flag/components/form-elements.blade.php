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
@if(isset($appManagementFlag) && count($appManagementFlag->toArray()) > 0)
<div class="form-group row align-items-center" :class="{'has-danger': errors.has('variable'), 'has-success': fields.variable && fields.variable.valid }">
    <label for="variable" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.app-management-flag.columns.variable') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.variable" v-validate="'required'" @input="validate($event)" :disabled="true" class="form-control" :class="{'form-control-danger': errors.has('variable'), 'form-control-success': fields.variable && fields.variable.valid}" id="variable" name="variable" placeholder="{{ trans('admin.app-management-flag.columns.variable') }}">
        <div v-if="errors.has('variable')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('variable') }}</div>
    </div>
</div>
@else
    <div class="form-group row align-items-center" :class="{'has-danger': errors.has('variable'), 'has-success': fields.variable && fields.variable.valid }">
        <label for="variable" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.app-management-flag.columns.variable') }}</label>
            <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <input type="text" v-model="form.variable" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('variable'), 'form-control-success': fields.variable && fields.variable.valid}" id="variable" name="variable" placeholder="{{ trans('admin.app-management-flag.columns.variable') }}">
            <div v-if="errors.has('variable')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('variable') }}</div>
        </div>
    </div>
@endif

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type'), 'has-success': fields.type && fields.type.valid }">
    <label for="type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.app-management-flag.columns.type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <select class="form-control" v-model="form.type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}" id="type" name="type" placeholder="{{ trans('admin.case-definition.columns.type') }}">
                <option value="">Select Type</option>                              
                <option value="string">string</option>
                <option value="float">float</option>
                <option value="boolean">boolean</option>
                <option value="list">list</option>

          </select>
        <div v-if="errors.has('type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type') }}</div>
    </div>
</div>
<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div v-if="form.type == 'string' || form.type == 'float' || form.type == 'boolean'" class="form-group row align-items-center" :class="{'has-danger': errors.has('value_{{ $locale }}'), 'has-success': fields.value_{{ $locale }} && fields.value_{{ $locale }}.valid }">
                <label for="value_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.app-management-flag.columns.value') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.value.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('value_{{ $locale }}'), 'form-control-success': fields.value_{{ $locale }} && fields.value_{{ $locale }}.valid }" id="value_{{ $locale }}" name="value_{{ $locale }}" placeholder="{{ trans('admin.app-management-flag.columns.value') }}">
                    <div v-if="errors.has('value_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('value_{{ $locale }}') }}</div>
                </div>
            </div> 
        </div>
    @endforeach
</div>
<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div v-if="form.type == 'list' " class="form-group row align-items-center" :class="{'has-danger': errors.has('value_{{ $locale }}'), 'has-success': fields.value_{{ $locale }} && fields.value_{{ $locale }}.valid }">
                <label for="value{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.app-management-flag.columns.value') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                   {{-- <input type="text" v-model="form.value.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('value_{{ $locale }}'), 'form-control-success': fields.value_{{ $locale }} && fields.value_{{ $locale }}.valid }" id="value_{{ $locale }}" name="value_{{ $locale }}" placeholder="{{ trans('admin.app-management-flag.columns.value') }}"> --}}
                    <multiselect v-model="form.value.{{ $locale }}" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="options" :multiple="true" :taggable="true"  @tag="addFeature" open-direction="bottom"  :close-on-select="false"></multiselect>
                    <div v-if="errors.has('value_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('value_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>


