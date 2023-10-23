<div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.flash-similar-app.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_title'), 'has-success': fields.sub_title && fields.sub_title.valid }">
    <label for="sub_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.sub_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sub_title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_title'), 'form-control-success': fields.sub_title && fields.sub_title.valid}" id="sub_title" name="sub_title" placeholder="{{ trans('admin.flash-similar-app.columns.sub_title') }}">
        <div v-if="errors.has('sub_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('href'), 'has-success': fields.href && fields.href.valid }">
    <label for="href" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.href') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.href" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('href'), 'form-control-success': fields.href && fields.href.valid}" id="href" name="href" placeholder="{{ trans('admin.flash-similar-app.columns.href') }}">
        <div v-if="errors.has('href')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('href') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('href_web'), 'has-success': fields.href_web && fields.href_web.valid }">
    <label for="href_web" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.href_web') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.href_web" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('href_web'), 'form-control-success': fields.href_web && fields.href_web.valid}" id="href_web" name="href_web" placeholder="{{ trans('admin.flash-similar-app.columns.href_web') }}">
        <div v-if="errors.has('href_web')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('href_web') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('href_ios'), 'has-success': fields.href_ios && fields.href_ios.valid }">
    <label for="href_ios" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.href_ios') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.href_ios" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('href_ios'), 'form-control-success': fields.href_ios && fields.href_ios.valid}" id="href_ios" name="href_ios" placeholder="{{ trans('admin.flash-similar-app.columns.href_ios') }}">
        <div v-if="errors.has('href_ios')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('href_ios') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('flash_app_icon'), 'has-success': fields.flash_app_icon && fields.flash_app_icon.valid }">
    <label for="flash_app_icon" class="col-form-label text-md-right" style="color:red" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.flash_app_icon') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($flashSimilarApp))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\FlashSimilarApp::class)->getMediaCollection('flash_app_icon'),
                'media' => $flashSimilarApp->getThumbs200ForCollection('flash_app_icon'),
                'Label' => "flash_app_icon"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\FlashSimilarApp::class)->getMediaCollection('flash_app_icon'),
                'Label' => "flash_app_icon",
            ])
        @endif
        <div v-if="errors.has('flash_app_icon')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('flash_app_icon') }}</div>

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('order_index'), 'has-success': fields.order_index && fields.order_index.valid }">
    <label for="order_index" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.flash-similar-app.columns.order_index') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.order_index" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('order_index'), 'form-control-success': fields.order_index && fields.order_index.valid}" id="order_index" name="order_index" placeholder="{{ trans('admin.flash-similar-app.columns.order_index') }}">
        <div v-if="errors.has('order_index')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('order_index') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('active'), 'has-success': fields.active && fields.active.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="active" type="checkbox" v-model="form.active" v-validate="''" data-vv-name="active"  name="active_fake_element">
        <label class="form-check-label" for="active">
            {{ trans('admin.flash-similar-app.columns.active') }}
        </label>
        <input type="hidden" name="active" :value="form.active">
        <div v-if="errors.has('active')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('active') }}</div>
    </div>
</div>


