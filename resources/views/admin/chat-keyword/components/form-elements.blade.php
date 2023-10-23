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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title{{ $locale }}'), 'has-success': fields.title{{ $locale }} && fields.title{{ $locale }}.valid }">
                <label for="title{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.chat-keyword.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title{{ $locale }}'), 'form-control-success': fields.title{{ $locale }} && fields.title{{ $locale }}.valid }" id="title{{ $locale }}" name="title{{ $locale }}" placeholder="{{ trans('admin.chat-keyword.columns.title') }}">
                    <div v-if="errors.has('title{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.chat-keyword.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>--}}

{{--<div class="form-group row align-items-center" :class="{'has-danger': errors.has('hit'), 'has-success': fields.hit && fields.hit.valid }">
    <label for="hit" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.hit') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.hit" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('hit'), 'form-control-success': fields.hit && fields.hit.valid}" id="hit" name="hit" placeholder="{{ trans('admin.chat-keyword.columns.hit') }}">
        <div v-if="errors.has('hit')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('hit') }}</div>
    </div>
</div>--}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('modules'), 'has-success': fields.modules && fields.modules.valid }">
    <label for="modules" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.modules') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.modules" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :options="{{ $modules->toJson() }}" :multiple="true" @input="getSubModules" open-direction="auto" :close-on-select="false">
        <template slot="selection" slot-scope="{ modules, search, isOpen }"><span class="multiselect__single" v-if="form.modules.length &amp;&amp; !isOpen">@{{ form.modules.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('modules')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('modules') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_modules'), 'has-success': fields.sub_modules && fields.sub_modules.valid }">
    <label for="sub_modules" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.sub_modules') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.sub_modules" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="name" track-by="id" :options="form.sub_module_options" :multiple="true" open-direction="auto" :close-on-select="false"></multiselect>
        <div v-if="errors.has('sub_modules')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_modules') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('resource_material'), 'has-success': fields.resource_material && fields.resource_material.valid }">
    <label for="resource_material" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.resource_material') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <multiselect v-model="form.resource_material" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" :options="{{ $resourceMaterials->toJson() }}" :multiple="true" open-direction="auto" :close-on-select="false">
        <template slot="selection" slot-scope="{ resource_material, search, isOpen }"><span class="multiselect__single" v-if="form.resource_material.length &amp;&amp; !isOpen">@{{ form.resource_material.length }} options selected</span></template></multiselect></multiselect>
        </multiselect>
        <div v-if="errors.has('resource_material')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('resource_material') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('custom_ordering'), 'has-success': fields.custom_ordering && fields.custom_ordering.valid }">
    <label for="custom_ordering" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.chat-keyword.columns.custom_ordering') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.custom_ordering" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('custom_ordering'), 'form-control-success': fields.custom_ordering && fields.custom_ordering.valid}" id="custom_ordering" name="custom_ordering" placeholder="{{ trans('admin.chat-keyword.columns.custom_ordering') }}">
        <div v-if="errors.has('custom_ordering')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('custom_ordering') }}</div>
    </div>
</div>


