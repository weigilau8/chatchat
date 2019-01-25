<template>
    <div>
    <div class="panel panel-primary">
            <div class="panel-heading" id="accordion">
                <span class="glyphicon glyphicon-comment"></span> {{ group.name }}
                <div class="btn-group pull-right">
                    <a type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-parent="#accordion-" :href="'#collapseOne-' + group.id">
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                </div>
            </div>
            <div class="panel-collapse collapse" :id="'collapseOne-' + group.id">
                <div class="panel-body chat-panel">
                    <ul class="chat">
                        <li v-for="conversation in conversations">
                        <!-- <span class="chat-img pull-left">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                        </span> -->
                            <div class="chat-body clearfix">
                                <div class="header">
                                    <strong class="primary-font">{{ conversation.user.name }}</strong>
                                </div>
                                <p>
                                    {{ conversation.message }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." v-model="message" @keyup.enter="store()" autofocus />
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat" @click.prevent="store()">
                                Send</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['group'],

        data() {
            return {
                conversations: [],
                message: '',
                group_id: this.group.id
            }
        },

        mounted() {
            $('.collapse').collapse();
            axios.get('/conversations', {
                params: {
                    id: this.group.id
                }
            })
            // populate data from database
            .then((response) => {
                var a = response.data;
                
                
                for (var i=0, len= a.length; i <len; i++) {
                    this.conversations.push(a[i]);
                }
                
            });
            this.id = this.group.id
            // this.startInterval();
            this.listenForNewMessage();
        },

        methods: {
            store() {
                axios.post('/conversations', {
                    message: 
                        this.message, 
                        group_id: this.group.id
                })
                .then((response) => {
                    this.message = '';
                    this.conversations.push(response.data);
                });
            },

            // startInterval: function() {
            //     var ii = this.group.id;
            //    console.log()
            //     setInterval(() => {  
             
            //         axios.get('/message', {
            //             params: {
            //                 id: ii
            //             }
            //         })
            //         // populate data from database
            //         .then((response) => {
            //             var a = response.data;
                    
            //             if (response.data.length > this.messages.length) {
            //                 this.messages.push(response.data[response.data.length - 1]);
            //             }
            //         });
            //         this.id = this.group.id
            //     }, 5000);
            // },

            listenForNewMessage() {
                Echo.private('groups.' + this.group.id)
                    .listen('NewMessage', (e) => {
                        // console.log(e);
                        this.conversations.push(e);
                    });
            }
        }
    }
</script>
