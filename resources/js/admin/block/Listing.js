import AppListing from '../app-components/Listing/AppListing';

Vue.component('block-listing', {
    mixins: [AppListing],
    props:["session_search"],
    data: function() {
        return {
            orderBy: {
                column: 'id',
                direction: 'desc'
            },  
            search: '',
        };
        
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