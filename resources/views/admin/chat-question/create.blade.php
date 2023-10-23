@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.chat-question.actions.create'))

@section('body')

    <div class="container-xl">

        <div class="card">

            <chat-question-form :action="'{{ url('admin/chat-questions') }}'" v-bind:cadre="{{ json_encode($cadre) }}"
                v-bind:keywords="{{ json_encode($keywords) }}"
                :locales="{{ json_encode($locales) }}" :send-empty-locales="false" v-cloak inline-template>

                <form class="form-horizontal form-create" method="post" @submit.prevent="onSubmit" :action="action"
                    novalidate>

                    <div class="card-header">
                        <i class="fa fa-plus"></i> {{ trans('admin.chat-question.actions.create') }}
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
