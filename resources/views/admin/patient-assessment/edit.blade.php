@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.patient-assessment.actions.edit', ['name' => $patientAssessment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <patient-assessment-form
                :action="'{{ $patientAssessment->resource_url }}'"
                :data="{{ $patientAssessment->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.patient-assessment.actions.edit', ['name' => $patientAssessment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.patient-assessment.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </patient-assessment-form>

        </div>
    
</div>

@endsection