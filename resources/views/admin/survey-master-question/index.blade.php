@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.survey-master-question.actions.index'))

@section('body')

    <survey-master-question-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/survey-master-questions') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.survey-master-question.actions.index') }}
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                            href="{{ url('admin/survey-master-questions/export') }}" role="button"><i
                                class="fa fa-file-excel-o"></i>&nbsp;
                            {{ trans('admin.survey-master-question.actions.export') }}</a>
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/survey-master-questions/create') }}" role="button"><i
                                class="fa fa-plus"></i>&nbsp; {{ trans('admin.survey-master-question.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
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
                                            {{ trans('admin.survey-master-question.columns.id') }}</th>
                                        <th is='sortable' width="5%" data-title="survey_master_id"
                                            :column="'survey_master_id'">
                                            {{ trans('admin.survey-master-question.columns.survey_master_id') }}</th>
                                        <th is='sortable' width="10%" data-title="question" :column="'question'">
                                            {{ trans('admin.survey-master-question.columns.question') }}</th>
                                        <th is='sortable' width="10%" data-title="type" :column="'type'">
                                            {{ trans('admin.survey-master-question.columns.type') }}</th>
                                        <th is='sortable' width="10%" data-title="option1" :column="'option1'">
                                            {{ trans('admin.survey-master-question.columns.option1') }}</th>
                                        <th is='sortable' width="10%" data-title="option2" :column="'option2'">
                                            {{ trans('admin.survey-master-question.columns.option2') }}</th>
                                        <th is='sortable' width="10%" data-title="option3" :column="'option3'">
                                            {{ trans('admin.survey-master-question.columns.option3') }}</th>
                                        <th is='sortable' width="10%" data-title="option4" :column="'option4'">
                                            {{ trans('admin.survey-master-question.columns.option4') }}</th>
                                        <th is='sortable' width="5%" data-title="order_index" :column="'order_index'">
                                            {{ trans('admin.survey-master-question.columns.order_index') }}</th>
                                        <th is='sortable' width="5%" data-title="active" :column="'active'">
                                            {{ trans('admin.survey-master-question.columns.active') }}</th>
                                        <th is='sortable' width="10%" data-title="created_at" :column="'created_at'">
                                            {{ trans('admin.survey-master-question.columns.created_at') }}</th>

                                        <th width="10%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="12">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/survey-master-questions')"
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
                                                    @click="bulkDelete('/admin/survey-master-questions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="survey_master_id">@{{ item.survey_master.title }}</td>
                                        <td data-title="question">@{{ item.question }}</td>
                                        <td data-title="type">@{{ item.type }}</td>
                                        <td data-title="option1">@{{ item.option1 }}</td>
                                        <td data-title="option2">@{{ item.option2 }}</td>
                                        <td data-title="option3">@{{ item.option3 }}</td>
                                        <td data-title="option4">@{{ item.option4 }}</td>
                                        <td data-title="order_index">@{{ item.order_index }}</td>
                                        <td data-title="active">@{{ item.active }}</td>
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
                                    href="{{ url('admin/survey-master-questions/create') }}" role="button"><i
                                        class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.survey-master-question.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </survey-master-question-listing>

@endsection
