import AppListing from "../app-components/Listing/AppListing";

Vue.component("enquiry-listing", {
    mixins: [AppListing],
    props: ['district','all_blocks','health_facility'],
    data: function() {
        let uri = window.location.search.substring(1);
        let params = new URLSearchParams(uri);
        return {
            form: {
                date: params.get("date") ? params.get("date") : "",
                select_cadre: '',
                select_country: '',
                select_state:'',
                select_district:'',
                select_block:'',
                select_health_facility: '',
                district:[],
                block:[],
                health_facility:[],
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
        };
    },
    filters: {
        moment: function(date) {
            return moment(date).format("Do MMMM YYYY, h:mm:ss a");
        }
    },
    methods: {
        getStateDistrict() {
            this.form.district = this.district.filter(v=>v.state_id == this.form.select_state).map(title=>title,id=>id);
        },
        getDistrictBlock() {
            this.form.block = this.all_blocks.filter(v=>v.state_id == this.form.select_state && v.district_id == this.form.select_district).map(title=>title,id=>id);
        },
        getBlockHealthFacility(){
            this.form.health_facility = this.health_facility.filter(
                v=>v.state_id == this.form.select_state && v.district_id == this.form.select_district && v.block_id == this.form.select_block)
                .map(title=>title,id=>id);
        }
    },
    beforeMount() {
        this.getStateDistrict();
        this.getDistrictBlock();
        this.getBlockHealthFacility();
    },
});
