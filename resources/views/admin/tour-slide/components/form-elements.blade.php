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
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('title_{{ $locale }}'), 'has-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }">
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.tour-slide.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.tour-slide.columns.title') }}">
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
                <label for="description_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.tour-slide.columns.description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <div>
                        {{--<wysiwyg v-model="form.description.{{ $locale }}" id="description_{{ $locale }}" name="description_{{ $locale }}" :config="mediaWysiwygConfig"></wysiwyg>--}}
                        <ckeditor  type="classic" id="description" v-model="form.description.{{ $locale }}" @input="$emit('input', $event);" :config="editorConfig"></ckeditor>
                    </div>
                    <div v-if="errors.has('description_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('tour_id'), 'has-success': fields.tour_id && fields.tour_id.valid }">
    <label for="tour_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.tour-slide.columns.tour_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.tour_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('tour_id'), 'form-control-success': fields.tour_id && fields.tour_id.valid}" id="tour_id" name="tour_id" placeholder="{{ trans('admin.tour-slide.columns.tour_id') }}"> --}}
        <multiselect v-model="form.tour_id" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="title" track-by="id" :options="{{ $tour->toJson() }}" :multiple="false" open-direction="auto"></multiselect>
        <div v-if="errors.has('tour_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('tour_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('type'), 'has-success': fields.type && fields.type.valid }">
    <label for="type" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.tour-slide.columns.type') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.type" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}" id="type" name="type" placeholder="{{ trans('admin.tour-slide.columns.type') }}"> --}}
        <select class="form-control" v-model="form.type" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('type'), 'form-control-success': fields.type && fields.type.valid}" id="type" name="type" placeholder="{{ trans('admin.tour-slide.columns.type') }}">
                <option value="">Select Type</option>
                <option value="photo">Photo</option>

          </select>
        <div v-if="errors.has('type')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('type') }}</div>
    </div>
</div>

<div v-if="form.type == 'video'" class="form-group row align-items-center" >
    <label for="tour_video" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.tour-slide.columns.tour_video') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('tour_video') }}</div>
        @if(isset($tourSlide))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\TourSlide::class)->getMediaCollection('tour_video'),
                'media' => $tourSlide->getThumbs200ForCollection('tour_video'),
                'Label' => "tour_video"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\TourSlide::class)->getMediaCollection('tour_video'),
                'Label' => "tour_video"
            ])
        @endif

    </div>
</div>


<div v-if="form.type == 'photo'" class="form-group row align-items-center" >
    <label for="tour_image" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.tour-slide.columns.tour_image') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('tour_image') }}</div>
        @if(isset($tourSlide))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\TourSlide::class)->getMediaCollection('tour_image'),
                'media' => $tourSlide->getThumbs200ForCollection('tour_image'),
                'Label' => "tour_image"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\TourSlide::class)->getMediaCollection('tour_image'),
                'Label' => "tour_image"
            ])
        @endif

    </div>
</div>

