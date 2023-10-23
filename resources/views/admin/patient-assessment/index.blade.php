@extends('brackets/admin-ui::admin.layout.default')

@section('title', trans('admin.patient-assessment.actions.index'))

@section('body')

    <patient-assessment-listing
        :data="{{ $data->toJson() }}"
        :url="'{{ url('admin/patient-assessments') }}'"
        inline-template>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> {{ trans('admin.patient-assessment.actions.index') }}
                        @can('admin.patient-assessment.export')
                            <a class="btn btn-primary btn-sm pull-right m-b-0 ml-2" href="{{ url('admin/patient-assessments/export') }}" role="button"><i class="fa fa-file-excel-o"></i>&nbsp; {{ trans('admin.patient-assessment.actions.export') }}</a>
                        @endcan
                        {{-- <a class="btn btn-primary btn-spinner btn-sm pull-right m-b-0" href="{{ url('admin/patient-assessments/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.patient-assessment.actions.create') }}</a> --}}
                    </div>
                    <div class="card-body" v-cloak>
                        <div class="card-block">
                            <form @submit.prevent="">
                                <div class="row justify-content-md-between">
                                    <div class="col col-lg-7 col-xl-5 form-group">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="{{ trans('brackets/admin-ui::admin.placeholder.search') }}" v-model="search" @keyup.enter="filter('search', $event.target.value)" />
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-primary" @click="filter('search', search)"><i class="fa fa-search"></i>&nbsp; {{ trans('brackets/admin-ui::admin.btn.search') }}</button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto form-group ">
                                        <select class="form-control" v-model="pagination.state.per_page">
                                            
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover table-responsive ">
                                <thead>
                                    <tr>
                                    
                                        <th width="5%" is='sortable' data-title="id" :column="'id'">{{ trans('admin.patient-assessment.columns.id') }}</th>
                                        <th width="5%" is='sortable' data-title="patient_name" :column="'patient_name'">{{ trans('admin.patient-assessment.columns.patient_name') }}</th>
                                        <th width="5%" is='sortable' data-title="age" :column="'age'">{{ trans('admin.patient-assessment.columns.age') }}</th>
                                        <th width="5%" is='sortable' data-title="gender" :column="'gender'">{{ trans('admin.patient-assessment.columns.gender') }}</th>
                                        <th width="5%" is='sortable' data-title="PULSE_RATE" :column="'PULSE_RATE'">{{ trans('admin.patient-assessment.columns.PULSE_RATE') }}</th>
                                        <th width="5%" is='sortable' data-title="TEMPERATURE" :column="'TEMPERATURE'">{{ trans('admin.patient-assessment.columns.TEMPERATURE') }}</th>
                                        <th width="5%" is='sortable' data-title="BLOOD_PRESSURE" :column="'BLOOD_PRESSURE'">{{ trans('admin.patient-assessment.columns.BLOOD_PRESSURE') }}</th>
                                        <th width="5%" is='sortable' data-title="RESPIRATORY_RATE" :column="'RESPIRATORY_RATE'">{{ trans('admin.patient-assessment.columns.RESPIRATORY_RATE') }}</th>
                                        <th width="5%" is='sortable' data-title="OXYGEN_SATURATION" :column="'OXYGEN_SATURATION'">{{ trans('admin.patient-assessment.columns.OXYGEN_SATURATION') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_BMI" :column="'TEXT_BMI'">{{ trans('admin.patient-assessment.columns.TEXT_BMI') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_MUAC" :column="'TEXT_MUAC'">{{ trans('admin.patient-assessment.columns.TEXT_MUAC') }}</th>
                                        <th width="5%" is='sortable' data-title="PEDAL_OEDEMA" :column="'PEDAL_OEDEMA'">{{ trans('admin.patient-assessment.columns.PEDAL_OEDEMA') }}</th>
                                        <th width="5%" is='sortable' data-title="GENERAL_CONDITION" :column="'GENERAL_CONDITION'">{{ trans('admin.patient-assessment.columns.GENERAL_CONDITION') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_ICTERUS" :column="'TEXT_ICTERUS'">{{ trans('admin.patient-assessment.columns.TEXT_ICTERUS') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_HEMOGLOBIN" :column="'TEXT_HEMOGLOBIN'">{{ trans('admin.patient-assessment.columns.TEXT_HEMOGLOBIN') }}</th>
                                        <th width="5%" is='sortable' data-title="COUNT_WBC" :column="'COUNT_WBC'">{{ trans('admin.patient-assessment.columns.COUNT_WBC') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_RBS" :column="'TEXT_RBS'">{{ trans('admin.patient-assessment.columns.TEXT_RBS') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_HIV" :column="'TEXT_HIV'">{{ trans('admin.patient-assessment.columns.TEXT_HIV') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_XRAY" :column="'TEXT_XRAY'">{{ trans('admin.patient-assessment.columns.TEXT_XRAY') }}</th>
                                        <th width="5%" is='sortable' data-title="TEXT_HEMOPTYSIS" :column="'TEXT_HEMOPTYSIS'">{{ trans('admin.patient-assessment.columns.TEXT_HEMOPTYSIS') }}</th>

                                    </tr>
                                    
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in collection" :key="item.id" :class="bulkItems[item.id] ? 'bg-bulk' : ''">
                                        
                                        <td data-title="id" >@{{ item.id }}</td>
                                        <td data-title="patient_name" >@{{ item.patient_name }}</td>
                                        <td data-title="age" >@{{ item.age }}</td>
                                        <td data-title="gender" >@{{ item.gender }}</td>
                                        <td data-title="PULSE_RATE" >@{{ item.PULSE_RATE }}</td>
                                        <td data-title="TEMPERATURE" >@{{ item.TEMPERATURE }}</td>
                                        <td data-title="BLOOD_PRESSURE" >@{{ item.BLOOD_PRESSURE }}</td>
                                        <td data-title="RESPIRATORY_RATE" >@{{ item.RESPIRATORY_RATE }}</td>
                                        <td data-title="OXYGEN_SATURATION" >@{{ item.OXYGEN_SATURATION }}</td>
                                        <td data-title="TEXT_BMI" >@{{ item.TEXT_BMI }}</td>
                                        <td data-title="TEXT_MUAC" >@{{ item.TEXT_MUAC }}</td>
                                        <td data-title="PEDAL_OEDEMA" >@{{ item.PEDAL_OEDEMA }}</td>
                                        <td data-title="GENERAL_CONDITION" >@{{ item.GENERAL_CONDITION }}</td>
                                        <td data-title="TEXT_ICTERUS" >@{{ item.TEXT_ICTERUS }}</td>
                                        <td data-title="TEXT_HEMOGLOBIN" >@{{ item.TEXT_HEMOGLOBIN }}</td>
                                        <td data-title="COUNT_WBC" >@{{ item.COUNT_WBC }}</td>
                                        <td data-title="TEXT_RBS" >@{{ item.TEXT_RBS }}</td>
                                        <td data-title="TEXT_HIV" >@{{ item.TEXT_HIV }}</td>
                                        <td data-title="TEXT_XRAY" >@{{ item.TEXT_XRAY }}</td>
                                        <td data-title="TEXT_HEMOPTYSIS" >@{{ item.TEXT_HEMOPTYSIS }}</td>
                                        
                                    </tr>
                                </tbody>
                            </table>

                            <div class="row" v-if="pagination.state.total > 0">
                                <div class="col-sm">
                                    <span class="pagination-caption">{{ trans('brackets/admin-ui::admin.pagination.overview') }}</span>
                                </div>
                                <div class="col-sm-auto">
                                    <pagination></pagination>
                                </div>
                            </div>

                            <div class="no-items-found" v-if="!collection.length > 0">
                                <i class="icon-magnifier"></i>
                                <h3>{{ trans('brackets/admin-ui::admin.index.no_items') }}</h3>
                                <p>{{ trans('brackets/admin-ui::admin.index.try_changing_items') }}</p>
                                {{-- <a class="btn btn-primary btn-spinner" href="{{ url('admin/patient-assessments/create') }}" role="button"><i class="fa fa-plus"></i>&nbsp; {{ trans('admin.patient-assessment.actions.create') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </patient-assessment-listing>

@endsection