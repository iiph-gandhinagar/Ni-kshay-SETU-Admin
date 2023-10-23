@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment.actions.create'))

@section('body')

    <div class="container-xl">

                <div class="card">
        
        <assessment-form
            :action="'{{ url('admin/assessments') }}'"
            v-bind:cadre="{{  json_encode($cadre) }}"
            v-bind:state="{{  json_encode($state) }}"
            v-bind:district="{{  json_encode($district) }}"
            :locales="{{ json_encode($locales) }}"
            :send-empty-locales="false"
            v-cloak
            inline-template>

            <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action" novalidate>
                
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ trans('admin.assessment.actions.create') }}
                </div>

                <div class="card-body">
                    @include('admin.assessment.components.form-elements')
                </div>
                                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="submiting">
                        <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                        {{ trans('brackets/admin-ui::admin.btn.save') }}
                    </button>
                </div>
                
            </form>

        </assessment-form>

        </div>

        </div>

    
@endsection