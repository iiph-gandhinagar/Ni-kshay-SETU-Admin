@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.dynamic-algorithm.actions.index'))

@section('body')

    <dynamic-algorithm-listing :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/dynamic-algorithms') }}?key={{ app('request')->input('key') }}&master={{ app('request')->input('master') }}&master_node_id={{ request()->query('master_node_id') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ $algoTitle }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/dynamic-algorithms/create') }}?key={{ app('request')->input('key') }}&master={{ app('request')->input('master') }}&master_node_id={{ app('request')->input('master_node_id') }}"
                            role="button"><i class="fa fa-plus"></i>&nbsp; New {{ $algoTitle }}</a>
                        <a class="btn btn-danger btn-spinner btn-sm pull-right m-b-0 mr-1" href="#"
                            onclick="window.history.go(-1)" role="button"><i class="fa fa-arrow-left"></i>&nbsp;
                            {{ trans('admin.dynamic-algorithm.actions.back') }}
                        </a>
                    </div>
                    @if (isset($message) && $message != '')
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert" @click="clearSession()">×</button>
                            <strong>{{ $message ?? '' }}</strong>
                        </div>
                    @endif
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

                                        <th is='sortable' data-title="id" :column="'id'">
                                            {{ trans('admin.dynamic-algorithm.columns.id') }}</th>
                                        {{-- <th is='sortable' :column="'algo_key'">{{ trans('admin.dynamic-algorithm.columns.algo_key') }}</th> --}}
                                        <th is='sortable' data-title="title" :column="'title'">
                                            {{ trans('admin.dynamic-algorithm.columns.title') }}</th>
                                        <th is='sortable' data-title="node_type" :column="'node_type'">
                                            {{ trans('admin.dynamic-algorithm.columns.node_type') }}</th>
                                        <th is='sortable' data-title="is_expandable" :column="'is_expandable'">
                                            {{ trans('admin.dynamic-algorithm.columns.is_expandable') }}</th>
                                        <th is='sortable' data-title="has_options" :column="'has_options'">
                                            {{ trans('admin.dynamic-algorithm.columns.has_options') }}</th>
                                        <th is='sortable' data-title="parent_id" :column="'parent_id'">
                                            {{ trans('admin.dynamic-algorithm.columns.parent_id') }}</th>
                                        <th is='sortable' data-title="index" :column="'index'">
                                            {{ trans('admin.dynamic-algorithm.columns.index') }}</th>
                                        <th is='sortable' data-title="activated" :column="'activated'">
                                            {{ trans('admin.dynamic-algorithm.columns.activated') }}</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="10">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/dynamic-algorithms')"
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
                                                    @click="bulkDelete('/admin/dynamic-algorithms/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                            <span v-if="getId() == index + 1 "
                                                style="background-color: yellow">@{{ index + 1 }}</span>
                                            <span v-else>@{{ index + 1 }}</span>
                                        </td>
                                        {{-- <td>@{{ item.algo_key }}</td> --}}
                                        <td data-title="title">
                                            <span
                                                v-if="item.node_type == 'Linking Node' || item.node_type == 'Linking Node Without Options'">
                                                <span v-if="item.parent_id == 0">
                                                    <a
                                                        :href="'/admin/dynamic-algorithms?key=' + item.algo_key + '&master=' +
                                                            item.id + '&master_node_id=' + item.id">@{{ item.title }}</a>
                                                </span>
                                                <span v-else>
                                                    <a
                                                        :href="'/admin/dynamic-algorithms?key=' + item.algo_key + '&master=' +
                                                            item.id + '&master_node_id=' + item.master_node_id">@{{ item.title }}</a>
                                                </span>
                                            </span>
                                            <span v-else>
                                                @{{ item.title }}
                                            </span>
                                        </td>
                                        <td data-title="node_type">@{{ item.node_type }}</td>
                                        <td data-title="is_expandable">@{{ item.is_expandable }}</td>
                                        <td data-title="has_options">@{{ item.has_options }}</td>
                                        <td data-title="parent_id">@{{ item.parent_id }}</td>
                                        <td data-title="index">@{{ item.index }}</td>
                                        <td data-title="activated">@{{ item.activated }}</td>
                                        {{-- <td>
                                            <label class="switch switch-3d switch-success">
                                                <input type="checkbox" class="switch-input" v-model="collection[index].activated" @change="toggleSwitch(item.resource_url, 'activated', collection[index])">
                                                <span class="switch-slider"></span>
                                            </label>
                                        </td> --}}
                                        <td class="action_buttons">
                                            <div class="row">
                                                <a v-if="item.parent_id == 0" class="btn btn-sm btn-dark"
                                                    :href="item.resource_url + '/send-initial-invitation'"
                                                    title="Send Initial Invitation" role="button"><i
                                                        class="fa fa-bell-o"></i></a>
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
                                    href="{{ url('admin/dynamic-algorithms/create') }}?key={{ app('request')->input('key') }}&master={{ app('request')->input('master') }}&master_node_id={{ app('request')->input('master_node_id') }}"
                                    role="button"><i class="fa fa-plus"></i>&nbsp; New {{ $algoTitle }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </dynamic-algorithm-listing>

@endsection

@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection
