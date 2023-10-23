@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.message-notification.actions.index'))

@section('body')

    <message-notification-listing :data="{{ $data->toJson() }}" :url="'{{ url('admin/message-notifications') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.message-notification.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0 ml-1"
                            href="{{ url('admin/message-notifications/create') }}" role="button"><i
                                class="fa fa-plus"></i>
                            {{ trans('admin.message-notification.actions.create') }}</a> 

                            <a class="btn btn-success btn-spinner btn-sm pull-right m-b-0"
                            href="{{ url('/admin/message-notifications/export') }}" role="button"><i
                                class="fa fa-download"></i>&nbsp; Sample File Download</a>
                    </div>
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
                                        <th width="5%" is='sortable'  data-title="id" :column="'id'">{{ trans('admin.message-notification.columns.id') }}</th>
                                        <th width="10%" is='sortable' data-title="user_name" :column="'user_name'">Recipient</th>
                                        <th width="10%" is='sortable' data-title="phone_no" :column="'phone_no'">{{ trans('admin.message-notification.columns.phone_no') }}</th>
                                        <th width="50%" is='sortable' data-title="message" :column="'message'">{{ trans('admin.message-notification.columns.message') }}</th>
                                        <th width="20%" is='sortable' data-title="created_at" :column="'created_at'">Message Receive At</th>

                                        <th width="5%"></th>
                                    </tr>
                                    
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id"
                                        :class="bulkItems[item.id] ? 'bg-bulk' : ''">

                                        <td data-title="id" >@{{ (pagination.state.current_page -1) * pagination.state.per_page + index+1 }}</td>
                                        <td data-title="user_name" >@{{ item . user_name }}</td>
                                        <td data-title="phone_no" >@{{ item . phone_no }}</td>
                                        <td data-title="message" >@{{ item . notification_message }}</td>
                                        <td data-title="created_at" >@{{ item . created_at | moment }}</td>
                                        <td></td>
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
                                    href="{{ url('admin/message-notifications/create') }}" role="button"><i
                                        class="fa fa-plus"></i>&nbsp;
                                    {{ trans('admin.message-notification.actions.create') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </message-notification-listing>

@endsection
