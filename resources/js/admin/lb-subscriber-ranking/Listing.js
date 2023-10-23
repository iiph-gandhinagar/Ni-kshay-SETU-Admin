import AppListing from '../app-components/Listing/AppListing';

Vue.component('lb-subscriber-ranking-listing', {
    mixins: [AppListing],
    props: ['level','badge','subscribers'],
    data: function data() {
        return {
            form: {
                select_level: '',
                select_subscriber: '',
                select_badge: '',
                level:[],
                badge:[],
                subscriber_data: [],
            },
            orderBy: {
                column: 'created_at',
                direction: 'desc'
            },    
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
        getLevelBadge() {
            this.form.badge = this.badge.filter(v=>v.level_id == this.form.select_level).map(badge=>badge,id=>id);
        },
    },
    beforeMount() {
        this.getLevelBadge();
    },
    filters: {
        moment: function (date) {
          return moment(date).format('Do MMMM YYYY, h:mm:ss a');
        }
    },
});