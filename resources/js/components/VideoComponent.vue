<template>
    <div class="container">
        <h1 class="text-center">ビデオチャットのサンプル</h1>
        <br>
        <div class="row">
            <div class="col-12">
                <div class="card" style="padding:15px;">
                    <div v-for="member in members" :key="member.id">
                        <a href="#" @click.prevent="startVideoChat(member.id)">「@{{ member.name }}」さんと通話を開始する</a>
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
            members: [],
        }
    },
    mounted() {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        navigator.mediaDevices.getUserMedia({ video: true, audio: true }).then((stream) => {
            // 自video表示
            const videoHere = this.$refs['video-here'];
            videoHere.srcObject = stream;
            this.stream = stream;

            this.channel = Echo.join('presence-video-chat').here((members) => {
                // 入室者一覧取得
                this.members = Object.keys(members)
                        .map((key) => members[key])
                        .filter((u) => u.id !== this.user.id);
            }).joining((member) => {
                // 入室者を追加
                this.members.push(member);
            }).leaving((member) => {
                // 退室者を除外
                this.members = this.members.filter((u) => u.id !== member.id);
            }).listenForWhisper('client-signal-' + this.user.id, (signal) => {
                // 相手video受信
                const userId = signal.userId;
                const peer = this.getPeer(userId, false);
                peer.signal(signal.data);
            });
        }).catch((err) => {
            console.log('catch', err);
        });
    },
    methods: {
        // 開始
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
                // video送信
                this.channel.whisper('client-signal-' + userId, {
                    userId: this.user.id,
                    data: data,
                });
            }).on('connect', () => {
                // 接続
                console.log('CONNECT');
            }).on('stream', (stream) => {
                // video受信
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