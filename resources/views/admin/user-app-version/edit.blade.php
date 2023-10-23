@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-app-version.actions.edit', ['name' => $userAppVersion->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <user-app-version-form
                :action="'{{ $userAppVersion->resource_url }}'"
                :data="{{ $userAppVersion->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.user-app-version.actions.edit', ['name' => $userAppVersion->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.user-app-version.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </user-app-version-form>

        </div>
    
</div>

@endsection