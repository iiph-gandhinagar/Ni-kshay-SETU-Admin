import AppListing from '../app-components/Listing/AppListing';

Vue.component('app-config-listing', {
    mixins: [AppListing],
    props:["session_search"],
    data: function data() {
        return {
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },   
            search: '', 
        }
    },
    methods:{
        getSerchFilter(ajax){
            if(ajax && ajax == 1){
                this.session_search = this.search;
            }else{
                this.search = this.session_search;
            }
        },
    },
    beforeMount() {
        this.getSerchFilter(0);
    }
});