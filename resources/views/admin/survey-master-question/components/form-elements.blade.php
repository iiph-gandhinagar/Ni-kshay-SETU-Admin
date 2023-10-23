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

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('survey_master_id'), 'has-success': fields.survey_master_id && fields.survey_master_id.valid }">
    <label for="survey_master_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-question.columns.survey_master_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.survey_master_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('survey_master_id'), 'form-control-success': fields.survey_master_id && fields.survey_master_id.valid}" id="survey_master_id" name="survey_master_id" placeholder="{{ trans('admin.survey-master-question.columns.survey_master_id') }}"> --}}
        <multiselect v-model="form.survey_master_id" 
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" 
        label="title" track-by="id" 
        :options="{{ $survey_master}}" 
        :multiple="false" 
        open-direction="auto" 
        :close-on-select="false"></multiselect>
        <div v-if="errors.has('survey_master_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('survey_master_id') }}</div>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('question_{{ $locale }}'), 'has-success': fields.question_{{ $locale }} && fields.question_{{ $locale }}.valid }">
                <label for="question_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master-question.columns.question') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.question.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question_{{ $locale }}'), 'form-control-success': fields.question_{{ $locale }} && fields.question_{{ $locale }}.valid }" id="question_{{ $locale }}" name="question_{{ $locale }}" placeholder="{{ trans('admin.survey-master-question.columns.question') }}">
                    <div v-if="errors.has('question_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('question_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type'), 'has-success': fields.type && fields.type.valid }">
    <label for="type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-question.columns.type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}" id="type" name="type" placeholder="{{ trans('admin.survey-master-question.columns.type') }}">--}}
        <select class="form-control" v-model="form.type" v-validate="''" @input="validate($event)" :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}" id="type" name="type" placeholder="{{ trans('admin.survey-master-question.columns.type') }}">
          <option value="">Select Options</option>                              
          <option value="options">Options</option>
          <option value="text_area">Text</option>
      </select>
        <div v-if="errors.has('type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type') }}</div>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div v-if="form.type == 'options'" class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option1_{{ $locale }}'), 'has-success': fields.option1_{{ $locale }} && fields.option1_{{ $locale }}.valid }">
                <label for="option1_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master-question.columns.option1') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option1.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option1_{{ $locale }}'), 'form-control-success': fields.option1_{{ $locale }} && fields.option1_{{ $locale }}.valid }" id="option1_{{ $locale }}" name="option1_{{ $locale }}" placeholder="{{ trans('admin.survey-master-question.columns.option1') }}">
                    <div v-if="errors.has('option1_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option1_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div v-if="form.type == 'options'" class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option2_{{ $locale }}'), 'has-success': fields.option2_{{ $locale }} && fields.option2_{{ $locale }}.valid }">
                <label for="option2_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master-question.columns.option2') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option2.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option2_{{ $locale }}'), 'form-control-success': fields.option2_{{ $locale }} && fields.option2_{{ $locale }}.valid }" id="option2_{{ $locale }}" name="option2_{{ $locale }}" placeholder="{{ trans('admin.survey-master-question.columns.option2') }}">
                    <div v-if="errors.has('option2_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option2_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div v-if="form.type == 'options'" class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option3_{{ $locale }}'), 'has-success': fields.option3_{{ $locale }} && fields.option3_{{ $locale }}.valid }">
                <label for="option3_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master-question.columns.option3') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option3.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option3_{{ $locale }}'), 'form-control-success': fields.option3_{{ $locale }} && fields.option3_{{ $locale }}.valid }" id="option3_{{ $locale }}" name="option3_{{ $locale }}" placeholder="{{ trans('admin.survey-master-question.columns.option3') }}">
                    <div v-if="errors.has('option3_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option3_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div v-if="form.type == 'options'" class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option4_{{ $locale }}'), 'has-success': fields.option4_{{ $locale }} && fields.option4_{{ $locale }}.valid }">
                <label for="option4_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.survey-master-question.columns.option4') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option4.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option4_{{ $locale }}'), 'form-control-success': fields.option4_{{ $locale }} && fields.option4_{{ $locale }}.valid }" id="option4_{{ $locale }}" name="option4_{{ $locale }}" placeholder="{{ trans('admin.survey-master-question.columns.option4') }}">
                    <div v-if="errors.has('option4_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option4_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.survey-master-question.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.survey-master-question.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.survey-master-question.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>
