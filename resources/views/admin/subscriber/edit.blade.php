@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.subscriber.actions.edit', ['name' => $subscriber->name]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <subscriber-form
                :action="'{{ $subscriber->resource_url }}'"
                :data="{{ $subscriber->toJson() }}"
                v-bind:cadre="{{  json_encode($cadre) }}"
                v-bind:state="{{  json_encode($state) }}"
                v-bind:district="{{  json_encode($district) }}"
                v-bind:block="{{  json_encode($block) }}"
                v-bind:health_facility="{{  json_encode($health_facility) }}"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.subscriber.actions.edit', ['name' => $subscriber->name]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.subscriber.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </subscriber-form>

        </div>
    
</div>

@endsection