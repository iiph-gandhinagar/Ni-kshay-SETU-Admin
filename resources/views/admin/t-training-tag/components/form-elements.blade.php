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

{{-- <div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('response_json_{{ $locale }}'), 'has-success': fields.response_json_{{ $locale }} && fields.response_json_{{ $locale }}.valid }">
                <label for="response_json_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.t-training-tag.columns.response_json') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.response_json.{{ $locale }}" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('response_json_{{ $locale }}'), 'form-control-success': fields.response_json_{{ $locale }} && fields.response_json_{{ $locale }}.valid }" id="response_json_{{ $locale }}" name="response_json_{{ $locale }}" placeholder="{{ trans('admin.t-training-tag.columns.response_json') }}">
                    <div v-if="errors.has('response_json_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('response_json_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div> --}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('tag'), 'has-success': fields.tag && fields.tag.valid }">
    <label for="tag" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.tag') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.tag" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('tag'), 'form-control-success': fields.tag && fields.tag.valid}" id="tag" name="tag" placeholder="{{ trans('admin.t-training-tag.columns.tag') }}">
        <div v-if="errors.has('tag')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('tag') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('pattern'), 'has-success': fields.pattern && fields.pattern.valid }">
    <label for="pattern" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.pattern') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.pattern" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="options" :multiple="true" :taggable="true"  @tag="addTag" open-direction="bottom"  :close-on-select="false"></multiselect>
        <div v-if="errors.has('pattern')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('pattern') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('is_fix_response'), 'has-success': fields.is_fix_response && fields.is_fix_response.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="is_fix_response" type="checkbox" v-model="form.is_fix_response" v-validate="''" data-vv-name="is_fix_response"  name="is_fix_response_fake_element">
        <label class="form-check-label" for="is_fix_response">
            {{ trans('admin.t-training-tag.columns.is_fix_response') }}
        </label>
        <input type="hidden" name="is_fix_response" :value="form.is_fix_response">
        <div v-if="errors.has('is_fix_response')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('is_fix_response') }}</div>
    </div>
</div>

{{--<div class="form-group row align-items-center" :class="{'has-danger': errors.has('like_count'), 'has-success': fields.like_count && fields.like_count.valid }">
    <label for="like_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.like_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.like_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('like_count'), 'form-control-success': fields.like_count && fields.like_count.valid}" id="like_count" name="like_count" placeholder="{{ trans('admin.t-training-tag.columns.like_count') }}">
        <div v-if="errors.has('like_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('like_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('dislike_count'), 'has-success': fields.dislike_count && fields.dislike_count.valid }">
    <label for="dislike_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.dislike_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.dislike_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('dislike_count'), 'form-control-success': fields.dislike_count && fields.dislike_count.valid}" id="dislike_count" name="dislike_count" placeholder="{{ trans('admin.t-training-tag.columns.dislike_count') }}">
        <div v-if="errors.has('dislike_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('dislike_count') }}</div>
    </div>
</div> --}}

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div v-if="form.is_fix_response != '' && form.is_fix_response != 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('response_{{ $locale }}'), 'has-success': fields.response_{{ $locale }} && fields.response_{{ $locale }}.valid }">
                <label for="response{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.t-training-tag.columns.response') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <multiselect v-model="form.response.{{ $locale }}" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="response_options" :multiple="true" :taggable="true"  @tag="addResponseTag" open-direction="bottom"  :close-on-select="false"></multiselect>
                    <div v-if="errors.has('response_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('response_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div v-if="form.is_fix_response == '' || form.is_fix_response == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('questions'), 'has-success': fields.questions && fields.questions.valid }">
    <label for="questions" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.questions') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.questions" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="question" track-by="id" :options="{{ $questions->toJson() }}" :multiple="true" open-direction="bottom" :close-on-select="false">
        <template slot="selection" slot-scope="{ questions, search, isOpen }"><span class="multiselect__single" v-if="form.questions.length > 0 && form.questions.length &amp;&amp; !isOpen">@{{ form.questions.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('questions')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('questions') }}</div>
        <draggable v-model="form.draggable_questions" class="list-group" :move="checkMove" @start="dragging = true" @end="dragging = false" name="draggable" :list="form.questions" style="overflow: scroll;max-height: 400px;">
            <div 
          class="list-group-item"
          v-for="element in form.questions"
          :key="element.id" :value="element.id"
        >
          @{{ element.question }}
        </div>
    </draggable>
    </div>
</div>

<div v-if="form.is_fix_response == '' || form.is_fix_response == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('modules'), 'has-success': fields.modules && fields.modules.valid }">
    <label for="modules" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.modules') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.modules" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :options="{{ $modules->toJson() }}" :multiple="true" @input="getSubModules" open-direction="bottom" :close-on-select="false">
            <template slot="selection" slot-scope="{ modules, search, isOpen }"><span class="multiselect__single" v-if="form.modules.length &amp;&amp; !isOpen">@{{ form.modules.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('modules')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('modules') }}</div>
    </div>
</div>

<div v-if="form.is_fix_response == '' || form.is_fix_response == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_modules'), 'has-success': fields.sub_modules && fields.sub_modules.valid }">
    <label for="sub_modules" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.sub_modules') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.sub_modules" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :options="form.sub_module_options" :multiple="true" open-direction="bottom" :close-on-select="false"></multiselect>
        <div v-if="errors.has('sub_modules')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_modules') }}</div>
    </div>
</div>

<div v-if="form.is_fix_response == '' || form.is_fix_response == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('resource_material'), 'has-success': fields.resource_material && fields.resource_material.valid }">
    <label for="resource_material" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.t-training-tag.columns.resource_material') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.resource_material" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" :options="{{ $resourceMaterials->toJson() }}" :multiple="true" open-direction="bottom" :close-on-select="false">
            <template slot="selection" slot-scope="{ resource_material, search, isOpen }"><span class="multiselect__single" v-if="form.resource_material.length > 0 && form.resource_material.length &amp;&amp; !isOpen">@{{ form.resource_material.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('resource_material')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('resource_material') }}</div>
        <draggable v-model="form.draggable_resource_material" class="list-group" :move="checkMove" @start="dragging = true" @end="dragging = false" name="draggable" :list="form.resource_material" style="overflow: scroll;max-height: 400px;">
            <div 
          class="list-group-item"
          v-for="element in form.resource_material"
          :key="element.id" :value="element.id"
        >
          @{{ element.title }}
        </div>
    </draggable>
    </div>
</div>


