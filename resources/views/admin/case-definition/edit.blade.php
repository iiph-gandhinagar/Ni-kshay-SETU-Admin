@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.case-definition.actions.edit', ['name' => $caseDefinition->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <case-definition-form
                :action="'{{ $caseDefinition->resource_url }}'"
                :data="{{ $caseDefinition->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:state="{{  json_encode($state) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.case-definition.actions.edit', ['name' => $caseDefinition->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.case-definition.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </case-definition-form>

        </div>
    
</div>

@endsection