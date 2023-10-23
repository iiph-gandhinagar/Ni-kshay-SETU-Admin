<div class="form-group row align-items-center" :class="{'has-danger': errors.has('title'), 'has-success': fields.title && fields.title.valid }">
    <label for="title" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-certificate.columns.title') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.title" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('title'), 'form-control-success': fields.title && fields.title.valid}" id="title" name="title" placeholder="{{ trans('admin.assessment-certificate.columns.title') }}">
        <div v-if="errors.has('title')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('title') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('assessment_certificate'), 'has-success': fields.assessment_certificate && fields.assessment_certificate.valid }">
    <label for="assessment_certificate" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-certificate.columns.certificate') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        @if(isset($assessmentCertificate))
            @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\AssessmentCertificate::class)->getMediaCollection('assessment_certificate'),
                'media' => $assessmentCertificate->getThumbs200ForCollection('assessment_certificate'),
                'Label' => "assessment_certificate"
            ])
        @else
        @include('brackets/admin-ui::admin.includes.avatar-uploader', [
                'mediaCollection' => app(App\Models\AssessmentCertificate::class)->getMediaCollection('assessment_certificate'),
                'Label' => "assessment_certificate"
            ])
        @endif
        <div v-if="errors.has('assessment_certificate')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('assessment_certificate') }}</div>

    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('top'), 'has-success': fields.top && fields.top.valid }">
    <label for="top" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-certificate.columns.top') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.top" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('top'), 'form-control-success': fields.top && fields.top.valid}" id="top" name="top" placeholder="{{ trans('admin.assessment-certificate.columns.top') }}">
        <div v-if="errors.has('top')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('top') }}</div>
        <div class="form-control-feedback form-text" style="color:red" v-clock>Please Don't Modify</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('left'), 'has-success': fields.left && fields.left.valid }">
    <label for="left" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.assessment-certificate.columns.left') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.left" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('left'), 'form-control-success': fields.left && fields.left.valid}" id="left" name="left" placeholder="{{ trans('admin.assessment-certificate.columns.left') }}">
        <div v-if="errors.has('left')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('left') }}</div>
        <div class="form-control-feedback form-text" style="color:red" v-clock>Please Don't Modify</div>

    </div>
</div>


