@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.automatic-notification.actions.index'))

@section('body')

    <automatic-notification-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/automatic-notifications') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.automatic-notification.actions.index') }}
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2" href="{{ url('admin/automatic-notifications/export') }}" role="button"><i class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.automatic-notification.actions.export') }}</a>
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/automatic-notifications/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.automatic-notification.actions.create') }}</a> --}}
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
                                        <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th>

                                        <th is='sortable' data-title="id" :column="'id'">{{ trans('admin.automatic-notification.columns.id') }}</th>
                                        <th is='sortable' data-title="title" :column="'title'">{{ trans('admin.automatic-notification.columns.title') }}</th>
                                        <th is='sortable' data-title="description" :column="'description'">{{ trans('admin.automatic-notification.columns.description') }}</th>
                                        <th is='sortable' data-title="type" :column="'type'">{{ trans('admin.automatic-notification.columns.type') }}</th>
                                        <th is='sortable' data-title="created_by" :column="'created_by'">{{ trans('admin.automatic-notification.columns.created_by') }}</th>
                                        <th is='sortable' data-title="created_at" :column="'created_at'">{{ trans('admin.automatic-notification.columns.created_at') }}</th>
                                        <th is='sortable' data-title="successful_count" :column="'successful_count'">{{ trans('admin.automatic-notification.columns.successful_count') }}</th>
                                        <th is='sortable' data-title="failed_count" :column="'failed_count'">{{ trans('admin.automatic-notification.columns.failed_count') }}</th>
                                        <th is='sortable' data-title="status" :column="'status'">{{ trans('admin.automatic-notification.columns.status') }}</th>
                                        {{-- <th is='sortable' :column="'linking_url'">{{ trans('admin.automatic-notification.columns.linking_url') }}</th> --}}
                                        {{-- <th is='sortable' :column="'subscriber_id'">{{ trans('admin.automatic-notification.columns.subscriber_id') }}</th> --}}

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="8">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/automatic-notifications')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/automatic-notifications/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td>

                                        <td data-title="id" >@{{ item.id }}</td>
                                        <td data-title="title" >@{{ item.title }}</td>
                                        <td data-title="description" >@{{ item.description }}</td>
                                        <td data-title="type" >@{{ item.type }}</td>
                                        <td data-title="created_by" >@{{ item?.admin_user?.first_name }} @{{ item?.admin_user?.last_name }}</td>
                                        <td data-title="created_at" >@{{ item.created_at | moment  }}</td>
                                        <td data-title="successful_count" >@{{ item.successful_count  }}</td>
                                        <td data-title="failed_count" >@{{ item.failed_count  }}</td>
                                        <td data-title="status" >@{{ item.status  }}</td>
                                        {{-- <td>@{{ item.linking_url }}</td> --}}
                                        {{-- <td>@{{ item.subscriber_id }}</td> --}}
                                        <td></td>
                                        {{-- <td>
                                            <div class="row no-gutters">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            </div>
                                        </td> --}}
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
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/automatic-notifications/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.automatic-notification.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </automatic-notification-listing>

@endsection