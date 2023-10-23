<div class="row form-inline" style="padding-bottom: 10px;" v-cloak>
    <div
        :class="{'col-xl-10 col-md-11 text-right': !isFormLocalized, 'col text-center': isFormLocalized, 'hidden': onSmallScreen }">
        <small>{{ trans('brackets/admin-ui::admin.forms.currently_editing_translation') }}<span
                v-if="!isFormLocalized && otherLocales.length > 1">
                {{ trans('brackets/admin-ui::admin.forms.more_can_be_managed') }}</span><span v-if="!isFormLocalized">
                | <a href="#"
                    @click.prevent="showLocalization">{{ trans('brackets/admin-ui::admin.forms.manage_translations') }}</a></span></small>
        <i class="localization-error" v-if="!isFormLocalized && showLocalizedValidationError"></i>
    </div>

    <div class="col text-center"
        :class="{'language-mobile': onSmallScreen, 'has-error': !isFormLocalized && showLocalizedValidationError}"
        v-if="isFormLocalized || onSmallScreen" v-cloak>
        <small>{{ trans('brackets/admin-ui::admin.forms.choose_translation_to_edit') }}
            <select class="form-control" v-model="currentLocale">
                <option :value="defaultLocale" v-if="onSmallScreen">@{{ defaultLocale . toUpperCase() }}</option>
                <option v-for="locale in otherLocales" :value="locale">@{{ locale . toUpperCase() }}</option>
            </select>
            <i class="localization-error" v-if="isFormLocalized && showLocalizedValidationError"></i>
            <span>|</span>
            <a href="#" @click.prevent="hideLocalization">{{ trans('brackets/admin-ui::admin.forms.hide') }}</a>
        </small>
    </div>
</div>

{{-- <div class="row">
    @foreach ($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }">
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.master-cm.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.master-cm.columns.title') }}">
                    <div v-if="errors.has('title_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div> --}}

<div class="form-group row align-items-center"
    :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right"
        :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.master-cm.columns.title') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.master-cm.columns.title') }}"> --}}
        <select class="form-control" v-model="form.title" v-validate="'required'" @input="validate($event)"
            :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}"
            id="title" name="title" placeholder="{{ trans('admin.master-cm.columns.title') }}">
            <option value="">Select Type</option>
            <option value="About IIPHG">About IIPHG</option>
            <option value="About CGC">About CGC</option>
            <option value="Nutrition Outcome Details">Nutrition Outcome Details</option>
            <option value="Presumptive Pulmonary TB">Presumptive Pulmonary TB</option>
            <option value="Presumptive Extra-Pulmonary TB">Presumptive Extra-Pulmonary TB</option>
            <option value="Presumptive Pulmonary Pediatric TB">Presumptive Pulmonary Pediatric TB</option>
            <option value="Presumptive Extra-Pulmonary Pediatric TB">Presumptive Extra-Pulmonary Pediatric TB</option>

        </select>
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors . first('title') }}
        </div>
    </div>
</div>

<div class="row">
    @foreach ($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center"
                :class="{'has-danger': errors.has('description_{{ $locale }}'), 'has-success': fields.description_{{ $locale }} && fields.description_{{ $locale }}.valid }">
                <label for="description_{{ $locale }}"
                    class="col-md-2 col-form-label text-md-right">{{ trans('admin.master-cm.columns.description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <div>
                        {{-- <wysiwyg v-model="form.description.{{ $locale }}" v-validate="''"
                            id="description_{{ $locale }}" name="description_{{ $locale }}"
                            :config="mediaWysiwygConfig"></wysiwyg> --}}
                            <ckeditor  type="classic" id="description" v-model="form.description.{{ $locale }}" @input="$emit('input', $event);" :config="editorConfig"></ckeditor>
                    </div>
                    <div v-if="errors.has('description_{{ $locale }}')" class="form-control-feedback form-text"
                        v-cloak>{{ '{{' }} errors.first('description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
