@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.resource-material.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <resource-material-form
            :action="'{{ url('admin/resource-materials') }}'"
            :locales="{{ json_encode($locales) }}"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            :send-empty-locales="false"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.resource-material.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.resource-material.components.form-elements', ['mode' => 'create'])
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </resource-material-form>

        </div>

        </div>

    
@endsection