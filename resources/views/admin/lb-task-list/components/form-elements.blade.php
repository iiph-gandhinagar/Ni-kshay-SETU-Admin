<div class="form-group row align-items-center" :class="{'has-danger': errors.has('level'), 'has-success': fields.level && fields.level.valid }">
    <label for="level" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.level') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.level" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('level'), 'form-control-success': fields.level && fields.level.valid}" id="level" name="level" placeholder="{{ trans('admin.lb-task-list.columns.level') }}"> --}}
        <multiselect v-model="form.level" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="level" track-by="id" 
            :options="{{ $lb_level->toJson() }}" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto" @input="getBadges()"></multiselect>
        <div v-if="errors.has('level')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('level') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('badges'), 'has-success': fields.badges && fields.badges.valid }">
    <label for="badges" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.badges') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        {{-- <input type="text" v-model="form.badges" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('badges'), 'form-control-success': fields.badges && fields.badges.valid}" id="badges" name="badges" placeholder="{{ trans('admin.lb-task-list.columns.badges') }}"> --}}
        <multiselect v-model="form.badges" placeholder="{{ trans('brackets/admin-ui::admin.forms.select_options') }}" label="badge" track-by="id" 
            :options="form.badge" 
            :multiple="false"
            :searchable="true"
            :close-on-select="true"
            open-direction="auto"></multiselect>
        <div v-if="errors.has('badges')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('badges') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('mins_spent'), 'has-success': fields.mins_spent && fields.mins_spent.valid }">
    <label for="mins_spent" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.mins_spent') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.mins_spent" v-validate="''" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('mins_spent'), 'form-control-success': fields.mins_spent && fields.mins_spent.valid}" id="mins_spent" name="mins_spent" placeholder="{{ trans('admin.lb-task-list.columns.mins_spent') }}">
        <div v-if="errors.has('mins_spent')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('mins_spent') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('sub_module_usage_count'), 'has-success': fields.sub_module_usage_count && fields.sub_module_usage_count.valid }">
    <label for="sub_module_usage_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.sub_module_usage_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.sub_module_usage_count" v-validate="'required'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('sub_module_usage_count'), 'form-control-success': fields.sub_module_usage_count && fields.sub_module_usage_count.valid}" id="sub_module_usage_count" name="sub_module_usage_count" placeholder="{{ trans('admin.lb-task-list.columns.sub_module_usage_count') }}">
        <div v-if="errors.has('sub_module_usage_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('sub_module_usage_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('App_opended_count'), 'has-success': fields.App_opended_count && fields.App_opended_count.valid }">
    <label for="App_opended_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.App_opended_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.App_opended_count" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('App_opended_count'), 'form-control-success': fields.App_opended_count && fields.App_opended_count.valid}" id="App_opended_count" name="App_opended_count" placeholder="{{ trans('admin.lb-task-list.columns.App_opended_count') }}">
        <div v-if="errors.has('App_opended_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('App_opended_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('chatbot_usage_count'), 'has-success': fields.chatbot_usage_count && fields.chatbot_usage_count.valid }">
    <label for="chatbot_usage_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.chatbot_usage_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.chatbot_usage_count" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('chatbot_usage_count'), 'form-control-success': fields.chatbot_usage_count && fields.chatbot_usage_count.valid}" id="chatbot_usage_count" name="chatbot_usage_count" placeholder="{{ trans('admin.lb-task-list.columns.chatbot_usage_count') }}">
        <div v-if="errors.has('chatbot_usage_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('chatbot_usage_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('resource_material_accessed_count'), 'has-success': fields.resource_material_accessed_count && fields.resource_material_accessed_count.valid }">
    <label for="resource_material_accessed_count" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.resource_material_accessed_count') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.resource_material_accessed_count" v-validate="'integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('resource_material_accessed_count'), 'form-control-success': fields.resource_material_accessed_count && fields.resource_material_accessed_count.valid}" id="resource_material_accessed_count" name="resource_material_accessed_count" placeholder="{{ trans('admin.lb-task-list.columns.resource_material_accessed_count') }}">
        <div v-if="errors.has('resource_material_accessed_count')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('resource_material_accessed_count') }}</div>
    </div>
</div>

<div class="form-group row align-items-center" :class="{'has-danger': errors.has('total_task'), 'has-success': fields.total_task && fields.total_task.valid }">
    <label for="total_task" class="col-form-label text-md-right" :class="isFormLocalized ? 'col-md-4' : 'col-md-2'">{{ trans('admin.lb-task-list.columns.total_task') }}</label>
        <div :class="isFormLocalized ? 'col-md-4' : 'col-md-9 col-xl-8'">
        <input type="text" v-model="form.total_task" v-validate="'required|integer'" @input="validate($event)" class="form-control" :class="{'form-control-danger': errors.has('total_task'), 'form-control-success': fields.total_task && fields.total_task.valid}" id="total_task" name="total_task" placeholder="{{ trans('admin.lb-task-list.columns.total_task') }}">
        <div v-if="errors.has('total_task')" class="form-control-feedback form-text" v-cloak>@{{ errors.first('total_task') }}</div>
    </div>
</div>


