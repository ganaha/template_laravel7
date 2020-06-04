<template>
    <div class="container">
        <h2>Public Channel</h2>
        <div class="base" v-for="message in messages" :key="message.message">
            <div :class="[username == message.username ? 'me' : 'you']">{{ message.message }}</div>
            <div :class="[username == message.username ? 'me-status' : 'you-status']">{{ message.timestamp }}, {{ message.username }}</div>
        </div>
        <form @submit.prevent="send">
            <input type="text" value="" v-model="text" />
            <input type="submit" value="送信">
        </form>
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
.me {
  width: 30%;
  margin:10px 10px 0px auto;
  padding: 20px;
  background-color: #7ade40;
  border-radius: 10px;
  word-wrap: break-word;
}
.you {
  width: 30%;
  margin:10px;
  padding: 20px;
  background-color: #f8f8f8;
  border-radius: 10px;
  word-wrap: break-word;
}
.me-status {
    float: right;
    margin-right: 10px;
}
.you-status {
    float: left;
    margin-left: 10px;
}
</style>

<script>
import Pusher from 'pusher-js';

export default {
    props: ["username"],
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

        var channel = pusher.subscribe("public-channel");
        channel.bind("public-event", (data) => {
            this.messages.push(data);
        });
    },
    methods: {
        send() {
            axios.post('/chat/send', {
              message: this.text,
            }).then((res) => {
                this.text = '';
            }).catch((e) => {
                console.log(e);
            });
        }
    }
}
</script>
