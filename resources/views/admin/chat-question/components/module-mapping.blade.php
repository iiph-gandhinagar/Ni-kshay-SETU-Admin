<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('modules_{{ $locale }}'), 'has-success': fields.modules_{{ $locale }} && fields.modules_{{ $locale }}.valid }">
                <label for="modules_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.modules') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    
                        <multiselect 
                        :searchable="true"
                        v-model="form.modules.{{ $locale }}" 
                        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
                        @if($locale == 'en')
                            :options="['Diagnoses Algortihm','Case Definition','Treatment Algorithm','Guidance on ADR','Latent Tb Infection','Differential Care Alogrithm']"
                        @elseif ($locale == "hi")
                            :options="['निदान एल्गोरिदम','केस परिभाषा','उपचार एल्गोरिदम','एडीआर . पर मार्गदर्शन','गुप्त टीबी संक्रमण','डिफरेंशियल केयर एल्गोरिथम']"
                        @else
                            :options="['નિદાન અલ્ગોરિધમનો','કેસની વ્યાખ્યા','સારવાર અલ્ગોરિધમનો','ADR અંગે માર્ગદર્શન','સુપ્ત ટીબી ચેપ','વિભેદક સંભાળ અલ્ગોરિધમ']"
                        @endif
                        open-direction="bottom" 
                        :multiple="false"
                        @input="getSubModules(form.modules.{{ $locale }},'{{ $locale }}' )"
                        id="modules{{ $locale }}" name="modules{{ $locale }}"></multiselect>
                    <div v-if="errors.has('modules_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('modules_{{ $locale }}') }}</div>

                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_modules_{{ $locale }}'), 'has-success': fields.sub_modules_{{ $locale }} && fields.sub_modules_{{ $locale }}.valid }">
                <label for="sub_modules_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-question.columns.sub_modules') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <multiselect 
                    :searchable="true"
                    track-by="id"
                    v-model="form.sub_modules.{{ $locale }}" 
                    placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
                    :options="form.sub_modules_list"
                    label="title"
                    open-direction="bottom"
                    :multiple="false"
                    id="sub_modules{{ $locale }}" name="sub_modules{{ $locale }}"></multiselect>
                    <div v-if="errors.has('sub_modules_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('sub_modules_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>