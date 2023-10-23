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
                <label for="title{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.resource-material.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title{{ $locale }}'), 'form-control-success': fields.title{{ $locale }} && fields.title{{ $locale }}.valid }" id="title{{ $locale }}" name="title{{ $locale }}" placeholder="{{ trans('admin.resource-material.columns.title') }}">
                    <div v-if="errors.has('title{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($user_state == '')
    <div class="form-group row align-items-center"
        :class="{'has-danger': errors.has('country_id'), 'has-success': fields.country && fields.country.valid }">
        <label for="country_id" class="col-form-label text-md-right"
            :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.country_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-3' : 'col-md-8 col-xl-7'">
            <multiselect v-model="form.country_id"
                placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title"
                track-by="id" :options="{{ $country->toJson() }}" :multiple="false" :close-on-select="false"
                :searchable="true" open-direction="auto"></multiselect>
            <div v-if="errors.has('country_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('country_id') }}
            </div>
        </div>
    </div>
@endif

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('state'), 'has-success': fields.state && fields.state.valid }">
    <label for="state" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.state') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.state" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('state'), 'form-control-success': fields.state && fields.state.valid}" id="state" name="state" placeholder="{{ trans('admin.resource-material.columns.state') }}"> --}}
        {{-- <select class="form-control" v-model="form.state" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('state'), 'form-control-success': fields.state && fields.state.valid}" id="state" name="state" placeholder="{{ trans('admin.resource-material.columns.state') }}">
            <option value="">Select State</option> 
            @foreach ($state as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}
      <multiselect 
        :searchable="true"
        v-model="form.state" 
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_states.map(type => type.id)" 
        :custom-label="opt => form.all_states.find(x => x.id == opt).title"
        open-direction="auto" 
        :close-on-select="false"
        :multiple="true">
            <template slot="selection" slot-scope="{ state, search, isOpen }"><span class="multiselect__single" v-if="form.state.length &amp;&amp; !isOpen">@{{ form.state.length }} options selected</span></template>
        </multiselect>
        <div v-if="errors.has('state')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('state') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAllStates">
            <span v-if="form.all_states.length == form.state.length">Clear</span>
            <span v-else>All</span>
        </button>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('cadre'), 'has-success': fields.cadre && fields.cadre.valid }">
    <label for="cadre" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.cadre') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.cadre" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('cadre'), 'form-control-success': fields.cadre && fields.cadre.valid}" id="cadre" name="cadre" placeholder="{{ trans('admin.resource-material.columns.cadre') }}"> --}}
        {{-- <select class="form-control" v-model="form.cadre" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('cadre'), 'form-control-success': fields.cadre && fields.cadre.valid}" id="cadre" name="cadre" placeholder="{{ trans('admin.resource-material.columns.cadre') }}">
            <option value="">Select Cadre</option> 
            @foreach ($cadre as $item)
                <option value="{{ $item->id }}" >{{ $item->title }}</option>  
            @endforeach    
      </select> --}}
      <multiselect 
        :searchable="true"
        v-model="form.cadre" 
        placeholder="{{ trans('brackets/admin-ui::admin.forms.select_an_option') }}" 
        :options="form.all_cadres.map(type => type.id)" 
        :custom-label="opt => form.all_cadres.find(x => x.id == opt).title"
        open-direction="auto" 
        :close-on-select="false"
        :multiple="true">
            <template slot="selection" slot-scope="{ cadre, search, isOpen }"><span class="multiselect__single" v-if="form.cadre.length &amp;&amp; !isOpen">@{{ form.cadre.length }} options selected</span></template></multiselect>
        <div v-if="errors.has('cadre')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('cadre') }}</div>
    </div>
    <div :class="isFormLocalized ? 'col-md-1' : 'col-md-1 col-xl-1'">
        <button type="button" class="btn btn-primary" v-on:click="selectAll">
            <span v-if="form.all_cadres.length == form.cadre.length">Clear</span>
            <span v-else>All</span>
        </button>
        </div>
</div>

