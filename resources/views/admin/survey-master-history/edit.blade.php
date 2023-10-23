@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.survey-master-history.actions.edit', ['name' => $surveyMasterHistory->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <survey-master-history-form
                :action="'{{ $surveyMasterHistory->resource_url }}'"
                :data="{{ $surveyMasterHistory->toJson() }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.survey-master-history.actions.edit', ['name' => $surveyMasterHistory->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.survey-master-history.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </survey-master-history-form>

        </div>
    
</div>

@endsection