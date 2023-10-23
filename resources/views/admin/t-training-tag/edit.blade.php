@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.t-training-tag.actions.edit', ['name' => $tTrainingTag->id]))

@section('body')
<link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <div class="container-xl">
        <div class="card">

            <t-training-tag-form
                :action="'{{ $tTrainingTag->resource_url }}'"
                :data="{{ $tTrainingTag->toJsonAllLocales() }}"
                v-bind:submodules="{{  json_encode($submodules) }}"
                :locales="{{ json_encode($locales) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.t-training-tag.actions.edit', ['name' => $tTrainingTag->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.t-training-tag.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </t-training-tag-form>

        </div>
    
</div>

@endsection