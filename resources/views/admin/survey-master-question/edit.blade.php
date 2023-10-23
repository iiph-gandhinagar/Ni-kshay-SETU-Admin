@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.survey-master-question.actions.edit', ['name' => $surveyMasterQuestion->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <survey-master-question-form
                :action="'{{ $surveyMasterQuestion->resource_url }}'"
                :data="{{ $surveyMasterQuestion->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.survey-master-question.actions.edit', ['name' => $surveyMasterQuestion->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.survey-master-question.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </survey-master-question-form>

        </div>
    
</div>

@endsection