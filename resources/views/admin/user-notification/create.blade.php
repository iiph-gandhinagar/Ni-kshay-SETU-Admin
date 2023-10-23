@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-notification.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <user-notification-form
            :action="'{{ url('admin/user-notifications') }}'"
            v-bind:subscriber="{{  json_encode($subscriber) }}"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            v-bind:district="{{  json_encode($district) }}"
            v-bind:assessment="{{  json_encode($assessment) }}"
            v-bind:resource_material="{{  json_encode($resource_material) }}"
            v-bind:case_definition="{{  json_encode($case_definition) }}"
            v-bind:dignosis_algo="{{  json_encode($dignosis_algo) }}"
            v-bind:cgc_algo="{{  json_encode($cgc_algo) }}"
            v-bind:differential_care_algo="{{  json_encode($differential_care_algo) }}"
            v-bind:guidance_on_adr="{{  json_encode($guidance_on_adr) }}"
            v-bind:latent_tb_infection="{{  json_encode($latent_tb_infection) }}"
            v-bind:treatment_algo="{{  json_encode($treatment_algo) }}"
            v-bind:dynamic_algo="{{  json_encode($dynamic_algo_master) }}"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.user-notification.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.user-notification.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </user-notification-form>

        </div>

        </div>

    
@endsection