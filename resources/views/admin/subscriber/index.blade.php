@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.subscriber.actions.index'))

@section('body')

    <subscriber-listing 
        :data="{{ $data->toJson() }}" 
        :district="{{ json_encode($district) }}"
        :all_blocks="{{ json_encode($block) }}"
        :health_facility="{{ json_encode($health_facility) }}"
        :session_search="'{{ $search }}'"
        :url="'{{ url('admin/subscribers?from_date='.$from_date.'&to_date='.$to_date.'&state_id='.$state_id.'&block_id='.$block_id) }}'" {{-- ?from_date='.$from_date.'&to_date='.$to_date.'&state_id='.$state_id.'&district_id='.$district_id.'&block_id='.$block_id --}}
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.subscriber.actions.index') }}
                        @can('admin.subscriber.export')
                            <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2"
                            :href="'/admin/subscribers/export?cadre_id=' + form.select_cadre + '&state_id=' + form.select_state + '&district_id=' + form.select_district + '&block_id=' + form.select_block + '&health_facility_id=' + form.select_health_facility + '&app_version='+ form.select_user_app_version + '&from_date=' + form.from_date + '&to_date=' + form.to_date + '&todayDate=' + form.from_date"
                            role="button"><i class="fa fa-file-excel-o"></i>&nbsp;
                            {{ trans('admin.subscriber.actions.export') }}</a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/subscribers/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.subscriber.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <!--col-md-12-->

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_state" id="state_id"
                                        name="state_id" placeholder="Select State"
                                        :options="{{ $state }}.map(type => type.id)"
                                        :custom-label="opt => {{ $state }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('state_id', form.select_state);getStateDistrict();">
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

                                    <multiselect :searchable="true" v-model="form.select_district" id="district_id"
                                        name="district_id" placeholder="Select District"
                                        :options="form.district.map(type => type.id)"
                                        :custom-label="opt => form.district.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="true"
                                        @input="filter('district_id', form.select_district);getDistrictBlock()">
                                    </multiselect>

                                </div>
                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_block" id="block_id"
                                        name="block_id" placeholder="Select Taluka"
                                        :options="form.block.map(type => type.id)"
                                        :custom-label="opt => form.block.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('block_id', form.select_block);getBlockHealthFacility()">
                                    </multiselect>

                                </div>
                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_health_facility"
                                        id="health_facility_id" name="health_facility_id"
                                        placeholder="Select Health Facility"
                                        :options="form.health_facility.map(type => type.id)"
                                        :custom-label="opt => form.health_facility.find(x => x.id == opt).health_facility_code"
                                        open-direction="auto" :multiple="false"
                                        @input="filter('health_facility_id', form.select_health_facility)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">

                                    <multiselect :searchable="true" v-model="form.select_user_app_version"
                                        id="user_app_version" name="user_app_version"
                                        placeholder="Select App Version"
                                        :options="{{ $app_version }}.map(type => type.app_version)"
                                        {{-- :custom-label="opt => form.user_app_version.find(x => x.id == opt).health_facility_code" --}}
                                        open-direction="auto" :multiple="false"
                                        @input="filter('user_app_version', form.select_user_app_version)">
                                    </multiselect>
                                </div>

                                <div class="col-md-3 form-group">
                                    <datetime v-model="form.from_date" class="flatpickr" id="from_date" name="from_date" placeholder="Select From Date" @input="filter('from_date', form.from_date)"></datetime>{{-- v-validate="'date_format:yyyy-MM-dd h:mm:ss'"  --}}
                                </div>

                                <div class="col-md-3 form-group">
                                    <datetime v-model="form.to_date" class="flatpickr" value="form.to_date" id="to_date" name="to_date" placeholder="Select To date" @input="filter('to_date', form.to_date)"></datetime>{{-- v-validate="'date_format:yyyy-MM-dd h:mm:ss'"  --}}
                                </div>
                            </div>
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}"
                                                v-model="search" id="search_field" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary"
                                                    @click="filter('search', search); getSerchFilter(1)"><i class="fa fa-search"></i>&nbsp;
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

                                        <th is='sortable' width="5%" data-title="id" :column="'id'" id="id1">{{ trans('admin.subscriber.columns.id') }}</th>
                                        <th is='sortable' width="10%"data-title="name"  :column="'name'">{{ trans('admin.subscriber.columns.name') }}
                                        </th>
                                        <th is='sortable' width="10%" data-title="phone_no" :column="'phone_no'">
                                            {{ trans('admin.subscriber.columns.phone_no') }}</th>
                                        <th is='sortable' width="10%" data-title="cadre_type" :column="'cadre_type'">
                                            {{ trans('admin.subscriber.columns.cadre_type') }}</th>
                                        <th is='sortable' width="5%" data-title="is_verified" :column="'is_verified'">SMS Verified</th>
                                        <th is='sortable' width="10%" data-title="cadre_id" :column="'cadre_id'">
                                            {{ trans('admin.subscriber.columns.cadre_id') }}</th>
                                        <th>Country</th>
                                        <th is='sortable' width="5%" data-title="block_id" :column="'block_id'">
                                            {{ trans('admin.subscriber.columns.block_id') }}</th>
                                        <th is='sortable' width="5%" data-title="district_id" :column="'district_id'">
                                            {{ trans('admin.subscriber.columns.district_id') }}</th>
                                        <th is='sortable' width="10%" data-title="state_id" :column="'state_id'">
                                            {{ trans('admin.subscriber.columns.state_id') }}</th>
                                        <th is='sortable' width="5%" data-title="health_facility_id" :column="'health_facility_id'">
                                            {{ trans('admin.subscriber.columns.health_facility_id') }}</th>
                                        <th is='sortable' width="10%" data-title="created_at" :column="'created_at'">Registration Date</th>
                                        <th is='sortable' width="5%" data-title="user_app_version" :column="'user_app_version'">User App Version</th>
                                        <th is='sortable' width="5%" data-title="forgot_otp_time" :column="'forgot_otp_time'">Messaged Date</th>
                                        {{-- <th is='sortable' width="5%" :column="'subscriber_activity_count'">No. of Visits</th> --}}

                                        <th width="5%"  data-title="action"></th>
                                    </tr>

                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                       <td  data-title="id">
                                            <span v-if="getId() ==(pagination.state.current_page -1) * pagination.state.per_page + index+1 " style="background-color: yellow">@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</span>
                                            <span v-else>@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</span>
                                       </td>
                                       <td data-title="name">@{{ item . name }} @{{ pagination.state.search }}</td>
                                        <td data-title="phone_no">@{{ item . phone_no }}</td>
                                        <td  data-title="cadre_type">@{{ item . cadre_type }}</td>
                                        <td  data-title="is_verified">
                                            <span v-if="item.is_verified == 1" class="badge badge-success m-1 p-1"
                                                style="font-size:0.8rem;color:white">Yes</span>
                                            <span v-else>

                                                <span class="badge badge-danger m-1 p-1" style="font-size:0.8rem;">No</span>

                                            </span>
                                        </td>
                                        <td data-title="cadre_id">@{{ item . cadre && item . cadre . title ? item . cadre . title : '' }}</td>
                                        <td data-title="country_id">@{{ item . country && item . country . title ? item . country . title : '' }}</td>
                                        <td data-title="block_id">@{{ item . block && item . block . title ? item . block . title : '' }}</td>
                                        <td data-title="district_id">@{{ item . district && item . district . title ? item . district . title : '' }}
                                        </td>
                                        <td data-title="state_id">@{{ item . state && item . state . title ? item . state . title : '' }}</td>
                                        <td data-title="health_facility_id">@{{ item . health_facility && item . health_facility . health_facility_code ? item . health_facility . health_facility_code : '' }}
                                        </td>
                                        <td data-title="created_at" >@{{ (item . created_at) | moment }}</td>
                                        <td data-title="user_app_version">@{{ item . user_app_version && item . user_app_version . app_version ? item . user_app_version . app_version : '' }}</td>
                                        {{-- <td>@{{ item.subscriber_activities_count ? item.subscriber_activities_count : ''}}</td> --}}
                                        <td>
                                            <span v-if="item.forgot_otp_time != '' && item.forgot_otp_time != null">@{{ item.forgot_otp_time | moment}}</span>
                                            <span v-else-if="item.forgot_otp_time == ''">--</span>
                                        </td>
                                        <td style="align-items: center;justify-content: center;display:flex">
                                            @can('admin.subscriber.edit')
                                            <div class="col">
                                                <a class="btn btn-sm btn-info" :href="item.resource_url + '/edit'" @click="idStore((pagination.state.current_page -1) * pagination.state.per_page + index+1)" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                            </div>
                                            @endcan

                                            <button class="btn btn-sm btn-warning" id="info" title="Leader Board"
                                                @click="leaderBoardDetails(item)"><i class="fa fa-bar-chart"></i>
                                            </button>

                                            <form class="col" @click="sendOtp(item.resource_url + '/sendOtp')"
                                                onsubmit="return false">
                                                {{-- <a class="btn btn-sm btn-spinner btn-warning" :href="item.resource_url + '/copy'" title="{{ trans('brackets/admin-ui::admin.btn.copy') }}" role="button"><i class="fa fa-copy"></i></a>onclick="return confirm('Are you sure you want to Copy this Assessment?');" --}}
                                                <button type="submit" class="btn btn-sm btn-spinner btn-danger"
                                                    title="Forgot Otp"><i class="fa fa-info"></i></button>
                                            </form>
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
                                                    id="op_title">Leader Board</span></h5>
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
                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/subscribers/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.subscriber.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </subscriber-listing>

@endsection

@section('bottom-scripts')
    @include('admin.script-element-pagination')
@endsection

