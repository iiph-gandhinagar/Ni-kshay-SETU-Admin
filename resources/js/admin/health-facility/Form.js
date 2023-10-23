import AppForm from '../app-components/Form/AppForm';

Vue.component('health-facility-form', {
    mixins: [AppForm],
    props:["state","district","block","health_facility"],
    data: function() {
        return {
            form: {
                state_id:  '' ,
                district_id:  '' ,
                block_id:  '' ,
                health_facility_code:  '' ,
                DMC:  false ,
                TRUNAT:  false ,
                CBNAAT:  false ,
                X_RAY:  false ,
                ICTC:  false ,
                LPA_Lab:  false ,
                CONFIRMATION_CENTER:  false ,
                Tobacco_Cessation_clinic:  false ,
                ANC_Clinic:  false ,
                Nutritional_Rehabilitation_centre:  false ,
                De_addiction_centres:  false ,
                ART_Centre:  false ,
                District_DRTB_Centre:  false ,
                NODAL_DRTB_CENTER:  false ,
                IRL:  false ,
                Pediatric_Care_Facility:  false ,
                longitude:  '' ,
                latitude:  '' ,
                country_id: [],
                filter_districts:[],
                filter_block:[],
                // filter_health_facility:[],
            }
        }
    },
    methods:{
        getClearData(){
            this.form.district_id = "";
            this.form.block_id = "";
            this.form.filter_districts= [];
            this.form.filter_block =[];
            this.getStateDistrict();
        },
        getClearBlock(){
            this.form.block_id = "";
            this.form.filter_block =[];
            this.getDistrictBlock();
        },
        getStateDistrict() {
            this.form.filter_districts = this.district.filter(v=>v.state_id == this.form.state_id.id).map(title=>title,id=>id);
        },
        getDistrictBlock() {
            this.form.filter_block = this.block.filter(v=>v.state_id == this.form.state_id.id && v.district_id == this.form.district_id.id).map(title=>title,id=>id);
        },
        // getBlockHealthFacility(){
        //     this.form.filter_health_facility = this.health_facility.filter(
        //         v=>v.state_id == this.form.state_id.id && v.district_id == this.form.district_id && v.block_id == this.form.block_id)
        //         .map(title=>title,id=>id);
        // },
    },
     beforeMount() {
        this.getStateDistrict();
        this.getDistrictBlock();
    }

});