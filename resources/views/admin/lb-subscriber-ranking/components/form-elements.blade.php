<div class="form-group row align-items-center" :class="{'has-danger': errors.has('subscriber_id'), 'has-success': fields.subscriber_id && fields.subscriber_id.valid }">
    <label for="subscriber_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.subscriber_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.subscriber_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('subscriber_id'), 'form-control-success': fields.subscriber_id && fields.subscriber_id.valid}" id="subscriber_id" name="subscriber_id" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.subscriber_id') }}">
        <div v-if="errors.has('subscriber_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('subscriber_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('level_id'), 'has-success': fields.level_id && fields.level_id.valid }">
    <label for="level_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.level_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.level_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('level_id'), 'form-control-success': fields.level_id && fields.level_id.valid}" id="level_id" name="level_id" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.level_id') }}">
        <div v-if="errors.has('level_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('level_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('badge_id'), 'has-success': fields.badge_id && fields.badge_id.valid }">
    <label for="badge_id" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.badge_id') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.badge_id" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('badge_id'), 'form-control-success': fields.badge_id && fields.badge_id.valid}" id="badge_id" name="badge_id" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.badge_id') }}">
        <div v-if="errors.has('badge_id')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('badge_id') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mins_spent_count'), 'has-success': fields.mins_spent_count && fields.mins_spent_count.valid }">
    <label for="mins_spent_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.mins_spent_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mins_spent_count" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mins_spent_count'), 'form-control-success': fields.mins_spent_count && fields.mins_spent_count.valid}" id="mins_spent_count" name="mins_spent_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.mins_spent_count') }}">
        <div v-if="errors.has('mins_spent_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mins_spent_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_module_usage_count'), 'has-success': fields.sub_module_usage_count && fields.sub_module_usage_count.valid }">
    <label for="sub_module_usage_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.sub_module_usage_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sub_module_usage_count" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_module_usage_count'), 'form-control-success': fields.sub_module_usage_count && fields.sub_module_usage_count.valid}" id="sub_module_usage_count" name="sub_module_usage_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.sub_module_usage_count') }}">
        <div v-if="errors.has('sub_module_usage_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_module_usage_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('App_opended_count'), 'has-success': fields.App_opended_count && fields.App_opended_count.valid }">
    <label for="App_opended_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.App_opended_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.App_opended_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('App_opended_count'), 'form-control-success': fields.App_opended_count && fields.App_opended_count.valid}" id="App_opended_count" name="App_opended_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.App_opended_count') }}">
        <div v-if="errors.has('App_opended_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('App_opended_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('chatbot_usage_count'), 'has-success': fields.chatbot_usage_count && fields.chatbot_usage_count.valid }">
    <label for="chatbot_usage_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.chatbot_usage_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.chatbot_usage_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('chatbot_usage_count'), 'form-control-success': fields.chatbot_usage_count && fields.chatbot_usage_count.valid}" id="chatbot_usage_count" name="chatbot_usage_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.chatbot_usage_count') }}">
        <div v-if="errors.has('chatbot_usage_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('chatbot_usage_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('resource_material_accessed_count'), 'has-success': fields.resource_material_accessed_count && fields.resource_material_accessed_count.valid }">
    <label for="resource_material_accessed_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.resource_material_accessed_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.resource_material_accessed_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('resource_material_accessed_count'), 'form-control-success': fields.resource_material_accessed_count && fields.resource_material_accessed_count.valid}" id="resource_material_accessed_count" name="resource_material_accessed_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.resource_material_accessed_count') }}">
        <div v-if="errors.has('resource_material_accessed_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('resource_material_accessed_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('total_task_count'), 'has-success': fields.total_task_count && fields.total_task_count.valid }">
    <label for="total_task_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-subscriber-ranking.columns.total_task_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.total_task_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('total_task_count'), 'form-control-success': fields.total_task_count && fields.total_task_count.valid}" id="total_task_count" name="total_task_count" placeholder="{{ trans('admin.lb-subscriber-ranking.columns.total_task_count') }}">
        <div v-if="errors.has('total_task_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('total_task_count') }}</div>
    </div>
</div>


