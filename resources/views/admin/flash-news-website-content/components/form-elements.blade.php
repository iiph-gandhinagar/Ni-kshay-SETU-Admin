<div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news-website-content.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.flash-news-website-content.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('source'), 'has-success': fields.source && fields.source.valid }">
    <label for="source" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news-website-content.columns.source') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.source" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('source'), 'form-control-success': fields.source && fields.source.valid}" id="source" name="source" placeholder="{{ trans('admin.flash-news-website-content.columns.source') }}">
        <div v-if="errors.has('source')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('source') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('href'), 'has-success': fields.href && fields.href.valid }">
    <label for="href" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news-website-content.columns.href') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.href" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('href'), 'form-control-success': fields.href && fields.href.valid}" id="href" name="href" placeholder="{{ trans('admin.flash-news-website-content.columns.href') }}">
        <div v-if="errors.has('href')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('href') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('author'), 'has-success': fields.author && fields.author.valid }">
    <label for="author" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news-website-content.columns.author') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.author" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('author'), 'form-control-success': fields.author && fields.author.valid}" id="author" name="author" placeholder="{{ trans('admin.flash-news-website-content.columns.author') }}">
        <div v-if="errors.has('author')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('author') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('publish_date'), 'has-success': fields.publish_date && fields.publish_date.valid }">
    <label for="publish_date" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-news-website-content.columns.publish_date') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.publish_date" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('publish_date'), 'form-control-success': fields.publish_date && fields.publish_date.valid}" id="publish_date" name="publish_date" placeholder="{{ trans('admin.flash-news-website-content.columns.publish_date') }}">
        <div v-if="errors.has('publish_date')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('publish_date') }}</div>
    </div>
</div>


