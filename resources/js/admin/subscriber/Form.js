import AppForm from '../app-components/Form/AppForm';

Vue.component('subscriber-form', {
    mixins: [AppForm],
    props: ["subscriber","cadre", "state", "district","block","health_facility"],
    data: function() {
        return {
            form: {
                api_token:  '' ,
                name:  '' ,
                phone_no:  '' ,
                password:  '' ,
                cadre_type:  '' ,
                is_verified:  false ,
                cadre_id:  [] ,
                country_id: [],
                block_id:  '' ,
                district_id:  [] ,
                state_id:  [] ,
                health_facility_id:  '' ,
                options: [],
                filtered_cadres: [],
                district_options: [],
                filtered_district: [],
                block_options: [],
                filtered_block: [],
                health_facility_options: [],
                filtered_health_facility: [],
                
            }
        }
    },
    methods: {
        getCadresOnChangeOfType: function() {
            this.form.state_id = [];
            this.form.country_id = [];
            this.form.cadre_id = [];
            this.form.district_id = [];
            this.form.block_id = [];
            this.form.health_facility_id = [];
            this.getCadres();
        },

        getCadres: function() {
            this.form.options = [];
            this.form.filtered_cadres = [];
            let cadre_type = this.form.cadre_type;

            if (this.form.cadre_type != "") {
                this.form.filtered_cadres = this.cadre.filter(function(e) {
                    return e.cadre_type == cadre_type;
                });
            }
            this.form.options = this.form.filtered_cadres;
        },

        getDistrictOnChangeOfState: function() {
            this.form.district_id = [];
            this.form.block_id = [];
            this.form.health_facility_id = [];
            this.getDistrictList();
        },

        getDistrictList: function() {
            this.form.district_options = [];
            this.form.filtered_district = [];
            let stateIds = this.form.state_id; //.map(v => v.id);
            if (stateIds != '' && stateIds != 0) {
                this.form.filtered_district = this.district.filter(el => {
                    return stateIds.id === el.state_id;
                });

                this.form.district_options = this.form.filtered_district;
            }
        },

        getBlockOnChangeOfDistrict: function() {
            this.form.block_id = [];
            this.form.health_facility_id = [];
            this.getBlockList();
        },

        getBlockList: function() {
            this.form.block_options = [];
            this.form.filtered_block = [];
            let districtIds = this.form.district_id; //.map(v => v.id);
            if (districtIds != '' && districtIds != 0) {
                this.form.filtered_block = this.block.filter(el => {
                    return districtIds.id === el.district_id;
                });

                this.form.block_options = this.form.filtered_block;
            }
        },

        getHealthFacilityOnChangeOfBlock: function() {
            this.form.health_facility_id = [];
            this.getHealthFacilityList();
        },

        getHealthFacilityList: function() {
            this.form.health_facility_options = [];
            this.form.filtered_health_facility = [];
            let blockIds = this.form.block_id; //.map(v => v.id);
            if (blockIds != '' && blockIds != 0) {
                this.form.filtered_health_facility = this.health_facility.filter(el => {
                    return blockIds.id === el.block_id;
                });

                this.form.health_facility_options = this.form.filtered_health_facility;
            }
        }
    },
    beforeMount() {
        //window on load
        this.getCadres();
        this.getDistrictList();
        this.getBlockList();
        this.getHealthFacilityList();
    }

});