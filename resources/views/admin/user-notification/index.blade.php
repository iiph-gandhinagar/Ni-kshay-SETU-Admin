@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-notification.actions.index'))

@section('body')

    <user-notification-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/user-notifications') }}'"
        :all_subscriber="{{ json_encode($subscriber) }}" :all_cadres="{{ json_encode($all_cadres) }}"
        :all_states="{{ json_encode($state) }}" :assessment="{{ json_encode($assessment) }}"
        :resource_material="{{ json_encode($resource_material) }}" :case_definition="{{ json_encode($case_definition) }}"
        :dignosis_algo="{{ json_encode($dignosis_algo) }}" :treatment_algo="{{ json_encode($treatment_algo) }}"
        :guidance_on_adr="{{ json_encode($guidance_on_adr) }}"
        :latent_tb_infection="{{ json_encode($latent_tb_infection) }}"
        :differential_care_algo="{{ json_encode($differential_care_algo) }}" :cgc_algo="{{ json_encode($cgc_algo) }}"
        :dynamic_algo_master="{{ json_encode($dynamic_algo_master) }}" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.user-notification.actions.index') }}
                        @can('admin.user-notification.create')
                            <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                                href="{{ url('admin/user-notifications/create') }}" role="button"><i
                                    class="fa fa-plus"></i>&nbsp; {{ trans('admin.user-notification.actions.create') }}</a>
                        @endcan
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

                                        <th width="5%" is='sortable' data-title="id" :column="'id'">
                                            {{ trans('admin.user-notification.columns.id') }}</th>
                                        <th width="25%" is='sortable' data-title="title" :column="'title'">
                                            {{ trans('admin.user-notification.columns.title') }}</th>
                                        <th width="25%" is='sortable' data-title="description" :column="'description'">
                                            {{ trans('admin.user-notification.columns.description') }}</th>
                                        <th wisth="5%" is='sortable' data-title="type" :column="'type'">
                                            {{ trans('admin.user-notification.columns.type') }}</th>
                                        <th width="5%" is='sortable' data-title="user_id" :column="'user_id'">
                                            {{ trans('admin.user-notification.columns.user_id') }}</th>
                                        <th width="15%" is='sortable' data-title="state_id" :column="'state_id'">
                                            {{ trans('admin.user-notification.columns.state_id') }}</th>
                                        <th width="5%" is='sortable' data-title="cadre_type" :column="'cadre_type'">
                                            {{ trans('admin.user-notification.columns.cadre_type') }}</th>
                                        <th width="5%" is='sortable' data-title="cadre_id" :column="'cadre_id'">
                                            {{ trans('admin.user-notification.columns.cadre_id') }}</th>
                                        <th width="5%" is='sortable' data-title="created_by" :column="'created_by'">
                                            {{ trans('admin.user-notification.columns.created_by') }}</th>
                                        <th width="10%" is='sortable' data-title="Notification Date"
                                            :column="'Notification Date'">Notification Date</th>

                                        <th></th>
                                    </tr>
                                    {{-- <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="9">
                                            <span class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }} @{{ clickedBulkItemsCount }}.  <a href="#" class="text-primary" @click="onBulkItemsClickedAll('/admin/user-notifications')" v-if="(clickedBulkItemsCount < pagination.state.total)"> <i class="fa" :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i> {{ trans('brackets/admin-ui::admin.listing.check_all_items') }} @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a
                                                        href="#" class="text-primary" @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>  </span>

                                            <span class="pull-right pr-2">
                                                <button class="btn btn-sm btn-danger pr-3 pl-3" @click="bulkDelete('/admin/user-notifications/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
                                            </span>

                                        </td>
                                    </tr> --}}
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        {{-- <td class="bulk-checkbox">
                                            <input class="form-check-input" :id="'enabled' + item.id" type="checkbox" v-model="bulkItems[item.id]" v-validate="''" :data-vv-name="'enabled' + item.id"  :name="'enabled' + item.id + '_fake_element'" @click="onBulkItemClicked(item.id)" :disabled="bulkCheckingAllLoader">
                                            <label class="form-check-label" :for="'enabled' + item.id">
                                            </label>
                                        </td> --}}

                                        <td data-title="id">@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</td>
                                        <td data-title="title">@{{ item.title }}</td>{{-- <td><div v-bind:title=item.description>@{{ item.title }} </div></td> --}}
                                        <td data-title="description">
                                            <div v-bind:title=item.description>@{{ item.description | stringCount }}</div>
                                        </td>
                                        <td data-title="type">@{{ item.type }}</td>
                                        <td data-title="user_id">
                                            <span v-if="item.type === 'public'" class="badge badge-danger m-1 p-1"
                                                style="font-size:0.8rem">All Users</span>
                                            <span v-else-if="item.type === 'user-specific'">

                                                <span v-if="getSubscriberCounts(item.user_id) <=5">

                                                    <span v-for="(subscriber,j) in getSubscriberNamesByIds(item)"
                                                        id="user-badges">
                                                        <span class="badge badge-success m-1 p-1"
                                                            style="font-size:0.8rem;color:white">@{{ subscriber }}</span>
                                                    </span>
                                                </span>
                                                <span v-else class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:white">Selected Users</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
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
                                        <td data-title="state_id">
                                            <span v-for="(state,j) in getStateNamesByIds(item)">
                                                <span class="badge badge-info m-1 p-1"
                                                    style="color:white">@{{ state }}</span>
                                            </span>
                                        </td>
                                        <td data-title="cadre_type">@{{ item.cadre_type }}</td>
                                        <td data-title="cadre_id">
                                            <span v-if="item.cadre_id == null ">
                                                --
                                            </span>
                                            <span v-else-if="all_cadres.length ==item.cadre_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Cadres</span>
                                            <span v-else-if="item.cadre_id.length != 0">
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected Cadres</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="created_by">@{{ item?.admin_user?.first_name }} @{{ item?.admin_user?.last_name }}</td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" id="info" title="info"
                                                @click="userNotification(item)"><i class="fa fa-info"></i>
                                            </button>
                                        </td>
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
                                @can('admin.user-notification.create')
                                    <a class="btn btn-primary btn-spinner"
                                        href="{{ url('admin/user-notifications/create') }}" role="button"><i
                                            class="fa fa-plus"></i>&nbsp;
                                        {{ trans('admin.user-notification.actions.create') }}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </user-notification-listing>

@endsection
