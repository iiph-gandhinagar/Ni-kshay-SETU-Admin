@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.survey-master.actions.index'))

@section('body')

    <survey-master-listing :data="{{ $data->toJson() }}" :all_cadres="{{ json_encode($all_cadres) }}"
        :all_states="{{ json_encode($state) }}" :all_districts="{{ json_encode($districts) }}"
        :survey_question="{{ json_encode($survey_question) }}" :url="'{{ url('admin/survey-masters') }}'" inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.survey-master.actions.index') }}
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                            href="{{ url('admin/survey-masters/export') }}" role="button"><i
                                class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.survey-master.actions.export') }}</a>
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('admin/survey-masters/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp;
                            {{ trans('admin.survey-master.actions.create') }}</a>
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

                                        <th is='sortable' width="5%" data-title="id" :column="'id'">
                                            {{ trans('admin.survey-master.columns.id') }}</th>
                                        <th is='sortable' width="15%" data-title="title" :column="'title'">
                                            {{ trans('admin.survey-master.columns.title') }}</th>
                                        <th is='sortable' width="5%" data-title="country_id" :column="'country_id'">
                                            {{ trans('admin.survey-master.columns.country_id') }}</th>
                                        <th is='sortable' width="5%" data-title="cadre_id" :column="'cadre_id'">
                                            {{ trans('admin.survey-master.columns.cadre_id') }}</th>
                                        <th is='sortable' width="10%" data-title="state_id" :column="'state_id'">
                                            {{ trans('admin.survey-master.columns.state_id') }}</th>
                                        <th is='sortable' width="5%" data-title="district_id" :column="'district_id'">
                                            {{ trans('admin.survey-master.columns.district_id') }}</th>
                                        <th is='sortable' width="5%" data-title="cadre_type" :column="'cadre_type'">
                                            {{ trans('admin.survey-master.columns.cadre_type') }}</th>
                                        <th is='sortable' width="15%" data-title="order_index" :column="'order_index'">
                                            {{ trans('admin.survey-master.columns.order_index') }}</th>
                                        <th is='sortable' width="5%" data-title="active" :column="'active'">
                                            {{ trans('admin.survey-master.columns.active') }}</th>
                                        <th is='sortable' width="15%" data-title="created_at" :column="'created_at'">
                                            {{ trans('admin.survey-master.columns.created_at') }}</th>

                                        <th width="15%"></th>
                                    </tr>
                                    <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                        <td class="bg-bulk-info d-table-cell text-center" colspan="11">
                                            <span
                                                class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                                @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                    @click="onBulkItemsClickedAll('/admin/survey-masters')"
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
                                                    @click="bulkDelete('/admin/survey-masters/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                        <td data-title="title">@{{ item.title }}</td>
                                        <td data-title="country_id">@{{ item.country_id }}</td>
                                        {{-- <td>@{{ item.cadre_id }}</td> --}}
                                        <td data-title="cadre_id">
                                            <span v-if="all_cadres.length === item.cadre_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Cadres</span>
                                            <span v-else-if="item.cadre_id.length != 0">
                                                {{-- <span v-for="(cadre,i) in getCadreNamesByIds(item)">
                                                    <span class="badge badge-warning m-1 p-1">@{{ cadre }}</span>
                                                </span> --}}
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected Cadres</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="state_id"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 4">
                                            <span v-if="all_states.length === item.state_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                States</span>
                                            <span v-else-if="item.state_id.split(',').length != 0">
                                                {{-- <span v-for="(state,j) in getStateNamesByIds(item)">
                                                    <span class="badge badge-info m-1 p-1" style="color:white">@{{ state }}</span>
                                                </span> --}}
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected States</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="district_id"
                                            v-if="{{ \Auth::user()->roles[0]['id'] }} != 3 && {{ \Auth::user()->roles[0]['id'] }} != 4">
                                            <span v-if="all_districts.length === item.district_id.split(',').length"
                                                class="badge badge-danger m-1 p-1" style="font-size:0.8rem">All
                                                Districts</span>
                                            <span
                                                v-else-if="item.district_id.split(',').length != 0 && item.district_id != '' ">
                                                <span class="badge badge-warning m-1 p-1"
                                                    style="font-size:0.8rem;color:black">Selected Districts</span>
                                            </span>
                                            <span v-else>
                                                --
                                            </span>
                                        </td>
                                        <td data-title="cadre_type">@{{ item.cadre_type }}</td>
                                        <td data-title="order_index">@{{ item.order_index }}</td>
                                        <td data-title="active">@{{ item.active }}</td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>

                                        <td class="action_buttons">
                                            <div class="row">
                                                <a class="btn btn-sm btn-dark"
                                                    :href="item.resource_url + '/send-initial-invitation'"
                                                    title="Send Initial Invitation" role="button"><i
                                                        class="fa fa-bell-o"></i></a>
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info"
                                                        :href="item.resource_url + '/edit'"
                                                        title="{{ trans('brackets/admin-ui::admin.btn.edit') }}"
                                                        role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col"
                                                    @submit.prevent="deleteItem(item.resource_url,item.id)">
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
                                <a class="btn btn-primary btn-spinner" href="{{ url('admin/survey-masters/create') }}"
                                    role="button"><i class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.survey-master.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </survey-master-listing>

@endsection
