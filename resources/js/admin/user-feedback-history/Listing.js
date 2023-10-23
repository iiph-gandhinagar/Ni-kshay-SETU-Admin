import AppListing from '../app-components/Listing/AppListing';

Vue.component('user-feedback-history-listing', {
    mixins: [AppListing],
    props: ['rating','subscribers'],
    data: function data() {
        return {
            form: {
                select_subscriber: '',
                select_rating: '',
                subscriber_data: [],
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
    }
});