import AppListing from '../app-components/Listing/AppListing';

Vue.component('static-blog-listing', {
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
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
    methods:{
        getKeywords(data){
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
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        }
    },
    beforeMount() {
        this.getSerchFilter(0);
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});