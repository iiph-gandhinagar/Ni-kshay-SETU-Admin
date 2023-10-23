@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-app-version.actions.index'))

@section('body')

    <user-app-version-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/user-app-versions') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.user-app-version.actions.index') }}
                        <p style="float:right;color:black">Subscriber Count(Who not Login) - {{ $subscriber_count}}</p>
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/user-app-versions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-app-version.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
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

                                        <th is='sortable' data-title="id" :column="'id'">{{ trans('admin.user-app-version.columns.id') }}</th>
                                        <th is='sortable' data-title="app_version" :column="'app_version'">{{ trans('admin.user-app-version.columns.app_version') }}</th>
                                        <th is='sortable' data-title="current_plateform" :column="'current_plateform'">{{ trans('admin.user-app-version.columns.current_plateform') }}</th>
                                        <th is='sortable' data-title="user_id" :column="'user_id'">User Count</th>

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="6">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/user-app-versions')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/user-app-versions/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td data-title="id">@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td data-title="app_version">@{{ item.app_version }}</td>
                                        <td data-title="current_plateform">@{{ item.current_plateform }}</td>
                                        <td data-title="user_id">@{{ item.user_count }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/user-app-versions/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-app-version.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-app-version-listing>

@endsection