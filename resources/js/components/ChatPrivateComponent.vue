<template>
    <div class="container">
        <h2>Private Channel</h2>
        <div class="base" v-for="message in messages" :key="message.timestamp">
            <div class="you">{{ message.message }}</div>
        </div>
    </div>
</template>

<style scoped>
.base {
    overflow: auto;
    max-height:500px;
    border-right: 1px solid #ddd;
    border-left: 1px solid #ddd;
    background-color: #eee;
}
.you {
  width: 30%;
  margin:10px;
  padding: 20px;
  background-color: #f8f8f8;
  border-radius: 10px;
  word-wrap: break-word;
}
</style>

<script>
import Pusher from 'pusher-js';

export default {
    props: ["user"],
    data() {
        return {
            messages: [],
            text: "",
        }
    },
    created() {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
            cluster: process.env.MIX_PUSHER_APP_CLUSTER
        });

        Echo.private('App.User.' + this.user.id).listen('PrivateChannelEvent', (e) => {
            this.messages.push(e);
        });
    }
}
</script>
