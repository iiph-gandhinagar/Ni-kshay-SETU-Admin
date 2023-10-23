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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_question_{{ $locale }}'), 'has-success': fields.feedback_question_{{ $locale }} && fields.feedback_question_{{ $locale }}.valid }">
                <label for="feedback_question_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.user-feedback-question.columns.feedback_question') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.feedback_question.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_question_{{ $locale }}'), 'form-control-success': fields.feedback_question_{{ $locale }} && fields.feedback_question_{{ $locale }}.valid }" id="feedback_question_{{ $locale }}" name="feedback_question_{{ $locale }}" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_question') }}">
                    <div v-if="errors.has('feedback_question_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('feedback_question_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_description_{{ $locale }}'), 'has-success': fields.feedback_description_{{ $locale }} && fields.feedback_description_{{ $locale }}.valid }">
                <label for="feedback_description_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.user-feedback-question.columns.feedback_description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    {{--<input type="text" v-model="form.feedback_description.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_description_{{ $locale }}'), 'form-control-success': fields.feedback_description_{{ $locale }} && fields.feedback_description_{{ $locale }}.valid }" id="feedback_description_{{ $locale }}" name="feedback_description_{{ $locale }}" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_description') }}"> --}}
                    <textarea type="text" v-model="form.feedback_description.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_description_{{ $locale }}'), 'form-control-success': fields.feedback_description_{{ $locale }} && fields.feedback_description_{{ $locale }}.valid}" id="feedback_description_{{ $locale }}" name="feedback_description_{{ $locale }}" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_description') }}"></textarea>
                    <div v-if="errors.has('feedback_description_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('feedback_description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_type'), 'has-success': fields.feedback_type && fields.feedback_type.valid }">
    <label for="feedback_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-question.columns.feedback_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.feedback_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_type'), 'form-control-success': fields.feedback_type && fields.feedback_type.valid}" id="feedback_type" name="feedback_type" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_type') }}">--}}
            <select class="form-control" v-model="form.feedback_type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('feedback_type'), 'form-control-success': fields.feedback_type && fields.feedback_type.valid}" id="feedback_type" name="feedback_type" placeholder="{{ trans('admin.case-definition.columns.feedback_type') }}">
                <option value="">Select Feedback Type</option>                              
                <option value="repeat">Repeat</option>
                <option value="no_repeat">No Repeat</option>

            </select>
        <div v-if="errors.has('feedback_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_type') }}</div>
    </div>
</div>

<div v-if="form.feedback_type == 'no_repeat'" class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_value'), 'has-success': fields.feedback_value && fields.feedback_value.valid }">
    <label for="feedback_value" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-question.columns.feedback_value') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.feedback_value" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_value'), 'form-control-success': fields.feedback_value && fields.feedback_value.valid}" id="feedback_value" name="feedback_value" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_value') }}">
        <div v-if="errors.has('feedback_value')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_value') }}</div>
    </div>
</div>

<div v-if="form.feedback_type == 'no_repeat'" class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_time'), 'has-success': fields.feedback_time && fields.feedback_time.valid }">
    <label for="feedback_time" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-question.columns.feedback_time') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.feedback_time" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_time'), 'form-control-success': fields.feedback_time && fields.feedback_time.valid}" id="feedback_time" name="feedback_time" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_time') }}">
        <div v-if="errors.has('feedback_time')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_time') }}</div>
    </div>
</div>

<div v-if="form.feedback_type == 'repeat'" class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_days'), 'has-success': fields.feedback_days && fields.feedback_days.valid }">
    <label for="feedback_days" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-question.columns.feedback_days') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.feedback_days" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_days'), 'form-control-success': fields.feedback_days && fields.feedback_days.valid}" id="feedback_days" name="feedback_days" placeholder="{{ trans('admin.user-feedback-question.columns.feedback_days') }}">
        <div v-if="errors.has('feedback_days')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_days') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_question_icon'), 'has-success': fields.feedback_question_icon && fields.feedback_question_icon.valid }">
    <label for="feedback_question_icon" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-question.columns.feedback_question_icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($userFeedbackQuestion))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\UserFeedbackQuestion::class)->getMediaCollection('feedback_question_icon'),
                'media' => $userFeedbackQuestion->getThumbs200ForCollection('feedback_question_icon'),
                'Label' => "feedback_question_icon"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\UserFeedbackQuestion::class)->getMediaCollection('feedback_question_icon'),
                'Label' => "feedback_question_icon",
            ])
        @endif
        <div v-if="errors.has('feedback_question_icon')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_question_icon') }}</div>

    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_active'), 'has-success': fields.is_active && fields.is_active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_active" type="checkbox" v-model="form.is_active" v-validate="''" data-vv-name="is_active"  name="is_active_fake_element">
        <label class="form-check-label" for="is_active">
            {{ trans('admin.user-feedback-question.columns.is_active') }}
        </label>
        <input type="hidden" name="is_active" :value="form.is_active">
        <div v-if="errors.has('is_active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_active') }}</div>
    </div>
</div>


