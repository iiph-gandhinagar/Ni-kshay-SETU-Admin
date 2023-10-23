<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subscriber_id'), 'has-success': fields.subscriber_id && fields.subscriber_id.valid }">
    <label for="subscriber_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-detail.columns.subscriber_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subscriber_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subscriber_id'), 'form-control-success': fields.subscriber_id && fields.subscriber_id.valid}" id="subscriber_id" name="subscriber_id" placeholder="{{ trans('admin.user-feedback-detail.columns.subscriber_id') }}">
        <div v-if="errors.has('subscriber_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subscriber_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('feedback_id'), 'has-success': fields.feedback_id && fields.feedback_id.valid }">
    <label for="feedback_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-detail.columns.feedback_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.feedback_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('feedback_id'), 'form-control-success': fields.feedback_id && fields.feedback_id.valid}" id="feedback_id" name="feedback_id" placeholder="{{ trans('admin.user-feedback-detail.columns.feedback_id') }}">
        <div v-if="errors.has('feedback_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('feedback_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('ratings'), 'has-success': fields.ratings && fields.ratings.valid }">
    <label for="ratings" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-detail.columns.ratings') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.ratings" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('ratings'), 'form-control-success': fields.ratings && fields.ratings.valid}" id="ratings" name="ratings" placeholder="{{ trans('admin.user-feedback-detail.columns.ratings') }}">
        <div v-if="errors.has('ratings')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('ratings') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('review'), 'has-success': fields.review && fields.review.valid }">
    <label for="review" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.user-feedback-detail.columns.review') }}</label>
    <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <div>
            <textarea class="form-control" v-model="form.review" v-validate="''" id="review" name="review"></textarea>
        </div>
        <div v-if="errors.has('review')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('review') }}</div>
    </div>
</div>


