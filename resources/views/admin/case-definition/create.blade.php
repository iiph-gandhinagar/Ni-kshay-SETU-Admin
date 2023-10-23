@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.case-definition.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <case-definition-form
            :action="'{{ url('admin/case-definitions') }}'"
            :locales="{{ json_encode($locales) }}"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            :send-empty-locales="false"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.case-definition.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.case-definition.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </case-definition-form>

        </div>

        </div>

    
@endsection