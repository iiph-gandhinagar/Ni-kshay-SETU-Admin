@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.assessment-question.actions.edit', ['name' => $assessmentQuestion->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <assessment-question-form
                :action="'{{ $assessmentQuestion->resource_url }}'"
                :data="{{ $assessmentQuestion->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.assessment-question.actions.edit', ['name' => $assessmentQuestion->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.assessment-question.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </assessment-question-form>

        </div>
    
</div>

@endsection