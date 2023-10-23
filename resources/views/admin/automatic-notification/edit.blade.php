@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.automatic-notification.actions.edit', ['name' => $automaticNotification->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <automatic-notification-form
                :action="'{{ $automaticNotification->resource_url }}'"
                :data="{{ $automaticNotification->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.automatic-notification.actions.edit', ['name' => $automaticNotification->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.automatic-notification.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </automatic-notification-form>

        </div>
    
</div>

@endsection