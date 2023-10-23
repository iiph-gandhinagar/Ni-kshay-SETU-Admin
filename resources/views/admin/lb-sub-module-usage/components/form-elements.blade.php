<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subscriber_id'), 'has-success': fields.subscriber_id && fields.subscriber_id.valid }">
    <label for="subscriber_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-sub-module-usage.columns.subscriber_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subscriber_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subscriber_id'), 'form-control-success': fields.subscriber_id && fields.subscriber_id.valid}" id="subscriber_id" name="subscriber_id" placeholder="{{ trans('admin.lb-sub-module-usage.columns.subscriber_id') }}">
        <div v-if="errors.has('subscriber_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subscriber_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_module'), 'has-success': fields.sub_module && fields.sub_module.valid }">
    <label for="sub_module" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-sub-module-usage.columns.sub_module') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sub_module" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_module'), 'form-control-success': fields.sub_module && fields.sub_module.valid}" id="sub_module" name="sub_module" placeholder="{{ trans('admin.lb-sub-module-usage.columns.sub_module') }}">
        <div v-if="errors.has('sub_module')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_module') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('total_time'), 'has-success': fields.total_time && fields.total_time.valid }">
    <label for="total_time" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-sub-module-usage.columns.total_time') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.total_time" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('total_time'), 'form-control-success': fields.total_time && fields.total_time.valid}" id="total_time" name="total_time" placeholder="{{ trans('admin.lb-sub-module-usage.columns.total_time') }}">
        <div v-if="errors.has('total_time')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('total_time') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mins_spent'), 'has-success': fields.mins_spent && fields.mins_spent.valid }">
    <label for="mins_spent" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-sub-module-usage.columns.mins_spent') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mins_spent" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mins_spent'), 'form-control-success': fields.mins_spent && fields.mins_spent.valid}" id="mins_spent" name="mins_spent" placeholder="{{ trans('admin.lb-sub-module-usage.columns.mins_spent') }}">
        <div v-if="errors.has('mins_spent')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mins_spent') }}</div>
    </div>
</div>

<div class="form-check row" :class="{'has-danger': errors.has('completed_flag'), 'has-success': fields.completed_flag && fields.completed_flag.valid }">
    <div class="ml-md-auto" :class="isFormLocalized ? 'col-md-8' : 'col-md-10'">
        <input class="form-check-input" id="completed_flag" type="checkbox" v-model="form.completed_flag" v-validate="''" data-vv-name="completed_flag"  name="completed_flag_fake_element">
        <label class="form-check-label" for="completed_flag">
            {{ trans('admin.lb-sub-module-usage.columns.completed_flag') }}
        </label>
        <input type="hidden" name="completed_flag" :value="form.completed_flag">
        <div v-if="errors.has('completed_flag')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('completed_flag') }}</div>
    </div>
</div>


