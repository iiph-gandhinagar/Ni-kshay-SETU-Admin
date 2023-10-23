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

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('node_type'), 'has-success': fields.node_type && fields.node_type.valid }">
    <label for="node_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.node_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.node_type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('node_type'), 'form-control-success': fields.node_type && fields.node_type.valid}" id="node_type" name="node_type" placeholder="{{ trans('admin.dynamic-algorithm.columns.node_type') }}"> --}}
        <select class="form-control" v-model="form.node_type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('node_type'), 'form-control-success': fields.node_type && fields.node_type.valid}" id="node_type" name="node_type" placeholder="{{ trans('admin.dynamic-algorithm.columns.node_type') }}">
            <option value="">Select Node Type</option>                              
            <option value="Linking Node">Linking Node</option>
            <option value="CMS Node">CMS Node</option>
            <option value="CMS Node(New Page)">CMS Node(New Page)</option>
            <option value="App Screen Node">App Screen Node</option>
            <option value="Linking Node Without Options">Linking Node Without Options</option>
      </select>
        <div v-if="errors.has('node_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('node_type') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_expandable'), 'has-success': fields.is_expandable && fields.is_expandable.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_expandable" type="checkbox" v-model="form.is_expandable" v-validate="''" data-vv-name="is_expandable"  name="is_expandable_fake_element">
        <label class="form-check-label" for="is_expandable">
            {{ trans('admin.dynamic-algorithm.columns.is_expandable') }}
        </label>
        <input type="hidden" name="is_expandable" :value="form.is_expandable">
        <div v-if="errors.has('is_expandable')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_expandable') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('has_options'), 'has-success': fields.has_options && fields.has_options.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="has_options" type="checkbox" v-model="form.has_options" v-validate="''" data-vv-name="has_options"  name="has_options_fake_element">
        <label class="form-check-label" for="has_options">
            {{ trans('admin.dynamic-algorithm.columns.has_options') }}
        </label>
        <input type="hidden" name="has_options" :value="form.has_options">
        <div v-if="errors.has('has_options')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('has_options') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('parent_id'), 'has-success': fields.parent_id && fields.parent_id.valid }">
    <label for="parent_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.parent_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input readonly type="text" v-model="form.parent_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('parent_id'), 'form-control-success': fields.parent_id && fields.parent_id.valid}" id="parent_id" name="parent_id" placeholder="{{ trans('admin.dynamic-algorithm.columns.parent_id') }}">
        <div v-if="errors.has('parent_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('algo_key'), 'has-success': fields.algo_key && fields.algo_key.valid }">
    <label for="algo_key" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.algo_key') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input readonly type="text" v-model="form.algo_key" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('algo_key'), 'form-control-success': fields.algo_key && fields.algo_key.valid}" id="algo_key" name="algo_key" placeholder="{{ trans('admin.dynamic-algorithm.columns.algo_key') }}">
        <div v-if="errors.has('algo_key')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('algo_key') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('index'), 'has-success': fields.index && fields.index.valid }">
    <label for="index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('index'), 'form-control-success': fields.index && fields.index.valid}" id="index" name="index" placeholder="{{ trans('admin.dynamic-algorithm.columns.index') }}">
        <div v-if="errors.has('index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('index') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('redirect_algo_type'), 'has-success': fields.redirect_algo_type && fields.redirect_algo_type.valid }">
    <label for="redirect_algo_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.redirect_algo_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <select class="form-control" onchange="getMasterNodes()" v-model="form.redirect_algo_type" @input="validate($event)" :class="{'form-control-danger': errors.has('redirect_algo_type'), 'form-control-success': fields.redirect_algo_type && fields.redirect_algo_type.valid}" id="redirect_algo_type" name="redirect_algo_type" placeholder="{{ trans('admin.dynamic-algorithm.columns.redirect_algo_type') }}">
                <option value="">Select Algorithm Type</option>
                <option value="Case Definition">Case Definition</option>
                <option value="Diagnosis Algorithm">Diagnosis Algorithm</option>
                <option value="Guidance on ADR">Guidance on ADR</option>
                <option value="Latent TB Infection">Latent TB Infection</option>
                <option value="Treatment Algorithm">Treatment Algorithm</option>

          </select>
        <div v-if="errors.has('redirect_algo_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('redirect_algo_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('redirect_node_id'), 'has-success': fields.redirect_node_id && fields.redirect_node_id.valid }">
    <label for="redirect_node_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.redirect_node_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <select class="form-control" v-model="form.redirect_node_id" @input="validate($event)" :class="{'form-control-danger': errors.has('redirect_node_id'), 'form-control-success': fields.redirect_node_id && fields.redirect_node_id.valid}" id="redirect_node_id" name="redirect_node_id" placeholder="{{ trans('admin.dynamic-algorithm.columns.redirect_node_id') }}">
                <option value="">Select Redirect Node</option>
          </select>
        <div v-if="errors.has('redirect_node_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('redirect_node_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" >
    <label for="node_icon" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.node_icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('node_icon') }}</div>
        @if(isset($dynamicAlgorithm))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\DynamicAlgorithm::class)->getMediaCollection('node_icon'),
                'media' => $dynamicAlgorithm->getThumbs200ForCollection('node_icon'),
                'Label' => "node_icon"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\DynamicAlgorithm::class)->getMediaCollection('node_icon'),
                'Label' => "node_icon"
            ])
        @endif

    </div>
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title_{{ $locale }}'), 'has-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }">
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.dynamic-algorithm.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.dynamic-algorithm.columns.title') }}">
                    <div v-if="errors.has('title_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('description_{{ $locale }}'), 'has-success': fields.description_{{ $locale }} && fields.description_{{ $locale }}.valid }">
                <label for="description_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.dynamic-algorithm.columns.description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <div>
                        {{--<wysiwyg v-model="form.description.{{ $locale }}" v-validate="''" id="description_{{ $locale }}" name="description_{{ $locale }}" :config="mediaWysiwygConfig"></wysiwyg>--}}
                        <ckeditor  type="classic" id="description" v-model="form.description.{{ $locale }}" @input="$emit('input', $event);" :config="editorConfig"></ckeditor>
                    </div>
                    <div v-if="errors.has('description_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('header{{ $locale }}'), 'has-success': fields.header{{ $locale }} && fields.header{{ $locale }}.valid }">
                <label for="header{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.dynamic-algorithm.columns.header') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.header.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('header{{ $locale }}'), 'form-control-success': fields.header{{ $locale }} && fields.header{{ $locale }}.valid }" id="header{{ $locale }}" name="header{{ $locale }}" placeholder="{{ trans('admin.dynamic-algorithm.columns.header') }}">
                    <div v-if="errors.has('header{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('header{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_header{{ $locale }}'), 'has-success': fields.sub_header{{ $locale }} && fields.sub_header{{ $locale }}.valid }">
                <label for="sub_header{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.dynamic-algorithm.columns.sub_header') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.sub_header.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_header{{ $locale }}'), 'form-control-success': fields.sub_header{{ $locale }} && fields.sub_header{{ $locale }}.valid }" id="sub_header{{ $locale }}" name="sub_header{{ $locale }}" placeholder="{{ trans('admin.dynamic-algorithm.columns.sub_header') }}">
                    <div v-if="errors.has('sub_header{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('sub_header{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('master_node_id'), 'has-success': fields.master_node_id && fields.master_node_id.valid }">
    <label for="master_node_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.dynamic-algorithm.columns.master_node_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.master_node_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('master_node_id'), 'form-control-success': fields.master_node_id && fields.master_node_id.valid}" id="master_node_id" name="master_node_id" placeholder="{{ trans('admin.dynamic-algorithm.columns.master_node_id') }}">
        <div v-if="errors.has('master_node_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('master_node_id') }}</div>
    </div>
