@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.subscriber-activity.actions.index'))

@section('body')

    <subscriber-activity-listing :data="{{ $data->toJson() }}" :subscribers="{{ json_encode($subscriber) }}"
        :country="{{ json_encode($country) }}" :url="'{{ url('admin/subscriber-activities?date=' . $date) }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.subscriber-activity.actions.index') }}
                        @can('admin.subscriber-activity.export')
                            <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                                :href="'/admin/subscriber-activities/export?subscriber_id=' + form.select_subscriber +
                                    '&plateform=' + form.select_plateform + '&action=' + form.select_action +
                                    '&cadre_id=' + form.select_cadre + '&state_id=' + form.select_state +
                                    '&todayDate=' + form.from_date + '&country_id=' + form.select_country"
                                role="button"><i class="fa fa-file-excel-o"></i>&nbsp;
                                {{ trans('admin.subscriber-activity.actions.export') }}</a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/subscriber-activities/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.subscriber-activity.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_subscriber" id="subscriber_id"
                                        name="subscriber_id" placeholder="Select Subscriber" @search-change="asyncFind"
                                        :options="form.subscriber_data.map(type => type.id)"
                                        :custom-label="opt => form.subscriber_data.find(x => x.id == opt).name"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('subscriber_id', form.select_subscriber)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_action" id="action"
                                        name="action" placeholder="Select Action"
                                        :options="{{ $action }}.map(type => type.action)"
                                        :custom-label="opt => {{ $action }}.find(x => x.action == opt).action"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('action', form.select_action)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_plateform" id="plateform"
                                        name="plateform" placeholder="Select Plateform"
                                        :options="{{ $plateform }}.map(type => type.plateform)"
                                        :custom-label="opt => {{ $plateform }}.find(x => x.plateform == opt).plateform"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('plateform', form.select_plateform)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_cadre" id="cadre_id"
                                        name="cadre_id" placeholder="Select Cadre"
                                        :options="{{ $cadre }}.map(type => type.id)"
                                        :custom-label="opt => {{ $cadre }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('cadre_id', form.select_cadre)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_country" id="country_id"
                                        name="country_id" placeholder="Select Country"
                                        :options="{{ $country }}.map(type => type.id)"
                                        :custom-label="opt => {{ $country }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('country_id', form.select_country)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_state" id="state_id"
                                        name="state_id" placeholder="Select State"
                                        :options="{{ $state }}.map(type => type.id)"
                                        :custom-label="opt => {{ $state }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('state_id', form.select_state)">
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

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span
                                        class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%" is='sortable' data-title="id" :column="'id'">
                                            {{ trans('admin.subscriber-activity.columns.id') }}</th>
                                        <th width="10%" is='sortable'data-title="user_id" :column="'user_id'">
                                            {{ trans('admin.subscriber-activity.columns.user_id') }}</th>
                                        <th width="10%" is='sortable'data-title="cadre" :column="'cadre'">Cadre
                                        </th>
                                        <th is='sortable' data-title="country" :column="'country'">Country</th>
                                        <th width="10%" is='sortable' data-title="state" :column="'state'">State
                                        </th>
                                        <th width="30%" is='sortable' data-title="action" column="'action'">
                                            {{ trans('admin.subscriber-activity.columns.action') }} </th>
                                        {{-- --}}
                                        <th width="5%" is='sortable' data-title="ip_address" :column="'ip_address'">
                                            {{ trans('admin.subscriber-activity.columns.ip_address') }}</th>
                                        <th width="5%" is='sortable' data-title="plateform" :column="'plateform'">
                                            Plateform</th>
                                        <th width="10%" is='sortable' data-title="created_at"
                                            :column="'created_at'">Activity Date</th>
                                        <th width="5%"></th>

                                    </tr>

                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td data-title="id">@{{ (pagination.state.current_page - 1) * pagination.state.per_page + index + 1 }}</td>
                                        <td data-title="user_id">@{{ item.user.name }}</td>
                                        <td data-title="cadre">@{{ item.user.cadre.title }}</td>
                                        <td data-title="country">@{{ item.user.country && item.user.country.title ? item.user.country.title : '' }}</td>
                                        <td data-title="state">@{{ item.user.state && item.user.state.title ? item.user.state.title : '' }}</td>
                                        <td data-title="action">@{{ item.action }}</td>
                                        <td data-title="ip_address">@{{ item.ip_address }}</td>
                                        <td data-title="plateform">@{{ item.plateform }}</td>
                                        <td data-title="created_at">@{{ item.created_at | moment }}</td>
                                        <td></td>

                                    </tr>
                                </tbody>
                            </table>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/subscriber-activities/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.subscriber-activity.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </subscriber-activity-listing>

@endsection
