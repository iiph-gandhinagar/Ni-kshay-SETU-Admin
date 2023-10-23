@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-keyword.actions.edit', ['name' => $chatKeyword->title]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <chat-keyword-form
                :action="'{{ $chatKeyword->resource_url }}'"
                :data="{{ $chatKeyword->toJsonAllLocales() }}"
                :locales="{{ json_encode($locales) }}"
                v-bind:submodules="{{  json_encode($submodules) }}"
                :send-empty-locales="false"
                v-cloak
                inline-template>
            
                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i> {{ trans('admin.chat-keyword.actions.edit', ['name' => $chatKeyword->title]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.chat-keyword.components.form-elements')
                    </div>
                    
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>
                    
                </form>

        </chat-keyword-form>

        </div>
    
</div>

@endsection