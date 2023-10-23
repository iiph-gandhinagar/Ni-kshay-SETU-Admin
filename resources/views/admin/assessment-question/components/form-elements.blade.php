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

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('assessment_id'), 'has-success': fields.assessment_id && fields.assessment_id.valid }">
    <label for="assessment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.assessment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.assessment_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.assessment-question.columns.assessment_id') }}"> --}}
        <select class="form-control" v-model="form.assessment_id" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.assessment-question.columns.assessment_id') }}">
            <option value="">Select Options</option> 
            @foreach ($assessment as $item)
                <option value="{{ $item->id }}" >{{ $item->assessment_title }}</option>  
            @endforeach    
      </select>
        <div v-if="errors.has('assessment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('assessment_id') }}</div>
    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('question{{ $locale }}'), 'has-success': fields.question{{ $locale }} && fields.question{{ $locale }}.valid }">
                <label for="question{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment-question.columns.question') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.question.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question{{ $locale }}'), 'form-control-success': fields.question{{ $locale }} && fields.question{{ $locale }}.valid }" id="question{{ $locale }}" name="question{{ $locale }}" placeholder="{{ trans('admin.assessment-question.columns.question') }}">
                    <div v-if="errors.has('question{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('question{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option1{{ $locale }}'), 'has-success': fields.option1{{ $locale }} && fields.option1{{ $locale }}.valid }">
                <label for="option1{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment-question.columns.option1') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option1.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option1{{ $locale }}'), 'form-control-success': fields.option1{{ $locale }} && fields.option1{{ $locale }}.valid }" id="option1{{ $locale }}" name="option1{{ $locale }}" placeholder="{{ trans('admin.assessment-question.columns.option1') }}">
                    <div v-if="errors.has('option1{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option1{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option2{{ $locale }}'), 'has-success': fields.option2{{ $locale }} && fields.option2{{ $locale }}.valid }">
                <label for="option2{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment-question.columns.option2') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option2.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option2{{ $locale }}'), 'form-control-success': fields.option2{{ $locale }} && fields.option2{{ $locale }}.valid }" id="option2{{ $locale }}" name="option2{{ $locale }}" placeholder="{{ trans('admin.assessment-question.columns.option2') }}">
                    <div v-if="errors.has('option2{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option2{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option3{{ $locale }}'), 'has-success': fields.option3{{ $locale }} && fields.option3{{ $locale }}.valid }">
                <label for="option3{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment-question.columns.option3') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option3.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option3{{ $locale }}'), 'form-control-success': fields.option3{{ $locale }} && fields.option3{{ $locale }}.valid }" id="option3{{ $locale }}" name="option3{{ $locale }}" placeholder="{{ trans('admin.assessment-question.columns.option3') }}">
                    <div v-if="errors.has('option3{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option3{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('option4{{ $locale }}'), 'has-success': fields.option4{{ $locale }} && fields.option4{{ $locale }}.valid }">
                <label for="option4{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.assessment-question.columns.option4') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.option4.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('option4{{ $locale }}'), 'form-control-success': fields.option4{{ $locale }} && fields.option4{{ $locale }}.valid }" id="option4{{ $locale }}" name="option4{{ $locale }}" placeholder="{{ trans('admin.assessment-question.columns.option4') }}">
                    <div v-if="errors.has('option4{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('option4{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('correct_answer'), 'has-success': fields.correct_answer && fields.correct_answer.valid }">
    <label for="correct_answer" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.correct_answer') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.correct_answer" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('correct_answer'), 'form-control-success': fields.correct_answer && fields.correct_answer.valid}" id="correct_answer" name="correct_answer" placeholder="{{ trans('admin.assessment-question.columns.correct_answer') }}"> --}}
        <select class="form-control" v-model="form.correct_answer" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('correct_answer'), 'form-control-success': fields.correct_answer && fields.correct_answer.valid}" id="correct_answer" name="correct_answer" placeholder="{{ trans('admin.assessment-question.columns.correct_answer') }}">
              <option value="">Select Options</option>                              
            <option value="option1">option 1</option>
            <option value="option2">option 2</option>
            <option value="option3">option 3</option>
            <option value="option4">option 4</option>
        </select>
        <div v-if="errors.has('correct_answer')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('correct_answer') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('category'), 'has-success': fields.category && fields.category.valid }">
    <label for="category" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.category') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.category" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('category'), 'form-control-success': fields.category && fields.category.valid}" id="category" name="category" placeholder="{{ trans('admin.assessment-question.columns.category') }}"> --}}
        <select class="form-control" v-model="form.category" v-validate="''" @input="validate($event)" :class="{'form-control-danger': errors.has('category'), 'form-control-success': fields.category && fields.category.valid}" id="category" name="category" placeholder="{{ trans('admin.assessment-question.columns.category') }}">
          <option value="">Select Options</option>                              
          <option value="Adverse Drug Reaction Management">Adverse Drug Reaction Management</option>
          <option value="Case Findings and Diagnostic Strategy">Case Findings and Diagnostic Strategy</option>
          <option value="Infection Control Measures">Infection Control Measures</option>
          <option value="Public Health Actions (PHA)">Public Health Actions (PHA)</option>
          <option value="Recordings and Reporting">Recordings and Reporting</option>
          <option value="Treatment of TB">Treatment of TB</option>
          <option value="New PMDT">New PMDT</option>
          <option value="Programmatic Management of TB preventive Treatment (PMTPT)">Programmatic Management of TB preventive Treatment (PMTPT)</option>
          <option value="Supply Chain Management">Supply Chain Management</option>
          <option value="Diagnostic QA Mechanism">Diagnostic QA Mechanism</option>
          <option value="Surveillance">Surveillance</option>
          <option value="Supervision and M&E">Supervision and M&E</option>
          <option value="NPY - Incentive">NPY - Incentive</option>
          <option value="Programmatic Knowledge on NTEP">Programmatic Knowledge on NTEP</option>
          <option value="ACSM">ACSM</option>
          <option value="Program Implementation Plan - NHM">Program Implementation Plan - NHM</option>
          <option value="TB Comorbidities">TB Comorbidities</option>
          <option value="No Category">No Category</option>
      </select>
        <div v-if="errors.has('category')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('category') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-question.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.assessment-question.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>


