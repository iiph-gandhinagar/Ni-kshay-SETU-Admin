@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-assessment.actions.edit', ['name' => $userAssessment->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <user-assessment-form
                :action="'{{ $userAssessment->resource_url }}'"
                :data="{{ $userAssessment->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.user-assessment.actions.edit', ['name' => $userAssessment->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.user-assessment.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </user-assessment-form>

        </div>
    
</div>

@endsection