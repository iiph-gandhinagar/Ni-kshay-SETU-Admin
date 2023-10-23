@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-question.actions.edit', ['name' => $chatQuestion->id]))

@section('body')

    <div class="container-xl">
        <div class="card">

            <chat-question-form 
                :action="'{{ $chatQuestion->resource_url }}'"
                :data="{{ $chatQuestion->toJsonAllLocales() }}" v-bind:cadre="{{ json_encode($cadre) }}"
                :locales="{{ json_encode($locales) }}" 
                :send-empty-locales="false" 
                v-cloak 
                inline-template>

                <form class="form-horizontal form-edit" method="post" @submit.prevent="onSubmit" :action="action" novalidate>


                    <div class="card-header">
                        <i class="fa fa-pencil"></i>
                        {{ trans('admin.chat-question.actions.edit', ['name' => $chatQuestion->id]) }}
                    </div>

                    <div class="card-body">
                        @include('admin.chat-question.components.form-elements')
                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" :disabled="submiting">
                            <i class="fa" :class="submiting ? 'fa-spinner' : 'fa-download'"></i>
                            {{ trans('brackets/admin-ui::admin.btn.save') }}
                        </button>
                    </div>

                </form>

            </chat-question-form>

        </div>

    </div>

@endsection