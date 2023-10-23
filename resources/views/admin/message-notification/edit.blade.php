@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.message-notification.actions.edit', ['name' => $messageNotification->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <message-notification-form
                :action="'{{ $messageNotification->resource_url }}'"
                :data="{{ $messageNotification->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.message-notification.actions.edit', ['name' => $messageNotification->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.message-notification.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </message-notification-form>

        </div>
    
</div>

@endsection