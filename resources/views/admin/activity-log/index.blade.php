@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.activity-log.actions.index'))

@section('body')

    <activity-log-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/activity-logs') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.activity-log.actions.index') }}
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/activity-logs/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.activity-log.actions.create') }}</a> --}}
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

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        {{-- <th class="bulk-checkbox">
                                            <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll" v-validate="''" data-vv-name="enabled"  name="enabled_fake_element" @click="onBulkItemsClickedAllWithPagination()">
                                            <label class="form-check-label" for="enabled">
                                                #
                                            </label>
                                        </th> --}}

                                        <th width="5%" is='sortable'  data-title="id"  :column="'id'">{{ trans('admin.activity-log.columns.id') }}</th>
                                        <th width="10%" is='sortable' data-title="description"  :column="'description'">{{ trans('admin.activity-log.columns.description') }}</th>
                                        <th width="20%" is='sortable' data-title="subject_type"  :column="'subject_type'">{{ trans('admin.activity-log.columns.subject_type') }}</th>
                                        {{-- <th width="5%" is='sortable' :column="'subject_id'">{{ trans('admin.activity-log.columns.subject_id') }}</th> --}}
                                        {{-- <th is='sortable' :column="'causer_type'">{{ trans('admin.activity-log.columns.causer_type') }}</th> --}}
                                        <th width="10%" is='sortable' data-title="causer_id"  :column="'causer_id'">{{ trans('admin.activity-log.columns.causer_id') }}</th>
                                        <th width="10%" is='sortable' data-title="created_at"  :column="'created_at'">{{ trans('admin.activity-log.columns.created_at') }}</th>
                                        {{-- <th width="50%" is='sortable' :column="'properties'">{{ trans('admin.activity-log.columns.properties') }}</th> --}}

                                        <th></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="9">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/activity-logs')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/activity-logs/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        {{-- <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td> --}}

                                        <td data-title="id">@{{ item.id }}</td>
                                        <td data-title="description">
                                            <span v-if="'deleted' === item.description" class="badge badge-danger m-1 p-1" style="font-size:0.8rem">Deleted</span>
                                            <span v-else-if="'updated' == item.description">
                                                <span class="badge badge-warning m-1 p-1" style="font-size:0.8rem;color:white">@{{ item.description }}</span>
                                            </span>
                                            <span v-else>
                                                <span class="badge badge-success m-1 p-1" style="font-size:0.8rem;color:white">@{{ item.description }}</span>
                                            </span>
                                        </td>
                                        <td data-title="subject_type">@{{ item.subject_type }}</td>
                                        {{-- <td>@{{ item.subject_id }}</td> --}}
                                        {{-- <td>@{{ item.causer_type }}</td> --}}
                                        <td data-title="causer_id"  >@{{ item.admin_user ? item.admin_user.first_name:'' }}</td>
                                        <td data-title="created_at" >@{{ item.created_at | moment }}</td>
                                        {{-- <td>@{{ item.properties }}</td> --}}
                                        <td>
                                            <button class="btn btn-sm btn-primary" id="info" title="info"
                                                @click="activityLogs(item)"><i class="fa fa-info"></i>
                                            </button>
                                        </td>
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
                            <div class="modal fade" id="payload_details" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel-2" aria-hidden="true">
                                <div class="modal-dialog" style="max-width: 40%;" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel-2"><span
                                                    id="op_title">Payload</span></h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" id="payload_data"></div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/activity-logs/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.activity-log.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </activity-log-listing>

@endsection