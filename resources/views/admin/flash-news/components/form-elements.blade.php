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
                <label for="title_{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ trans('admin.flash-news.columns.title') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <input type="text" v-model="form.title.{{ $locale }}" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title_{{ $locale }}'), 'form-control-success': fields.title_{{ $locale }} && fields.title_{{ $locale }}.valid }" id="title_{{ $locale }}" name="title_{{ $locale }}" placeholder="{{ trans('admin.flash-news.columns.title') }}">
                    <div v-if="errors.has('title_{{ $locale }}')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('title_{{ $locale }}') }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('description_{{ $locale }}'), 'has-success': fields.description_{{ $locale }} && fields.description_{{ $locale }}.valid }">
                <label for="description" class="col-md-2 col-form-label text-md-right">{{ trans('admin.flash-news.columns.description') }}</label>
                <div class="col-md-9" :class="{'col-xl-8': !isFormLocalized }">
                    <div>
                        <wysiwyg v-model="form.description" v-validate="''" id="description" name="description" :config="mediaWysiwygConfig"></wysiwyg>
                    </div>
                    <div v-if="errors.has('description')" class="form-control-feedback form-text" v-cloak>{{'{{'}} errors.first('description') }}</div>
                </div>
            </div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('author'), 'has-success': fields.author && fields.author.valid }">
    <label for="author" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.author') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.author" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('author'), 'form-control-success': fields.author && fields.author.valid}" id="author" name="author" placeholder="{{ trans('admin.flash-news.columns.author') }}">
        <div v-if="errors.has('author')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('author') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('source'), 'has-success': fields.source && fields.source.valid }">
    <label for="source" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.source') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.source" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('source'), 'form-control-success': fields.source && fields.source.valid}" id="source" name="source" placeholder="{{ trans('admin.flash-news.columns.source') }}">
        <div v-if="errors.has('source')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('source') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('href'), 'has-success': fields.href && fields.href.valid }">
    <label for="href" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.href') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.href" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('href'), 'form-control-success': fields.href && fields.href.valid}" id="href" name="href" placeholder="{{ trans('admin.flash-news.columns.href') }}">
        <div v-if="errors.has('href')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('href') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.flash-news.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('publish_date'), 'has-success': fields.publish_date && fields.publish_date.valid }">
    <label for="publish_date" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.publish_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.publish_date" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('publish_date'), 'form-control-success': fields.publish_date && fields.publish_date.valid}" id="publish_date" name="publish_date" placeholder="{{ trans('admin.flash-news.columns.publish_date') }}">
        <div v-if="errors.has('publish_date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('publish_date') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('flash_news_icon'), 'has-success': fields.flash_news_icon && fields.flash_news_icon.valid }">
    <label for="flash_news_icon" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news.columns.flash_news_icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($flashNews))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\FlashNews::class)->getMediaCollection('flash_news_icon'),
                'media' => $flashNews->getThumbs200ForCollection('flash_news_icon'),
                'Label' => "flash_news_icon"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\FlashNews::class)->getMediaCollection('flash_news_icon'),
                'Label' => "flash_news_icon",
            ])
        @endif
        <div v-if="errors.has('flash_news_icon')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('flash_news_icon') }}</div>

    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.flash-news.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>

