@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment-question.actions.index'))

@section('body')

    <assessment-question-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/assessment-questions') }}'"
        :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.assessment-question.actions.index') }}
                        @can('admin.assessment-question.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/assessment-questions/create') }}" role="button"><i
                                    class="fa fa-plus"></i>&nbsp; {{ trans('admin.assessment-question.actions.create') }}</a>
                        @endcan
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group">
                                    <select class="form-control" id="assessment" name="assessment"
                                        v-model="form.select_assessment"
                                        @change="filter('assessment', form.select_assessment)">
                                        <option value="0">Select Assessment</option>
                                        @foreach ($assessment as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $session == $item->id ? 'selected' : '' }}>{{ $item->assessment_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_category"
                                        placeholder="Select category"
                                        :options="{{ $category }}.map(type => type.category)" open-direction="auto"
                                        :multiple="false" @input="filter('category', form.select_category)">
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

                                        <th width="5%" data-title="id" is='sortable' :column="'id'"
                                            id="id1">{{ trans('admin.assessment-question.columns.id') }}</th>
                                        <th width="10%"data-title="assessment_id" is='sortable'
                                            :column="'assessment_id'">
                                            {{ trans('admin.assessment-question.columns.assessment_id') }}</th>
                                        <th width="10%"data-title="question" is='sortable' :column="'question'">
                                            {{ trans('admin.assessment-question.columns.question') }}</th>
                                        <th width="10%"data-title="option1" is='sortable' :column="'option1'">
                                            {{ trans('admin.assessment-question.columns.option1') }}</th>
                                        <th width="10%"data-title="option2" is='sortable' :column="'option2'">
                                            {{ trans('admin.assessment-question.columns.option2') }}</th>
                                        <th width="10%"data-title="option3" is='sortable' :column="'option3'">
                                            {{ trans('admin.assessment-question.columns.option3') }}</th>
                                        <th width="10%"data-title="option4" is='sortable' :column="'option4'">
                                            {{ trans('admin.assessment-question.columns.option4') }}</th>
                                        <th width="5%" data-title="correct_answer" is='sortable'
                                            :column="'correct_answer'">
                                            {{ trans('admin.assessment-question.columns.correct_answer') }}</th>
                                        <th width="2%" data-title="order_index" is='sortable' :column="'order_index'">
                                            {{ trans('admin.assessment-question.columns.order_index') }}</th>
                                        <th width="2%" data-title="category" is='sortable' :column="'category'">
                                            {{ trans('admin.assessment-question.columns.category') }}</th>
                                        <th width="2%" data-title="created_by" is='sortable' :column="'created_by'">
                                            Created By</th>

                                        <th width="10"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="16">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/assessment-questions')"
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
                                                    @click="bulkDelete('/admin/assessment-questions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>
                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-warning pr-3 pl-3"
                                                    @click="copy('/admin/assessment-questions/copy-questions')">Copy</button>
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

                                        <td data-title="id">
                                            <span
                                                v-if="getId() ==(pagination.state.current_page -1) * pagination.state.per_page + index+1 "
                                                style="background-color: yellow">@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                            <span v-else>@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</span>
                                        </td>

                                        <td data-title="assessment_id">@{{ item.assessment_with_trashed.assessment_title }}</td>
                                        <td data-title="question">@{{ item.question }}</td>
                                        <td data-title="option1">@{{ item.option1 }}</td>
                                        <td data-title="option2">@{{ item.option2 }}</td>
                                        <td data-title="option3">@{{ item.option3 }}</td>
                                        <td data-title="option4">@{{ item.option4 }}</td>
                                        <td data-title="correct_answer">@{{ item.correct_answer }}</td>
                                        <td data-title="order_index">@{{ item.order_index }}</td>
                                        <td data-title="category">@{{ item.category }}</td>
                                        <td data-title="created_by">@{{ item.assessment_with_trashed.user.first_name }}</td>

                                        <td class="action_buttons">
                                            <div v-if="({{ \Auth::user()->roles[0]['id'] }} == 3 && item.assessment_with_trashed.user.roles[0].id == 3 || {{ \Auth::user()->roles[0]['id'] }} == 10 && item.assessment_with_trashed.user.roles[0].id == 10 || {{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 10 )"
                                                class="row">
                                                @can('admin.assessment-question.edit')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-spinner btn-info"
                                                            :href="item.resource_url + '/edit'"
                                                            @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                            role="button"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.assessment-question.delete')
                                                    <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i
                                                                class="fa fa-trash-o"></i></button>
                                                    </form>
                                                @endcan
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
                                @can('admin.assessment-question.create')
                                    <a class="btn btn-primary btn-spinner"
                                        href="{{ url('admin/assessment-questions/create') }}" role="button"><i
                                            class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.assessment-question.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </assessment-question-listing>

@endsection
@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
