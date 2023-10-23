import AppListing from '../app-components/Listing/AppListing';

Vue.component('static-what-we-do-listing', {
    mixins: [AppListing],
    props:["session_search"],
    data: function() {
        return {
            orderBy: {
                column: 'created_at',
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
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    beforeMount() {
        this.getSerchFilter(0);
    }
});