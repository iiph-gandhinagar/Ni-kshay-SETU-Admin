@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-keyword-hit.actions.index'))

@section('body')

    <chat-keyword-hit-listing
        :data="{{ $data->toJson() }}"
        :district="{{ json_encode($district) }}"
        :all_blocks="{{ json_encode($block) }}"
        :health_facility="{{ json_encode($health_facility) }}"
        :subscribers="{{ json_encode($subscriber) }}"
        :country="{{ json_encode($country) }}"
        :url="'{{ url('admin/chat-keyword-hits') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.chat-keyword-hit.actions.index') }}
                        @can('admin.chat-keyword-hit.export')
                        <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2" :href="'/admin/chat-keyword-hits/export?subscriber_id=' + form.select_subscriber + '&cadre_id=' + form.select_cadre + '&state_id=' + form.select_state + '&district_id=' + form.select_district + '&block_id=' + form.select_block + '&health_facility_id=' + form.select_health_facility + '&country_id=' + form.select_country" role="button"><i class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.chat-keyword-hit.actions.export') }}</a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/chat-keyword-hits/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.chat-keyword-hit.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row justify-content-md-between col-md-12">
                                <div class="col-md-3 form-group">
                                    <multiselect :searchable="true" v-model="form.select_subscriber" id="subscriber_id" name="subscriber_id"
                                        placeholder="Select Subscriber"
                                        @search-change="asyncFind"
                                        :options="form.subscriber_data.map(type => type.id)"
                                        :custom-label="opt =>form.subscriber_data.find(x => x.id == opt).name"
                                        open-direction="auto" :multiple="false" @input="filter('subscriber_id', form.select_subscriber)">
                                    </multiselect>
                                </div>
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
                                        open-direction="auto" :multiple="false"
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

                                    <multiselect :searchable="true" v-model="form.select_country" id="country_id" name="country_id"
                                        placeholder="Select Country"
                                        :options="{{ $country }}.map(type => type.id)"
                                        :custom-label="opt => {{ $country }}.find(x => x.id == opt).title"
                                        open-direction="auto" :multiple="false" @input="filter('country_id', form.select_country)">
                                    </multiselect>
                                </div>
                                <div class="col-md-3 form-group"></div>
            
                            </div>
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

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <table class="table table-hover ">
                                <thead>
                                    <tr>

                                        <th is='sortable' data-title="id" :column="'id'">{{ trans('admin.chat-keyword-hit.columns.id') }}</th>
                                        <th is='sortable' data-title="keyword_id" :column="'keyword_id'">{{ trans('admin.chat-keyword-hit.columns.keyword_id') }}</th>
                                        <th is='sortable' data-title="subscriber_id" :column="'subscriber_id'">{{ trans('admin.chat-keyword-hit.columns.subscriber_id') }}</th>
                                        <th is='sortable' data-title="cadre" :column="'cadre'">Cadre</th>
                                        <th is='sortable' data-title="country" :column="'country'">Country</th>
                                        <th is='sortable' data-title="state" :column="'state'">State</th>
                                        <th is='sortable' data-title="district" :column="'district'">District</th>
                                        <th is='sortable' data-title="block" :column="'block'">Block</th>
                                        <th is='sortable' data-title="health_facility" :column="'health_facility'">Health Facility</th>
                                        <th is='sortable' data-title="created_at" :column="'created_at'">Created At</th>
                                        <th></th>
                                    </tr>
                                 
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                     

                                        <td data-title="id" >@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td data-title="keyword_id" >@{{ item.keyword.title }}</td>
                                        <td data-title="subscriber_id" >@{{ item.user.name }}</td>
                                        <td data-title="cadre" >@{{ item . user . cadre && item . user. cadre . title ? item . user . cadre . title : '' }}</td>
                                        <td data-title="country" >@{{ item.user.country && item .user. country . title ? item .user. country . title : '' }}</td>
                                        <td data-title="state" >@{{ item . user . state && item . user . state . title ? item . user . state . title : '' }}</td>
                                        <td data-title="district" >@{{ item . user . district && item . user . district . title ? item . user . district . title : '' }}
                                        </td>
                                        <td data-title="block" >@{{ item . user . block && item . user . block . title ? item . user . block . title : '' }}</td>
                                        <td data-title="health_facility" >@{{ item . user . health_facility && item . user . health_facility . health_facility_code ? item . user . health_facility . health_facility_code : '' }}
                                        </td>
                                        {{-- <td>@{{ moment(item.created_at).format('MM/DD/YYYY hh:mm') }}</td> --}}
                                        <td data-title="created_at" >@{{ item.created_at | moment }}</td>
                                        <td ></td>
                                      
                                    </tr>
                                </tbody>
                            </table>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </chat-keyword-hit-listing>

@endsection