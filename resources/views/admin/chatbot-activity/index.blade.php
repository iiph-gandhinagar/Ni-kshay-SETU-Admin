@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chatbot-activity.actions.index'))

@section('body')

    <chatbot-activity-listing
        :data="{{ $data->toJson() }}"
        :subscribers="{{ json_encode($subscriber) }}"
        :action="{{ json_encode($action) }}"
        :url="'{{ url('admin/chatbot-activities?response='.$response) }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.chatbot-activity.actions.index') }}
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/chatbot-activities/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.chatbot-activity.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <div class="row col-md-12">
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
                                  
                                    <multiselect :searchable="true" v-model="form.select_action" id="action" name="action"
                                        placeholder="Select Action"
                                        :options="{{ $action }}.map(type => type.action)"
                                        :custom-label="opt => {{ $action }}.find(x => x.action == opt).action"
                                        open-direction="auto" :multiple="false" @input="filter('action', form.select_action)">
                                    </multiselect>
                                </div>
                                <div class="col-md-3 form-group">

                                    {{-- <multiselect :searchable="true" v-model="form.select_response" id="response" name="response"
                                        placeholder="Select Response"
                                        :options="{{ $response }}.map(type => type.response)"
                                        :custom-label="opt => {{ $response }}.find(x => x.response == opt).response"
                                        open-direction="auto" :multiple="false" @input="filter('response', form.select_response)">
                                    </multiselect> --}}
                                    <select class="form-control" v-model="form.select_response" name="response"
                                        id="response" @change="getResponse($event);">
                                        <option value="">Select Response</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <form @submit.prevent="">
                            <div class="row justify-content-md-between">
                                <div class="col col-lg-7 col-xl-5 form-group">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
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

                        <table class="table table-hover ">
                            <thead>
                                <tr>
                                    <th class="bulk-checkbox">
                                        <input class="form-check-input" id="enabled" type="checkbox" v-model="isClickedAll"
                                            v-validate="''" data-vv-name="enabled" name="enabled_fake_element"
                                            @click="onBulkItemsClickedAllWithPagination()">
                                        <label class="form-check-label" for="enabled">
                                            #
                                        </label>
                                    </th>

                                    <th is='sortable' data-title="id"  :column="'id'">
                                        {{ trans('admin.chatbot-activity.columns.id') }}</th>
                                    <th is='sortable' data-title="user_id"  :column="'user_id'">
                                        {{ trans('admin.chatbot-activity.columns.user_id') }}</th>
                                    <th is='sortable' data-title="cadre"  :column="'cadre'">cadre</th>
                                    <th is='sortable' data-title="action"  :column="'action'">
                                        {{ trans('admin.chatbot-activity.columns.action') }}</th>
                                    <th is='sortable' data-title="payload"  :column="'payload'">
                                        {{ trans('admin.chatbot-activity.columns.payload') }}</th>
                                    <th is='sortable' data-title="plateform"  :column="'plateform'">
                                        {{ trans('admin.chatbot-activity.columns.plateform') }}</th>
                                    <th is='sortable' data-title="response"  :column="'response'">
                                        {{ trans('admin.chatbot-activity.columns.response') }}</th>
                                    <th is='sortable' data-title="ip_address"  :column="'ip_address'">
                                        {{ trans('admin.chatbot-activity.columns.ip_address') }}</th>
                                    <th is='sortable' data-title="tag"  :column="'tag'">Tag</th>
                                    {{-- <th is='sortable' :column="'question_id'">{{ trans('admin.chatbot-activity.columns.question_id') }}</th> --}}
                                    <th is='sortable' data-title="like"  :column="'like'">
                                        {{ trans('admin.chatbot-activity.columns.like') }}</th>
                                    <th is='sortable' data-title="dislike"  :column="'dislike'">
                                        {{ trans('admin.chatbot-activity.columns.dislike') }}</th>
                                    <th is='sortable' data-title="created_at"  :column="'created_at'">Activity Time</th>


                                    <th></th>
                                </tr>
                                <tr v-show="(clickedBulkItemsCount > 0) || isClickedAll">
                                    <td class="bg-bulk-info d-table-cell text-center" colspan="10">
                                        <span
                                            class="align-middle font-weight-light text-dark">{{ trans('brackets/admin-ui::admin.listing.selected_items') }}
                                            @{{ clickedBulkItemsCount }}. <a href="#" class="text-primary"
                                                @click="onBulkItemsClickedAll('/admin/chatbot-activities')"
                                                v-if="(clickedBulkItemsCount < pagination.state.total)"> <i
                                                    class="fa"
                                                    :class="bulkCheckingAllLoader ? 'fa-spinner' : ''"></i>
                                                {{ trans('brackets/admin-ui::admin.listing.check_all_items') }}
                                                @{{ pagination.state.total }}</a> <span class="text-primary">|</span> <a href="#"
                                                class="text-primary"
                                                @click="onBulkItemsClickedAllUncheck()">{{ trans('brackets/admin-ui::admin.listing.uncheck_all_items') }}</a>
                                        </span>

                                        <span class="pull-right pr-2">
                                            <button class="btn btn-sm btn-danger pr-3 pl-3"
                                                @click="bulkDelete('/admin/chatbot-activities/bulk-destroy')">{{ trans('brackets/admin-ui::admin.btn.delete') }}</button>
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
                                    <td data-title="id" >@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                    {{-- <td>@{{ item.id }}</td> --}}
                                    <td data-title="user_id" >@{{ item.user.name }}</td>
                                    <td data-title="cadre" >@{{ item.user.cadre.title }}</td>
                                    <td data-title="action" >@{{ item.action }}</td>
                                    <td data-title="payload" >@{{ item.payload }}</td>
                                    <td data-title="plateform" >@{{ item.plateform }}</td>
                                    <td data-title="response" >
                                        <span v-if="item.response == 1" class="badge badge-success m-1 p-1"
                                            style="font-size:0.8rem;color:white">Yes</span>
                                        <span v-else>
                                            <span class="badge badge-warning m-1 p-1"
                                                style="font-size:0.8rem;color:black">No</span>
                                        </span>
                                    </td>
                                    <td data-title="ip_address" >@{{ item.ip_address }}</td>
                                    <td data-title="tag" >@{{ item.tag && item.tag.tag ? item.tag.tag : 0 }}</td>
                                    {{-- <td>@{{ item.question_id }}</td> --}}
                                    <td data-title="like" >@{{ item.like }}</td>
                                    <td data-title="dislike" >@{{ item.dislike }}</td>
                                    <td data-title="created_at" >@{{ item.created_at | moment }}</td>

                                    <td>
                                        {{-- <div class="row no-gutters">
                                                <div class="col-auto">
                                                    <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                                </div>
                                                <form class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                                </form>
                                            </div> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="no-items-found" v-if="!collection.length > 0">
                            <i class="icon-magnifier"></i>
                            <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                            <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                            {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/chatbot-activities/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.chatbot-activity.actions.create') }}</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </chatbot-activity-listing>

@endsection

