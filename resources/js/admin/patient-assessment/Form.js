import AppForm from '../app-components/Form/AppForm';

Vue.component('patient-assessment-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                nikshay_id:  '' ,
                patient_name:  '' ,
                age:  '' ,
                gender:  '' ,
                patient_selected_data:  this.getLocalizedFormDefaults() ,
                PULSE_RATE:  '' ,
                TEMPERATURE:  '' ,
                BLOOD_PRESSURE:  '' ,
                RESPIRATORY_RATE:  '' ,
                OXYGEN_SATURATION:  '' ,
                TEXT_BMI:  '' ,
                TEXT_MUAC:  '' ,
                PEDAL_OEDEMA:  '' ,
                GENERAL_CONDITION:  '' ,
                TEXT_ICTERUS:  '' ,
                TEXT_HEMOGLOBIN:  '' ,
                COUNT_WBC:  '' ,
                TEXT_RBS:  '' ,
                TEXT_HIV:  '' ,
                TEXT_XRAY:  '' ,
                TEXT_HEMOPTYSIS:  '' ,
                
            }
        }
    }

});