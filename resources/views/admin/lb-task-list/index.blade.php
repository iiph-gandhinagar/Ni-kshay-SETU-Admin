@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.lb-task-list.actions.index'))

@section('body')

    <lb-task-list-listing :data="{{ $data->toJson() }}" :level="{{ json_encode($level) }}"
        :badge="{{ json_encode($badge) }}" :url="'{{ url('admin/lb-task-lists') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.lb-task-list.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/lb-task-lists/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                            {{ trans('admin.lb-task-list.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group ">

                                    <multiselect :searchable="true" v-model="form.select_level" id="level_id"
                                        name="level_id" placeholder="Select Level"
                                        :options="{{ $level }}.map(type => type.id)"
                                        :custom-label="opt => {{ $level }}.find(x => x.id == opt).level"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('level_id', form.select_level);getLevelBadge();">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_badge" id="badge_id"
                                        name="badge_id" placeholder="Select Badge"
                                        :options="form.badge.map(type => type.id)"
                                        :custom-label="opt => form.badge.find(x => x.id == opt).badge"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('badge_id', form.select_badge)">
                                    </multiselect>
                                </div>
                                <div class="col-md-3 form-group"></div>
                                <div class="col-md-3 form-group"></div>
                            </div>
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary"
                                                    @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp;
                                                    {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto form-group ">
                                        <select class="form-control" v-model="pagination.state.per_page">

                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover ">
                                <thead>
                                    <tr>
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox"
                                                v-model="isClickedAll" v-validate="''" data-vv-name="enabled"
                                                name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' width="5%" data-title="id" :column="'id'">
                                            {{ trans('admin.lb-task-list.columns.id') }}</th>
                                        <th is='sortable' width="15%" data-title="level" :column="'level'">
                                            {{ trans('admin.lb-task-list.columns.level') }}</th>
                                        <th is='sortable' width="15%" data-title="badges" :column="'badges'">
                                            {{ trans('admin.lb-task-list.columns.badges') }}</th>
                                        <th is='sortable' width="5%" data-title="mins_spent" :column="'mins_spent'">
                                            {{ trans('admin.lb-task-list.columns.mins_spent') }}</th>
                                        <th is='sortable' width="5%" data-title="sub_module_usage_count"
                                            :column="'sub_module_usage_count'">
                                            {{ trans('admin.lb-task-list.columns.sub_module_usage_count') }}</th>
                                        <th is='sortable' width="5%" data-title="App_opended_count"
                                            :column="'App_opended_count'">
                                            {{ trans('admin.lb-task-list.columns.App_opended_count') }}</th>
                                        <th is='sortable' width="5%" data-title="chatbot_usage_count"
                                            :column="'chatbot_usage_count'">
                                            {{ trans('admin.lb-task-list.columns.chatbot_usage_count') }}</th>
                                        <th is='sortable' width="5%" data-title="resource_material_accessed_count"
                                            :column="'resource_material_accessed_count'">
                                            {{ trans('admin.lb-task-list.columns.resource_material_accessed_count') }}</th>
                                        <th is='sortable' width="5%" data-title="total_task" :column="'total_task'">
                                            {{ trans('admin.lb-task-list.columns.total_task') }}</th>
                                        <th is='sortable' width="20%" data-title="created_at" :column="'created_at'">
                                            {{ trans('admin.lb-task-list.columns.created_at') }}</th>

                                        <th width="15%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="11">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/lb-task-lists')"
                                                    v-if="(clickedBulkItemsCount < pagination.state.total)"> <i
                                                        class="fa"
                                                        :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i>
                                                    {{ trans('brackets/admin-ui::admin.listing.check_all_items') }}
                                                    @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                    href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>
                                            </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3"
                                                    @click="bulkDelete('/admin/lb-task-lists/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox"
                                                v-model="bulkItems[item.id]" v-validate="''"
                                                :data-vv-name="'enabled' + item.id"
                                                :name="'enabled' + item.id + '_fake_element'"
                                                @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td data-title="id">@{{ item.id }}</td>
                                        <td data-title="level">@{{ item.lb_level.level }}</td>
                                        <td data-title="badges">@{{ item.lb_badge.badge }}</td>
                                        <td data-title="mins_spent">@{{ item.mins_spent }}</td>
                                        <td data-title="sub_module_usage_count">@{{ item.sub_module_usage_count }}</td>
                                        <td data-title="App_opended_count">@{{ item.App_opended_count }}</td>
                                        <td data-title="chatbot_usage_count">@{{ item.chatbot_usage_count }}</td>
                                        <td data-title="resource_material_accessed_count">@{{ item.resource_material_accessed_count }}</td>
                                        <td data-title="total_task">@{{ item.total_task }}</td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>

                                        <td class="action_buttons">
                                            <div class="row">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info"
                                                        :href="item.resource_url + '/edit'"
                                                        title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                        role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i
                                                            class="fa fa-trash-o"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/lb-task-lists/create') }}"
                                    role="button"><i class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.lb-task-list.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </lb-task-list-listing>

@endsection
