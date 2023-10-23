@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-keyword.actions.index'))

@section('body')

    <chat-keyword-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/chat-keywords') }}'"
        :session_search="'{{ $search }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.chat-keyword.actions.index') }}
                        @can('admin.chat-keyword.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/chat-keywords/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                                {{ trans('admin.chat-keyword.actions.create') }}</a>
                        @endcan
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

                                        <th is='sortable' data-title="id" :column="'id'" id="id1">
                                            {{ trans('admin.chat-keyword.columns.id') }}</th>
                                        <th is='sortable' data-title="title" :column="'title'">
                                            {{ trans('admin.chat-keyword.columns.title') }}</th>
                                        <th is='sortable' data-title="hit" :column="'hit'">
                                            {{ trans('admin.chat-keyword.columns.hit') }}</th>
                                        <th is='sortable' data-title="custom_ordering" :column="'custom_ordering'">
                                            {{ trans('admin.chat-keyword.columns.custom_ordering') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="7">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/chat-keywords')"
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
                                                    @click="bulkDelete('/admin/chat-keywords/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="title">@{{ item.title }}</td>
                                        <td data-title="hit">@{{ item.hit }}</td>
                                        <td data-title="custom_ordering"> @{{ item.custom_ordering }}</td>

                                        <td class="action_buttons">
                                            <div class="row">
                                                @can('admin.chat-keyword.edit')
                                                    <div class="col-auto">
                                                        <a class="btn btn-sm btn-spinner btn-info"
                                                            :href="item.resource_url + '/edit'"
                                                            @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)"
                                                            title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                            role="button"><i class="fa fa-edit"></i></a>
                                                    </div>
                                                @endcan
                                                @can('admin.chat-keyword.delete')
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


                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                @can('admin.chat-keyword.create')
                                    <a class="btn btn-primary btn-spinner" href="{{ url('admin/chat-keywords/create') }}"
                                        role="button"><i class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.chat-keyword.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-keyword-listing>

@endsection

@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
