@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.survey-master.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <survey-master-form
            :action="'{{ url('admin/survey-masters') }}'"
            :locales="{{ json_encode($locales) }}"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            v-bind:district="{{  json_encode($district) }}"
            :send-empty-locales="false"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.survey-master.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.survey-master.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </survey-master-form>

        </div>

        </div>

    
@endsection