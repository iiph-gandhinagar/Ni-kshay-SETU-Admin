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
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.static-blog.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="''" @input="validate($event);getTitle()" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.static-blog.columns.title') }}">
                    <div v-if="errors.has('title_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('short_description_{{ $locale }}'), 'has-success': fields.short_description_{{ $locale }} && fields.short_description_{{ $locale }}.valid }">
                <label for="short_description_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.static-blog.columns.short_description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.short_description.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('short_description_{{ $locale }}'), 'form-control-success': fields.short_description_{{ $locale }} && fields.short_description_{{ $locale }}.valid }" id="short_description_{{ $locale }}" name="short_description_{{ $locale }}" placeholder="{{ trans('admin.static-blog.columns.short_description') }}">
                    <div v-if="errors.has('short_description_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('short_description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row">
    @foreach($locales as $locale)
        <div class="col-md" v-show="shouldShowLangGroup('{{ $locale }}')" v-cloak>
            <div class="form-group row align-items-center" :class="{'has-danger': errors.has('description_{{ $locale }}'), 'has-success': fields.description_{{ $locale }} && fields.description_{{ $locale }}.valid }">
                <label for="description_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.static-blog.columns.description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <div>
                        {{-- <wysiwyg v-model="form.description.{{ $locale }}" v-validate="''" id="description_{{ $locale }}" name="description_{{ $locale }}" :config="mediaWysiwygConfig"></wysiwyg> --}}
                        {{--<textarea v-model="form.description.{{ $locale }}"  @input="getCkEditorData($event.target.value)" v-validate="''" id="description_{{ $locale }}" name="description_{{ $locale }}" class="editor ckeditor form-control" ></textarea>--}}
                        <ckeditor  type="classic" id="description" v-model="form.description.{{ $locale }}" @input="$emit('input', $event);" :config="editorConfig"></ckeditor>
                        
                        {{-- <div id="toolbar-container" >
                        </div>&lt;p&gt;I
                        <!-- This container will become the editable. -->
                            <div id="editor" id="editor" name="description_{{ $locale }}">
                                <input type="hidden" v-model="form.description.{{ $locale }}" name="form.description.{{ $locale }}" id="form.description.{{ $locale }}">
                            </div>--}}
                    </div>
                    <div v-if="errors.has('description_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('description_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('author'), 'has-success': fields.author && fields.author.valid }">
    <label for="author" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.author') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.author" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('author'), 'form-control-success': fields.author && fields.author.valid}" id="author" name="author" placeholder="{{ trans('admin.static-blog.columns.author') }}">
        <div v-if="errors.has('author')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('author') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('source'), 'has-success': fields.source && fields.source.valid }">
    <label for="source" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.source') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.source" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('source'), 'form-control-success': fields.source && fields.source.valid}" id="source" name="source" placeholder="{{ trans('admin.static-blog.columns.source') }}">
        <div v-if="errors.has('source')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('source') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('keywords'), 'has-success': fields.keywords && fields.keywords.valid }">
    <label for="keywords" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.keywords') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.keywords" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('keywords'), 'form-control-success': fields.keywords && fields.keywords.valid}" id="keywords" name="keywords" placeholder="{{ trans('admin.static-blog.columns.keywords') }}"> --}}
        <multiselect v-model="form.keywords" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" :options="options" :multiple="true" :taggable="true"  @tag="addTag" open-direction="bottom"  :close-on-select="false"></multiselect>
        <div v-if="errors.has('keywords')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('keywords') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.static-blog.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('blog_thumb_image1'), 'has-success': fields.blog_thumb_image1 && fields.blog_thumb_image1.valid }">
    <label for="blog_thumb_image1" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.blog_thumb_image1') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($staticBlog))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image1'),
                'media' => $staticBlog->getThumbs200ForCollection('blog_thumb_image1'),
                'Label' => "blog_thumb_image1"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image1'),
                'Label' => "blog_thumb_image1",
            ])
        @endif
        <div v-if="errors.has('blog_thumb_image1')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('blog_thumb_image1') }}</div>

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('blog_thumb_image2'), 'has-success': fields.blog_thumb_image2 && fields.blog_thumb_image2.valid }">
    <label for="blog_thumb_image2" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.blog_thumb_image2') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($staticBlog))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image2'),
                'media' => $staticBlog->getThumbs200ForCollection('blog_thumb_image2'),
                'Label' => "blog_thumb_image2"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image2'),
                'Label' => "blog_thumb_image2"
            ])
        @endif
        <div v-if="errors.has('blog_thumb_image2')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('blog_thumb_image2') }}</div>

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('blog_thumb_image3'), 'has-success': fields.blog_thumb_image3 && fields.blog_thumb_image3.valid }">
    <label for="blog_thumb_image3" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.static-blog.columns.blog_thumb_image3') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($staticBlog))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image3'),
                'media' => $staticBlog->getThumbs200ForCollection('blog_thumb_image3'),
                'Label' => "blog_thumb_image3"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\StaticBlog::class)->getMediaCollection('blog_thumb_image3'),
                'Label' => "blog_thumb_image3"
            ])
        @endif
        <div v-if="errors.has('blog_thumb_image3')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('blog_thumb_image3') }}</div>

    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.static-blog.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>

{{--@section('bottom-scripts')
    @include('admin.script-element-for-CKeditor')
@endsection--}}
