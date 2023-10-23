import AppListing from '../app-components/Listing/AppListing';

Vue.component('static-release-listing', {
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
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY');
        }
    },
    methods:{
        getFeatures(data){
            const splitted = data.split("|");
            console.log(splitted);
            return splitted;
        },
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