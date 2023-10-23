import AppListing from '../app-components/Listing/AppListing';

Vue.component('chat-keyword-hit-listing', {
    mixins: [AppListing],
    props: ['district','all_blocks','health_facility','subscribers'],
    data: function() {
        return {
            form: {
                select_subscriber: '',
                select_cadre: "",
                select_country: "",
                select_state: "",
                select_district: "",
                select_block: "",
                select_health_facility: "",
                date: '',
                district:[],
                block:[],
                subscriber_data: [],
                health_facility:[],
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            }, 
        }
    }, 
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    methods: {
        asyncFind (query) {
            this.isLoading = true;
            if(this.form.select_subscriber == '' || this.form.select_subscriber == null || this.form.select_subscriber == 0){
                if(query.length >= 3){
                    this.form.subscriber_data = this.subscribers.filter(option => option.name.toLowerCase().startsWith(query.toLowerCase()));
                    this.isLoading = false;
                }
            }else{
                this.form.subscriber_data = this.subscribers.filter(v=>v.id == this.form.select_subscriber).map(name=>name,id=>id)
                this.isLoading = false;
            } 
          },
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
});