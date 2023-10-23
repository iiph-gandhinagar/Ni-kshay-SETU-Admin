@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.lb-task-list.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <lb-task-list-form
            :action="'{{ url('admin/lb-task-lists') }}'"
            v-bind:lb_badge="{{  json_encode($lb_badge) }}"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.lb-task-list.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.lb-task-list.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </lb-task-list-form>

        </div>

        </div>

    
@endsection