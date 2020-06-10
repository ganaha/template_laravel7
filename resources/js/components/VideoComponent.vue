<template>
    <div class="container">
        <h1 class="text-center">ビデオチャットのサンプル</h1>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="card" style="padding:15px;">
                    <div v-for="user in others" :key="user.id">
                        <a href="#" @click.prevent="startVideoChat(user.id)">「@{{ user.name }}」さんと通話を開始する</a>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-5">
                <div class="text-center">自分の映像</div>
                <video ref="video-here" autoplay></video>
            </div>
            <div class="col-2 text-center">
                ⇔<br>
                ビデオチャット
            </div>
            <div class="col-5">
                <div class="text-center">相手の映像</div>
                <video ref="video-there" autoplay></video>
            </div>
        </div>
    </div>
</template>

<style scoped>
video {
    width: 100%
}
</style>

<script>
export default {
    props: ['user', 'others'],
    data() {
        return {
            stream: null,
            channel: null,
            peers: {},
        }
    },
    mounted() {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        navigator.mediaDevices.getUserMedia({ video: true, audio: true }).then((stream) => {
            const videoHere = this.$refs['video-here'];
            videoHere.srcObject = stream;
            this.stream = stream;

            var pusher = new Pusher(process.env.MIX_PUSHER_APP_KEY, {
                authEndpoint: '/auth/video_chat',
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                auth: {
                    headers: {
                        'X-CSRF-Token': document.head.querySelector('meta[name="csrf-token"]').content
                    }
                }
            });

            this.channel = pusher.subscribe('presence-video-chat');
            this.channel.bind('client-signal-' + this.user.id, (signal) => {
                const userId = signal.userId;
                const peer = this.getPeer(userId, false);
                peer.signal(signal.data);
            });
        })
    },
    methods: {
        startVideoChat(userId) {
            console.log('click');
            this.getPeer(userId, true);
        },
        getPeer(userId, initiator) {
            if (this.peers[userId] !== undefined) return this.peers[userId];
            let peer = new Peer({
                initiator,
                stream: this.stream,
                trickle: false
            });
            peer.on('signal', (data) => {
                this.channel.trigger('client-signal-' + userId, {
                    userId: this.user.id,
                    data: data,
                });
            }).on('connect', () => {
                console.log('CONNECT');
            }).on('stream', (stream) => {
                const videoThere = this.$refs['video-there'];
                videoThere.srcObject = stream;
            }).on('close', () => {
                const peer = this.peers[userId];
                if (peer !== undefined) peer.destroy();
                delete this.peers[userId];
            }).on('error', (err) => {
                console.log('err', err);
            });
            this.peers[userId] = peer;

            return peer;
        }
    }
}
</script>