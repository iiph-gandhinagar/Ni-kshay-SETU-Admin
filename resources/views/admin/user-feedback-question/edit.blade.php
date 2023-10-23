@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.user-feedback-question.actions.edit', ['name' => $userFeedbackQuestion->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <user-feedback-question-form
                :action="'{{ $userFeedbackQuestion->resource_url }}'"
                :data="{{ $userFeedbackQuestion->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.user-feedback-question.actions.edit', ['name' => $userFeedbackQuestion->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.user-feedback-question.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </user-feedback-question-form>

        </div>
    
</div>

@endsection