{{-- <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.resource-material.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>--}}

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('parent_id'), 'has-success': fields.parent_id && fields.parent_id.valid }">
    <label for="parent_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.parent_id') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @include('admin.resource-material.components.parent-list')
        {{-- <input type="text" v-model="form.parent_id" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('parent_id'), 'form-control-success': fields.parent_id && fields.parent_id.valid}" id="parent_id" name="parent_id" placeholder="{{ trans('admin.resource-material.columns.parent_id') }}"> --}}
        <div v-if="errors.has('parent_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('parent_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type_of_materials'), 'has-success': fields.type_of_materials && fields.type_of_materials.valid }">
    <label for="type_of_materials" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.type_of_materials') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <div>
            <textarea class="form-control" v-model="form.type_of_materials" v-validate="'required'" id="type_of_materials" name="type_of_materials"></textarea>
        </div> --}}
        <select class="form-control" v-model="form.type_of_materials" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('type_of_materials'), 'form-control-success': fields.type_of_materials && fields.type_of_materials.valid}" id="type_of_materials" name="type_of_materials" placeholder="{{ trans('admin.assessment-question.columns.type_of_materials') }}">
          <option value="">Select Options</option>
          <option v-if="{{ \Auth::user()->roles[0]['id'] }} != 3" value="folder">Folder</option>
          <option value="pdfs">PDF</option>
          <option value="videos">Videos</option>
          <option value="ppt">Presentation</option>
          <option value="document">Document</option>
          <option value="images">Images</option>
          <option value="pdf_office_orders">Pdf Office Orders</option>
      </select>
        <div v-if="errors.has('type_of_materials')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type_of_materials') }}</div>
    </div>
</div>

<div v-if="form.type_of_materials != '' && form.type_of_materials != 'folder'" class="form-group row align-items-center" :class="{'has-danger': errors.has('material'), 'has-success': fields.material && fields.material.valid }">
    <label for="material" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.material') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($resourceMaterial))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\ResourceMaterial::class)->getMediaCollection('material'),
                'media' => $resourceMaterial->getThumbs200ForCollection('material'),
                'Label' => "material"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\ResourceMaterial::class)->getMediaCollection('material'),
                'Label' => "material"
            ])
        @endif
        <div v-if="errors.has('material')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('material') }}</div>

    </div>
</div>

<div v-if="form.type_of_materials == 'videos'" class="form-group row align-items-center" :class="{'has-danger': errors.has('video_thumb'), 'has-success': fields.video_thumb && fields.video_thumb.valid }">
    <label for="video_thumb" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.video_thumb') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($resourceMaterial))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\ResourceMaterial::class)->getMediaCollection('video_thumb'),
                'media' => $resourceMaterial->getThumbs200ForCollection('video_thumb'),
                'Label' => "video_thumb"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\ResourceMaterial::class)->getMediaCollection('video_thumb'),
                'Label' => "video_thumb"
            ])
        @endif
        <div v-if="errors.has('video_thumb')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('video_thumb') }}</div>
    </div>
</div>

<div v-if="form.parent_id == 0" class="form-group row align-items-center" :class="{'has-danger': errors.has('icon_type'), 'has-success': fields.icon_type && fields.icon_type.valid }">
    <label for="icon_type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.icon_type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.icon_type" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('icon_type'), 'form-control-success': fields.icon_type && fields.icon_type.valid}" id="icon_type" name="icon_type" placeholder="{{ trans('admin.resource-material.columns.icon_type') }}"> --}}
        <select class="form-control" v-model="form.icon_type" @input="validate($event)" :class="{'form-control-danger': errors.has('icon_type'), 'form-control-success': fields.icon_type && fields.icon_type.valid}" id="icon_type" name="icon_type" placeholder="{{ trans('admin.assessment-question.columns.icon_type') }}">
            <option value="">Select Icon Type</option>
            <option value="NTEP">NTEP</option>
            <option value="folder">Folder</option>
            <option value="pdf">PDF</option>
            <option value="video">Video</option>
            <option value="ppt">Presentation</option>
            <option value="document">Document</option>
            <option value="image">Image</option>
        </select>
        <div v-if="errors.has('icon_type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('icon_type') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('index'), 'has-success': fields.index && fields.index.valid }">
    <label for="index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.resource-material.columns.index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.index" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('index'), 'form-control-success': fields.index && fields.index.valid}" id="index" name="index" placeholder="{{ trans('admin.resource-material.columns.index') }}">
        <div v-if="errors.has('index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('index') }}</div>
    </div>
</div>