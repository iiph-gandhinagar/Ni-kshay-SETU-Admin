import AppListing from '../app-components/Listing/AppListing';

Vue.component('static-module-listing', {
    mixins: [AppListing],
    props:["session_search"],
    data: function() {
        return {
            search: '',
        };
        
    },
    filters: {
        stripHTML: function (str) {
            // console.log("string --->",str);
            return str.replace(/<\/?[^>]+(>|$)/g, "");
        },
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    data: function data() {
        return {
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
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