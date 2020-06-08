<template>
    <form @submit.prevent="submit">
        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Push</button>
    </form>
</template>

<script>
export default {
    props: ["id"],
    methods: {
        submit() {
            if (!confirm('Pusher? Are you sure?')) return;
            axios.post('/admin/users/' + this.id + '/pusher').then((res) => {
                console.log(res);
            }).catch((e) => {
                console.log(e);
            });
        }
    }
}
</script>