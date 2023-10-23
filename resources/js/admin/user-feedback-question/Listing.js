import AppListing from '../app-components/Listing/AppListing';

Vue.component('user-feedback-question-listing', {
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
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
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