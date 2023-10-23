@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.t-training-tag.actions.index'))

@section('body')

    <t-training-tag-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/t-training-tags') }}'"
        :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.t-training-tag.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/t-training-tags/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                            {{ trans('admin.t-training-tag.actions.create') }}</a>
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
                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>
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

                                        <th is='sortable' width="5%" data-title="id" :column="'id'"
                                            id="id">{{ trans('admin.t-training-tag.columns.id') }}</th>
                                        <th is='sortable' width="25%" data-title="tag" :column="'tag'">
                                            {{ trans('admin.t-training-tag.columns.tag') }}</th>
                                        <th is='sortable' width="5%" data-title="is_fix_response"
                                            :column="'is_fix_response'">
                                            {{ trans('admin.t-training-tag.columns.is_fix_response') }}</th>
                                        <th is='sortable' width="20%" data-title="updated_at" :column="'updated_at'">
                                            {{ trans('admin.t-training-tag.columns.updated_at') }}</th>
                                        <th is='sortable' width="5%" data-title="like_count" :column="'like_count'">
                                            {{ trans('admin.t-training-tag.columns.like_count') }}</th>
                                        <th is='sortable' width="5%" data-title="dislike_count"
                                            :column="'dislike_count'">
                                            {{ trans('admin.t-training-tag.columns.dislike_count') }}</th>
                                        <th is='sortable' width="5%" data-title="pattern" :column="'pattern'">
                                            Pattern Count</th>
                                        <th is='sortable' width="5%" data-title="Question" :column="'Question'">
                                            Question Count</th>

                                        <th width="20%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="8">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/t-training-tags')"
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
                                                    @click="bulkDelete('/admin/t-training-tags/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="tag">@{{ item.tag }}</td>
                                        <td data-title="is_fix_response">@{{ item.is_fix_response }}</td>
                                        <td data-title="updated_at">@{{ item.updated_at | moment }}</td>
                                        <td data-title="like_count">@{{ item.like_count }}</td>
                                        <td data-title="dislike_count">@{{ item.dislike_count }}</td>
                                        <td data-title="pattern">@{{ item.PatternsInTag }}</td>
                                        <td data-title="Question">@{{ item.QuestionsInTag }}</td>
                                        <td class="action_buttons">
                                            <div class="row">
                                                <form class="col" @click="copyTag(item.resource_url + '/copy')"
                                                    onsubmit="return false">
                                                    {{-- <a class="btn btn-sm btn-spinner btn-warning" :href="item.resource_url + '/copy'" title="{{ trans('brackets/admin-ui::admin.btn.copy') }}" role="button"><i class="fa fa-copy"></i></a>onclick="return confirm('Are you sure you want to Copy this Assessment?');" --}}
                                                    <button type="submit" class="btn btn-sm btn-warning"
                                                        title="Copy Tag"><i class="fa fa-copy"></i></button>
                                                </form>
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info"
                                                        :href="item.resource_url + '/edit'"
                                                        @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
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


                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/t-training-tags/create') }}"
                                    role="button"><i class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.t-training-tag.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </t-training-tag-listing>

@endsection

@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
