<template>
<div>
    <div class="row" v-if="blogList">
        <div class="col-md-4" v-for="dt in data.data">
            <div class="card card-widget">
              <div class="card-header">
                <div class="user-block">
                  <img class="img-circle" :src="images" alt="User Image">
                  <span class="username"><a href="#">{{ user }}</a></span>
                  <span class="description">created - {{ formatDate(dt.created_at) }}  
                    <a class="pl-1" @click.prevent="deleted(dt.id)"><i class="fas fa-trash red">
                        delete
                    </i></a></span>
                </div>
              </div>
              <!-- /.card-header -->
              <a @click.prevent="detail(dt.id)">
              <div class="card-body" style="display: block;">
                <div class="img-card-list">
                    <div v-html="imageBlog(dt.images)">
                    </div>
                </div>
                <p class="my-2">{{ dt.title }}</p>
                <span class="float-right text-muted">{{ dt.view }} View </span>
              </div>
              </a>
              <!-- /.card-body -->
            </div>
        </div>

        <div class="mt-4 ml-4">
                <pagination :data="data" @pagination-change-page="getResults" :limit="1"></pagination>
        </div>
    </div>
    <div v-else="blogList">
         <div class="row">
            <div class="col-md-12">
            <div class="card card-outline card-info">
                <div class="card-header">
                <h3 class="card-title">
                    update
                </h3>
                <div class="card-tools">
                        <button type="button" class="btn btn-warning" @click.prevent="back">back <i class="fas fa-angle-double-left"></i></button>
                </div>
                </div>
                     <BlogUpdate :data-id-props="id" @showList="backBlog" />
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    import BlogUpdate from './BlogUpdate.vue';
    export default {
        data(){
            return{
                data: {},
                images: '/img/avatar.png',
                user: '',
                blogList: true,
                id: 0,
            }
        },
        methods:{
            async loadData(){
                this.$Progress.start()
                await axios.get('/api/user/blog').then((res) => {
                    if(res.data.success){
                        this.data = res.data.blog
                        this.user = res.data.user
                        if(res.data.images != ''){
                            this.images = res.data.images
                        }
                    }
                }).catch((err) => {

                });
                this.$Progress.finish()

            },
            getResults(page = 1) {
			    axios.get('/api/user/blog?page=' + page).then((res) => {
                    this.data = res.data.blog;
                }).catch((err) => {

                })
            },
            detail(dtId){
                this.blogList = false;
                this.id = dtId
            },
            back(){
                this.blogList = true;
            },
            backBlog(dt){
                this.blogList = dt
                Fire.$emit('successAction')

            },
            formatDate(dt){
                let tgl = dt.split(" ");
                let waktu = tgl[0].split("-");
                let month = 'Jan';
                
                switch (parseInt(waktu[1])) {
                    case 1:
                        month = 'Jan';
                        break;
                    case 2:
                        month = 'Feb';
                        break;
                    case 3:
                        month = 'Mar';
                        break;
                    case 4:
                        month = 'Apr';
                        break;
                    case 5:
                        month = 'May';
                        break;
                    case 6:
                        month = 'Jun';
                        break;
                    case 7:
                        month = 'Jul';
                        break;
                    case 8:
                        month = 'Aug';
                        break;
                    case 9:
                        month = 'Sep';
                        break;
                    case 10:
                        month = 'Oct';
                        break;
                    case 11:
                        month = 'Nov';
                        break;
                    case 12:
                        month = 'Dec';
                        break;
                    
                    default:
                        month = 'Jan';
                        break;
                }
                return waktu[2] + "-" + month + "-" + waktu[0];
            },
            deleted(id){
                Swal.fire({
                    title: 'Info',
                    text: 'Apakah Yakin',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                }).then((result) => {
                    this.$Progress.start()
                    if (result.value) {
                        axios.delete('/api/user/blog/'+id)
                        .then((res)=>{
                            if(res.data.success){
                                Swal.fire(
                                    'Success',
                                    'Success delete data',
                                    'success'
                                )
                                this.$Progress.finish()
                                Fire.$emit('successAction')
                            }else{
                                Swal.fire(
                                    'Sory',
                                    res.data.messages,
                                    'warning'
                                )
                            }
                        })
                        .catch(()=>{
                            Swal.fire(
                                'Sory',
                                res.data.messages,
                                'warning'
                            )
                        })
                    }
                })
            },
            imageBlog(dt){
                if(dt.includes('/')){
                    let src = dt;
                    return('<img class="img-blog-list" src="'+src+'" alt="Image" style="height: 200px;">');
                }else{
                    return('<p class="img-aliases">'+dt+'</p>');
                }
            },

        },
        created(){
            this.loadData()
            Fire.$on('successAction', () => {
                this.loadData();
            });
            //search
            Fire.$on('searchDataOnPage', () => {
                let q = this.$parent.searchData
                axios.get('/api/user/blog/search?search='+ q).then((res) => {
                    if(res.data.success){
                        if(res.data.count > 0){
                            this.data = res.data.data
                        }else{
                            this.data = {}
                        }
                    }
                }).catch((err) => {

                })
            });
            //reset
            Fire.$on('resetDataOnPage', () => {
                this.loadData();
            });
        },
        components: {
            BlogUpdate
        }
    }
</script>
