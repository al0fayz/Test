<template>
    <div class="container">
        <h3>Selamat Datang {{ name }}</h3>
    </div>
</template>

<script>

export default {
    data() {
        return {
            email: "",
            name: "",
        };
    },
    methods: {
        async loadData() {
            await axios
                .get("/api/user/dashboard")
                .then(res => {
                    if (res.data.success) {
                        this.name = res.data.name;
                        this.email = res.data.email;
                    }
                })
                .catch(err => {});
        }
    },
    created() {
        this.loadData();
    },
    mounted(){
        
    },
};
</script>
