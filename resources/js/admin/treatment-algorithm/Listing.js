import AppListing from '../app-components/Listing/AppListing';

Vue.component('treatment-algorithm-listing', {
    mixins: [AppListing],
    props: ['all_cadres','all_states'],
    data: function data() {
        return {
            orderBy: {
                column: 'index',
                direction: 'desc'
            },    
        }
    },
    methods:{
        clearSession() {
            console.log("inside clear session");
            axios.get("/admin/treatment-algorithms").then(result => {
                console.log("session closed");
            });
        },
        idStore(id){
            localStorage.setItem('id1',id);
        },
        getId(){
            return localStorage.getItem('id1');
        }
    },
    beforeMount() {
        if(window.location.href != localStorage.getItem('url')){
            localStorage.setItem('scrollpos', '')
            localStorage.setItem('id1', '')
        }
    }
});