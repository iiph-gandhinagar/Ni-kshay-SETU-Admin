<div class="form-group row align-items-center" :class="{'has-danger': errors.has('chapter_title'), 'has-success': fields.chapter_title && fields.chapter_title.valid }">
    <label for="chapter_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.chapter_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.chapter_title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('chapter_title'), 'form-control-success': fields.chapter_title && fields.chapter_title.valid}" id="chapter_title" name="chapter_title" placeholder="{{ trans('admin.cgc-intervention.columns.chapter_title') }}">
        <div v-if="errors.has('chapter_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('chapter_title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" >
    <label for="chapter_video" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.chapter_video') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('chapter_video') }}</div>
        @if(isset($cgcIntervention))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('chapter_video'),
                'media' => $cgcIntervention->getThumbs200ForCollection('chapter_video'),
                'Label' => "chapter_video"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('chapter_video'),
                'Label' => "chapter_video"
            ])
        @endif

    </div>
</div>

<div class="form-group row align-items-center" >
    <label for="video_image" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.video_image') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('video_image') }}</div>
        @if(isset($cgcIntervention))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('video_image'),
                'media' => $cgcIntervention->getThumbs200ForCollection('video_image'),
                'Label' => "video_image"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('video_image'),
                'Label' => "video_image"
            ])
        @endif

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('video_title'), 'has-success': fields.video_title && fields.video_title.valid }">
    <label for="video_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.video_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.video_title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('video_title'), 'form-control-success': fields.video_title && fields.video_title.valid}" id="video_title" name="video_title" placeholder="{{ trans('admin.cgc-intervention.columns.video_title') }}">
        <div v-if="errors.has('video_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('video_title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('description'), 'has-success': fields.description && fields.description.valid }">
    <label for="description" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.description') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.description" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('description'), 'form-control-success': fields.description && fields.description.valid}" id="description" name="description" placeholder="{{ trans('admin.cgc-intervention.columns.description') }}"> --}}
        <div>
            {{-- <textarea class="form-control" v-model="form.description" v-validate="'required|max:1000'" id="description" name="description"></textarea> --}}
            <wysiwyg v-model="form.description" v-validate="'required|max:1000'" id="description" name="description" :config="mediaWysiwygConfig"></wysiwyg>
        </div>
        <div v-if="errors.has('description')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('description') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" >
    <label for="reference_links" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.reference_links') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div v-if="errors.has('')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('reference_links') }}</div>
        @if(isset($cgcIntervention))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('reference_links'),
                'media' => $cgcIntervention->getThumbs200ForCollection('reference_links'),
                'Label' => "reference_links"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\CgcIntervention::class)->getMediaCollection('reference_links'),
                'Label' => "reference_links"
            ])
        @endif

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('reference_title'), 'has-success': fields.reference_title && fields.reference_title.valid }">
    <label for="reference_title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.reference_title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.reference_title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('reference_title'), 'form-control-success': fields.reference_title && fields.reference_title.valid}" id="reference_title" name="reference_title" placeholder="{{ trans('admin.cgc-intervention.columns.reference_title') }}">
        <div v-if="errors.has('reference_title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('reference_title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('assessment_id'), 'has-success': fields.assessment_id && fields.assessment_id.valid }">
    <label for="assessment_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.cgc-intervention.columns.assessment_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.assessment_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.assessment-question.columns.assessment_id') }}"> --}}
        <select class="form-control" v-model="form.assessment_id" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('assessment_id'), 'form-control-success': fields.assessment_id && fields.assessment_id.valid}" id="assessment_id" name="assessment_id" placeholder="{{ trans('admin.cgc-intervention.columns.assessment_id') }}">
            <option value="">Select Assessment</option> 
            @foreach ($assessment as $item)
                <option value="{{ $item->id }}" >{{ $item->assessment_title }}</option>  
            @endforeach    
      </select>
        <div v-if="errors.has('assessment_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('assessment_id') }}</div>
    </div>
</div>


