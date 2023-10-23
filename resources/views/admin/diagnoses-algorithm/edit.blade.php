@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.diagnoses-algorithm.actions.edit', ['name' => $diagnosesAlgorithm->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <diagnoses-algorithm-form
                :action="'{{ $diagnosesAlgorithm->resource_url }}'"
                :data="{{ $diagnosesAlgorithm->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:state="{{  json_encode($state) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.diagnoses-algorithm.actions.edit', ['name' => $diagnosesAlgorithm->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.diagnoses-algorithm.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </diagnoses-algorithm-form>

        </div>
    
</div>

@endsection