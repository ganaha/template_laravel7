<template>
    <div class="container">
        <h2>Presence Channel</h2>
        <div>入室者: 
            <ul>
                <li v-for="user in hereUsers" :key="user.id">{{ user.name }}</li>
            </ul>
        </div>
        <div class="base" v-for="message in messages" :key="message.timestamp">
            <div :class="[username == message.name ? 'me' : 'you']">{{ message.message }}</div>
            <div v-show="message.timestamp" :class="[username == message.name ? 'me-status' : 'you-status']">{{ message.timestamp | moment('hh:mm') }}, {{ message.name }}</div>
        </div>
        <div v-show="typingUsername">{{ typingUsername }} さんが入力中...</div>
        <form @submit.prevent="send">
            <input type="text" value="" v-model="text" @keydown="typeInput" />
            <input type="submit" value="送信">
        </form>
        <button @click="leaveChannel">退室</button>
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
  width: 40%;
  margin:10px 10px 0px auto;
  padding: 20px;
  background-color: #7ade40;
  border-radius: 10px;
  word-wrap: break-word;
}
.you {
  width: 40%;
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
import moment from 'moment';

export default {
    props: ["username", "id"],
    data() {
        return {
            messages: [],
            text: "",
            hereUsers: [],
            channnel: null,
            typingUsername: '',
            typingTimer: null,
        }
    },
    created() {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        this.channel = Echo.join('chat.' + this.id).here((users) => {
            this.hereUsers = users;
        }).joining((user) => {
            this.messages.push({message: user.name + 'さんが入室しました。'});
            this.hereUsers.push(user);
        }).leaving((user) => {
            this.messages.push({message: user.name + 'さんが退室しました。'});
            this.hereUsers = this.hereUsers.filter((u) => u.id !== user.id);
        }).listen('PresenceChannelEvent', (e) => {
            this.stopTyping();
            this.messages.push(e);
        }).listenForWhisper('typing', (e) => {
            this.typingUsername = e.name;
            if (this.typingTimer) clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(this.stopTyping, 3000);
        });
    },
    filters: {
        moment(val, format) {
            return moment(val).format(format);
        }
    },
    methods: {
        send() {
            axios.post('/chat/presence/' + this.id, {
              message: this.text,
            }).then((res) => {
                this.text = '';
            }).catch((e) => {
                console.log('error', e);
            });
        },
        leaveChannel() {
            Echo.leave('chat.' + this.id);
        },
        typeInput() {
            this.channel.whisper('typing', {
                name: this.username
            });
        },
        stopTyping() {
            this.typingUsername = '';
        }
    }
}
</script>
