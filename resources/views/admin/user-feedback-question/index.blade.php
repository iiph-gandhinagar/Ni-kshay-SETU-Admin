@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-feedback-question.actions.index'))

@section('body')

    <user-feedback-question-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/user-feedback-questions') }}'"
        :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.user-feedback-question.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/user-feedback-questions/create') }}" role="button"><i
                                class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-feedback-question.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                id="search_field" v-model="search"
                                                @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary"
                                                    @click="filter('search', search); getSerchFilter(1)"><i
                                                        class="fa fa-search"></i>&nbsp;
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
                                            {{ trans('admin.user-feedback-question.columns.id') }}</th>
                                        <th is='sortable' width="15%" data-title="feedback_question"
                                            :column="'feedback_question'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_question') }}</th>
                                        <th is='sortable' width="15%" data-title="feedback_description"
                                            :column="'feedback_description'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_description') }}</th>
                                        <th is='sortable' width="5%" data-title="feedback_value"
                                            :column="'feedback_value'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_value') }}</th>
                                        <th is='sortable' width="5%" data-title="feedback_time"
                                            :column="'feedback_time'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_time') }}</th>
                                        <th is='sortable' width="10%" data-title="feedback_type"
                                            :column="'feedback_type'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_type') }}</th>
                                        <th is='sortable' width="10%" data-title="feedback_days"
                                            :column="'feedback_days'">
                                            {{ trans('admin.user-feedback-question.columns.feedback_days') }}</th>
                                        <th is='sortable' width="5%" data-title="is_active" :column="'is_active'">
                                            {{ trans('admin.user-feedback-question.columns.is_active') }}</th>
                                        <th is='sortable' width="10%" data-title="created_at" :column="'created_at'">
                                            {{ trans('admin.user-feedback-question.columns.created_at') }}</th>

                                        <th width="10%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="10">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/user-feedback-questions')"
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
                                                    @click="bulkDelete('/admin/user-feedback-questions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="feedback_question">@{{ item.feedback_question }}</td>
                                        <td data-title="feedback_description">@{{ item.feedback_description }}</td>
                                        <td data-title="feedback_value">@{{ item.feedback_value }}</td>
                                        <td data-title="feedback_time">@{{ item.feedback_time }}</td>
                                        <td data-title="feedback_type">@{{ item.feedback_type }}</td>
                                        <td data-title="feedback_days">@{{ item.feedback_days }}</td>
                                        <td data-title="is_active">@{{ item.is_active }}</td>
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
                                <a class="btn btn-primary btn-spinner"
                                    href="{{ url('admin/user-feedback-questions/create') }}" role="button"><i
                                        class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.user-feedback-question.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-feedback-question-listing>

@endsection
