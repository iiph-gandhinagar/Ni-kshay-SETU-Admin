<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question1_{{ $locale }}'), 'has-success': fields.training_question1_{{ $locale }} && fields.training_question1_{{ $locale }}.valid }">
                <label for="training_question1_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question1') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question1.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question1_{{ $locale }}'), 'form-control-success': fields.training_question1_{{ $locale }} && fields.training_question1_{{ $locale }}.valid }" id="training_question1_{{ $locale }}" name="training_question1_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question1') }}">
                    <div v-if="errors.has('training_question1_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question1_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question2_{{ $locale }}'), 'has-success': fields.training_question2_{{ $locale }} && fields.training_question2_{{ $locale }}.valid }">
                <label for="training_question2_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question2') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question2.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question2_{{ $locale }}'), 'form-control-success': fields.training_question2_{{ $locale }} && fields.training_question2_{{ $locale }}.valid }" id="training_question2_{{ $locale }}" name="training_question2_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question2') }}">
                    <div v-if="errors.has('training_question2_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question2_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question3_{{ $locale }}'), 'has-success': fields.training_question3_{{ $locale }} && fields.training_question3_{{ $locale }}.valid }">
                <label for="training_question3_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question3') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question3.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question3_{{ $locale }}'), 'form-control-success': fields.training_question3_{{ $locale }} && fields.training_question3_{{ $locale }}.valid }" id="training_question3_{{ $locale }}" name="training_question3_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question3') }}">
                    <div v-if="errors.has('training_question3_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question3_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question4_{{ $locale }}'), 'has-success': fields.training_question4_{{ $locale }} && fields.training_question4_{{ $locale }}.valid }">
                <label for="training_question4_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question4') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question4.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question4_{{ $locale }}'), 'form-control-success': fields.training_question4_{{ $locale }} && fields.training_question4_{{ $locale }}.valid }" id="training_question4_{{ $locale }}" name="training_question4_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question4') }}">
                    <div v-if="errors.has('training_question4_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question4_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question5_{{ $locale }}'), 'has-success': fields.training_question5_{{ $locale }} && fields.training_question5_{{ $locale }}.valid }">
                <label for="training_question5_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question5') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question5.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question5_{{ $locale }}'), 'form-control-success': fields.training_question5_{{ $locale }} && fields.training_question5_{{ $locale }}.valid }" id="training_question5_{{ $locale }}" name="training_question5_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question5') }}">
                    <div v-if="errors.has('training_question5_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question5_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>


<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question6_{{ $locale }}'), 'has-success': fields.training_question6_{{ $locale }} && fields.training_question6_{{ $locale }}.valid }">
                <label for="training_question6_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question6') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question6.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question6_{{ $locale }}'), 'form-control-success': fields.training_question6_{{ $locale }} && fields.training_question6_{{ $locale }}.valid }" id="training_question6_{{ $locale }}" name="training_question6_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question6') }}">
                    <div v-if="errors.has('training_question6_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question6_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question7_{{ $locale }}'), 'has-success': fields.training_question7_{{ $locale }} && fields.training_question7_{{ $locale }}.valid }">
                <label for="training_question7_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question7') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question7.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question7_{{ $locale }}'), 'form-control-success': fields.training_question7_{{ $locale }} && fields.training_question7_{{ $locale }}.valid }" id="training_question7_{{ $locale }}" name="training_question7_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question7') }}">
                    <div v-if="errors.has('training_question7_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question7_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question8_{{ $locale }}'), 'has-success': fields.training_question8_{{ $locale }} && fields.training_question8_{{ $locale }}.valid }">
                <label for="training_question8_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question8') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question8.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question8_{{ $locale }}'), 'form-control-success': fields.training_question8_{{ $locale }} && fields.training_question8_{{ $locale }}.valid }" id="training_question8_{{ $locale }}" name="training_question8_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question8') }}">
                    <div v-if="errors.has('training_question8_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question8_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question9_{{ $locale }}'), 'has-success': fields.training_question9_{{ $locale }} && fields.training_question9_{{ $locale }}.valid }">
                <label for="training_question9_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question9') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question9.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question9_{{ $locale }}'), 'form-control-success': fields.training_question9_{{ $locale }} && fields.training_question9_{{ $locale }}.valid }" id="training_question9_{{ $locale }}" name="training_question9_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question9') }}">
                    <div v-if="errors.has('training_question9_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question9_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('training_question10_{{ $locale }}'), 'has-success': fields.training_question10_{{ $locale }} && fields.training_question10_{{ $locale }}.valid }">
                <label for="training_question10_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.training_question10') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.training_question10.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('training_question10_{{ $locale }}'), 'form-control-success': fields.training_question10_{{ $locale }} && fields.training_question10_{{ $locale }}.valid }" id="training_question10_{{ $locale }}" name="training_question10_{{ $locale }}" placeholder="{{ trans('admin.chat-question.columns.training_question10') }}">
                    <div v-if="errors.has('training_question10_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('training_question10_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

