@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.cgc-interventions-algorithm.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <cgc-interventions-algorithm-form
            :action="'{{ url('admin/cgc-interventions-algorithms') }}'"
            :locales="{{ json_encode($locales) }}"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            :send-empty-locales="false"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.cgc-interventions-algorithm.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.cgc-interventions-algorithm.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </cgc-interventions-algorithm-form>

        </div>

        </div>

    
@endsection