@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.admin-user.actions.index'))

@section('body')

    <admin-user-listing
        :data="{{ $data->toJson() }}"
        :activation="!!'{{ $activation }}'"
        :url="'{{ url('admin/admin-users') }}'"
        :all_states="{{ json_encode($states) }}"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.admin-user.actions.index') }}
                        <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/admin-users/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.admin-user.actions.create') }}</a>
                    </div>
                    <div class="card-body" v-cloak>
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
                                    <th is='sortable' data-title="id" :column="'id'">{{ trans('admin.admin-user.columns.id') }}</th>
                                    <th is='sortable' data-title="first_name" :column="'first_name'">{{ trans('admin.admin-user.columns.first_name') }}</th>
                                    <th is='sortable' data-title="last_name" :column="'last_name'">{{ trans('admin.admin-user.columns.last_name') }}</th>
                                    <th is='sortable' data-title="email" :column="'email'">{{ trans('admin.admin-user.columns.email') }}</th>
                                    <th is='sortable' data-title="activated" :column="'activated'" v-if="activation">{{ trans('admin.admin-user.columns.activated') }}</th>
                                    <th is='sortable' data-title="forbidden" :column="'forbidden'">{{ trans('admin.admin-user.columns.forbidden') }}</th>
                                    <th is='sortable' data-title="language" :column="'language'">{{ trans('admin.admin-user.columns.language') }}</th>
                                    <th is='sortable' data-title="state" :column="'state'">{{ trans('admin.admin-user.columns.state') }}</th>
                                    <th is='sortable' data-title="role_type" :column="'role_type'">{{ trans('admin.admin-user.columns.role_type') }}</th>
                                    <th is='sortable' data-title="last_login_at" :column="'last_login_at'">{{ trans('admin.admin-user.columns.last_login_at') }}</th>
                                    <th is='sortable' data-title="roles" :column="'roles'">{{ trans('admin.admin-user.columns.roles') }}</th>
                                    
                                    <th  data-title="lastField"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in collection">
                                    <td data-title="id" >@{{ item.id }}</td>
                                    <td data-title="first_name" >@{{ item.first_name }}</td>
                                    <td data-title="last_name" >@{{ item.last_name }}</td>
                                    <td data-title="email" >@{{ item.email }}</td>
                                    <td v-if="activation" data-title="activated">
                                        <label class="switch switch-3d switch-success">
                                            <input type="checkbox" class="switch-input" v-model="collection[index].activated" @change="toggleSwitch(item.resource_url, 'activated', collection[index])">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </td>
                                    <td data-title="forbidden">
                                        <label class="switch switch-3d switch-danger">
                                            <input type="checkbox" class="switch-input" v-model="collection[index].forbidden" @change="toggleSwitch(item.resource_url, 'forbidden', collection[index])">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </td>
                                    <td data-title="language" >@{{ item.language }}</td>
                                    <td data-title="state">
                                        <span v-if="item.state != '' ">
                                            <span v-for="(states,j) in getStateNamesByIds(item)">
                                                <span class="badge badge-danger m-1 p-1" style="color:white">@{{states}}</span>
                                            </span>
                                        </span>
                                        <span v-else class="badge badge-danger m-1 p-1" style="color:white"> ALL STATES</span>
                                    </td>
                                    
                                    <td data-title="role_type">@{{ item.role_type.toUpperCase() }}</td>
                                    <td data-title="last_login_at">@{{ item.last_login_at | datetime }}</td>
                                    <td data-title="roles">@{{ item.roles[0].name }}</td>
                                    
                                    <td data-title="lastField">
                                        <div class="row no-gutters">
                                            @can('admin.admin-user.impersonal-login')
                                            <div class="col-auto">
                                                <button class="btn btn-sm btn-success" v-show="item.activated" @click="impersonalLogin(item.resource_url + '/impersonal-login', item)" title="Impersonal login" role="button"><i class="fa fa-user-o"></i></button>
                                            </div>
                                            @endcan
                                            <div class="col-auto">
                                                <button class="btn btn-sm btn-warning" v-show="!item.activated" @click="resendActivation(item.resource_url + '/resend-activation')" title="Resend activation" role="button"><i class="fa fa-envelope-o"></i></button>
                                            </div>
                                            <div v-if="item.id > 1" class="col-auto">
                                                <a class="btn btn-sm btn-spinner btn-info" :href="item.resource_url + '/edit'" title="{{ trans('brackets/admin-ui::admin.btn.edit') }}" role="button"><i class="fa fa-edit"></i></a>
                                            </div>
                                            <form v-if="item.id > 1" class="col" @submit.prevent="deleteItem(item.resource_url)">
                                                <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('brackets/admin-ui::admin.btn.delete') }}"><i class="fa fa-trash-o"></i></button>
                                            </form>
                                        </div>
                                    </td>
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
                            <a class="btn btn-primary btn-spinner" href="{{ url('admin/admin-users/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.new') }} AdminUser</a>
	                    </div>
                    </div>
                </div>
            </div>
        </div>
    </admin-user-listing>

@endsection