</div>

<div v-if ="form.parent_id == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('state_id'), 'has-success': fields.state_id && fields.state_id.valid }">
    <label for="state_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.diagnoses-algorithm.columns.state_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
            <multiselect 
            :searchable="true"
            v-model="form.state_id" 
            placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
            :options="form.all_states.map(type => type.id)" 
            :custom-label="opt => form.all_states.find(x => x.id == opt).title"
            open-direction="auto" 
            :close-on-select="false"
            :multiple="true">
                <template slot="selection" slot-scope="{ state_id, search, isOpen }"><span class="multiselect__single" v-if="form.state_id.length &amp;&amp; !isOpen">@{{ form.state_id.length }} options selected</span></template>
            </multiselect>
            <div v-if="errors.has('state_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state_id') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAllStates">
            <span v-if="form.all_states.length == form.state_id.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>
<div v-if ="form.parent_id == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre_id'), 'has-success': fields.cadre_id && fields.cadre_id.valid }">
    <label for="cadre_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.diagnoses-algorithm.columns.cadre_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect 
        :searchable="true"
        v-model="form.cadre_id" 
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_cadres.map(type => type.id)" 
        :custom-label="opt => form.all_cadres.find(x => x.id == opt).title"
        open-direction="auto" 
        :close-on-select="false"
        :multiple="true">
            <template slot="selection" slot-scope="{ cadre_id, search, isOpen }"><span class="multiselect__single" v-if="form.cadre_id.length &amp;&amp; !isOpen">@{{ form.cadre_id.length }} options selected</span></template></multiselect>
        <div v-if="errors.has('cadre_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre_id') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAll">
            <span v-if="form.all_cadres.length == form.cadre_id.length">Clear</span>
            <span v-else>All</span>
        </button>
        </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('activated'), 'has-success': fields.activated && fields.activated.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="activated" type="checkbox" v-model="form.activated" v-validate="''" data-vv-name="activated"  name="activated_fake_element">
        <label class="form-check-label" for="activated">
            {{ trans('admin.dynamic-algorithm.columns.activated') }}
        </label>
        <input type="hidden" name="activated" :value="form.activated">
        <div v-if="errors.has('activated')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('activated') }}</div>
    </div>
</div>

@section('bottom-scripts')
    @include('admin.script-element-for-master-node')
@endsection


