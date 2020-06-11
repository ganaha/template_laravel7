<template>
    <div class="container">
        <h1 class="text-center">グループビデオチャットのサンプル</h1>

        <!-- 入室者一覧 -->
        <div class="row">
            <div class="col-12">
                <div class="card" style="width: 18rem;">
                    <div class="card-header">
                        入室者一覧
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-for="member in members" :key="member.id">{{ member.name }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ビデオ一覧 -->
        <div class="row">
            <div class="col-sm">
                <div class="card">
                    <video class="card-img-top" ref="video-self" autoplay></video>
                    <div class="card-body">
                        <h5 class="card-title text-center">自分の映像</h5>
                    </div>
                </div>
            </div>
            <div class="col-sm" v-for="member in members" :key="member.id">
                <div class="card text-center">
                    <video class="card-img-top" :ref="'video-' + member.id" autoplay></video>
                    <div class="card-body">
                        <h5 class="card-title">{{ member.name }}の映像</h5>
                        <button class="btn btn-primary btn-block" @click.prevent="startVideoChat(member.id)">Call</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- チャットメッセージ表示 -->
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item" v-for="message in messages" :key="message.timestamp">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ message.name }}</h5>
                            <small class="text-muted">{{ message.timestamp }}</small>
                        </div>
                        <p class="mb-1">{{ message.message }}</p>
                    </li>
                </ul>
            </div>
        </div>
        <!-- チャットメッセージ入力 -->
        <div class="row">
            <div class="col-12">
                <div class="input-group">
                    <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." v-model="text" autofocus />
                    <span class="input-group-btn">
                        <button class="btn btn-primary" @click.prevent="send">Send</button>
                    </span>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.row {
    margin-top: 30px;
}
.btn {
    margin-left: 10px;
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
            text: '',
            messages: [],
        }
    },
    mounted() {
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        navigator.mediaDevices.getUserMedia({ video: true, audio: true }).then((stream) => {
            // 自video表示
            const videoHere = this.$refs['video-self'];
            videoHere.srcObject = stream;
            this.stream = stream;

            // ビデオチャット
            this.channel = Echo.join('chat.1').here((members) => {
                // 入室者一覧取得
                this.members = Object.keys(members)
                        .map((key) => members[key])
                        .filter((u) => u.id !== this.user.id);
            }).joining((member) => {
                // 入室者を追加
                this.members.push(member);
                // Video Start
                this.startVideoChat(member.id);
            }).leaving((member) => {
                // 退室者を除外
                this.members = this.members.filter((u) => u.id !== member.id);
            }).listenForWhisper('client-signal-' + this.user.id, (signal) => {
                // 相手video受信
                const userId = signal.userId;
                const peer = this.getPeer(userId, false);
                peer.signal(signal.data);
            }).listen('PresenceChannelEvent', (e) => {
                this.messages.push(e);
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
                const videoThere = this.$refs['video-' + userId];
                videoThere[0].srcObject = stream;
            }).on('close', () => {
                const peer = this.peers[userId];
                if (peer !== undefined) peer.destroy();
                delete this.peers[userId];
            }).on('error', (err) => {
                console.log('err', err);
            });
            this.peers[userId] = peer;

            return peer;
        },
        // メッセージ送信
        send() {
            console.log('send', this.text);
            axios.post('/chat/presence/1', {
              message: this.text,
            }).then((res) => {
                this.text = '';
            }).catch((e) => {
                console.log('error', e);
            });
        }
    }
}
</script>