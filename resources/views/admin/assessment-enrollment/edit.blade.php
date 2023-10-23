@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment-enrollment.actions.edit', ['name' => $assessmentEnrollment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <assessment-enrollment-form
                :action="'{{ $assessmentEnrollment->resource_url }}'"
                :data="{{ $assessmentEnrollment->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.assessment-enrollment.actions.edit', ['name' => $assessmentEnrollment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.assessment-enrollment.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </assessment-enrollment-form>

        </div>
    
</div>

@endsection