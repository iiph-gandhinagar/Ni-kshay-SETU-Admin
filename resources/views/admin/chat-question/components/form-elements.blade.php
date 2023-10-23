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

{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('like_count'), 'has-success': fields.like_count && fields.like_count.valid }">
    <label for="like_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.like_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.like_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('like_count'), 'form-control-success': fields.like_count && fields.like_count.valid}" id="like_count" name="like_count" placeholder="{{ trans('admin.chat-question.columns.like_count') }}">
        <div v-if="errors.has('like_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('like_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dislike_count'), 'has-success': fields.dislike_count && fields.dislike_count.valid }">
    <label for="dislike_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.dislike_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dislike_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dislike_count'), 'form-control-success': fields.dislike_count && fields.dislike_count.valid}" id="dislike_count" name="dislike_count" placeholder="{{ trans('admin.chat-question.columns.dislike_count') }}">
        <div v-if="errors.has('dislike_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dislike_count') }}</div>
    </div>
</div> --}}

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('question{{ $locale }}'), 'has-success': fields.question{{ $locale }} && fields.question{{ $locale }}.valid }">
                <label for="question{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.question') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.question.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('question{{ $locale }}'), 'form-control-success': fields.question{{ $locale }} && fields.question{{ $locale }}.valid }" id="question{{ $locale }}" name="question{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.question') }}">
                    <div v-if="errors.has('question{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('question{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('answer{{ $locale }}'), 'has-success': fields.answer{{ $locale }} && fields.answer{{ $locale }}.valid }">
                <label for="answer{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.answer') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                   {{-- <input type="text" v-model="form.answer.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('answer{{ $locale }}'), 'form-control-success': fields.answer{{ $locale }} && fields.answer{{ $locale }}.valid }" id="answer{{ $locale }}" name="answer{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.answer') }}"> --}}
                    {{--<wysiwyg v-model="form.answer.{{ $locale }}" v-validate="''" id="answer{{ $locale }}" name="answer{{ $locale }}" :config="mediaWysiwygConfig"></wysiwyg>--}}
                    <ckeditor  type="classic" id="description" v-model="form.answer.{{ $locale }}" @input="$emit('input', $event);" :config="editorConfig"></ckeditor>
                        
                    <div v-if="errors.has('answer{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('answer{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('keyword_id'), 'has-success': fields.keyword_id && fields.keyword_id.valid }">
    <label for="keyword_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.keyword_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect 
        :searchable="true"
        v-model="form.keyword_id"
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_keywords.map(type => type.id)" 
        :custom-label="opt => form.all_keywords.find(x => x.id == opt).title"
        open-direction="auto" 
        :close-on-select="false"
        :multiple="true">
            <template slot="selection" slot-scope="{ keyword_id, search, isOpen }"><span class="multiselect__single" v-if="form.keyword_id.length &amp;&amp; !isOpen">@{{ form.keyword_id.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('keyword_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('keyword_id') }}</div>
    </div>
</div>

{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('hit'), 'has-success': fields.hit && fields.hit.valid }">
    <label for="hit" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.hit') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.hit" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('hit'), 'form-control-success': fields.hit && fields.hit.valid}" id="hit" name="hit" placeholder="{{ trans('admin.chat-question.columns.hit') }}">
        <div v-if="errors.has('hit')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('hit') }}</div>
    </div>
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
    <label for="cadre_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.cadre_id') }}</label>
     <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
        <multiselect 
        :searchable="true"
        v-model="form.cadre_id" 
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_cadres.map(type => type.id)" 
        :custom-label="opt => form.all_cadres.find(x => x.id == opt).title"
        open-direction="auto" 
        :close-on-select="false"
        :multiple="true">
            <template slot="selection" slot-scope="{ cadre_id, search, isOpen }"><span class="multiselect__single" v-if="form.cadre_id.length &amp;&amp; !isOpen">@{{ form.cadre_id.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
    <button type="button" class="btn btn-primary" v-on:click="selectAll">
        <span v-if="form.all_cadres.length == form.cadre_id.length">Clear</span>
        <span v-else>All</span>
    </button>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('category'), 'has-success': fields.category && fields.category.valid }">
    <label for="category" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-question.columns.category') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.category" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('category'), 'form-control-success': fields.category && fields.category.valid}" id="category" name="category" placeholder="{{ trans('admin.chat-question.columns.category') }}"> --}}
        <select class="form-control" v-model="form.category" v-validate="''" @input="validate($event)" :class="{'form-control-danger': errors.has('category'), 'form-control-success': fields.category && fields.category.valid}" id="category" name="category" placeholder="{{ trans('admin.chat-question.columns.category') }}">
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
      </select>
        <div v-if="errors.has('category')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('category') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('activated'), 'has-success': fields.activated && fields.activated.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="activated" type="checkbox" v-model="form.activated" v-validate="''" data-vv-name="activated"  name="activated_fake_element">
        <label class="form-check-label" for="activated">
            {{ trans('admin.chat-question.columns.activated') }}
        </label>
        <input type="hidden" name="activated" :value="form.activated">
        <div v-if="errors.has('activated')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('activated') }}</div>
    </div>
</div>
