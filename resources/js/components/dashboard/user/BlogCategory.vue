<template>
    <div>
        <div class="card mt-2">
            <div class="card-header">
                <h3 class="card-title">Category List</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-success" @click.prevent="newModal">New <i class="fas fa-plus"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody v-if="available">
                    <tr v-for="dt in data.data" :key="dt.id">
                        <td>{{ dt.category_name }}</td>
                        <td>
                            <a href="#" @click="editModal(dt)">
                                <i class="fas fa-edit yellow">
                                    edit
                                </i>
                            </a>
                            ||
                            <a href="#" @click="deleteData(dt.id)">
                                <i class="fas fa-trash red">
                                    delete
                                </i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else="available">
                    <tr class="text-center">
                        <td colspan="3">Not found</td>
                    </tr>
                </tbody>
            </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                  <pagination :data="data" @pagination-change-page="getResults" :limit="1"></pagination>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="tldModal" tabindex="-1" role="dialog" aria-labelledby="createModal" aria-hidden="true">
            <form @submit.prevent="editMode ? editData() : saveData()" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ editMode ? 'Edit Data' : 'Add Data' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" v-model="form.category_name" class="form-control" name="category_name" :class="{ 'is-invalid': form.errors.has('category_name') }">
                    </div>
                    <has-error :form="form" field="category_name"></has-error>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                    <button type="button" @click.prevent="editMode ? editData() : saveData()" class="btn btn-primary">Save</button>
                </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return{
                data: {},
                form: new Form({
                    id: '',
                    category_name: ''
                }),
                available: false,
                editMode: false,
            }
        },
        methods:{
            async loadData(){
                await axios.get('/api/user/blogCategory').then((res) => {
                    if(res.data.data.length > 0){
                        this.available = true;
                    }else{
                        this.available = false;
                    }
                    this.data = res.data;

                }).catch((err)=> {

                });
            },
            getResults(page = 1) {
			    axios.get('/api/user/blogCategory?page=' + page).then((res) => {
                    this.data = res.data;
                }).catch((err) => {

                })
            },
            saveData(){ 
                this.$Progress.start()
                this.form.post('/api/user/blogCategory').then((res) => {
                    let dt = res.data
                    $('#tldModal').modal('hide')
                    if(dt.success){
                        toast.fire({
                            icon: 'success',
                            title: 'Success'
                        });
                        Fire.$emit('successAction')

                    }else{
                        Swal.fire(
                            'Maaf',
                            'Failed Save Data',
                            "error"
                        )
                    }
                    this.$Progress.finish()
                }).catch((err) => {
                    
                });
            },
            editData(){
                this.$Progress.start()
                this.form.put('/api/user/blogCategory/'+this.form.id).then((res) => {
                    let dt = res.data
                    $('#tldModal').modal('hide')
                    if(dt.success){
                        toast.fire({
                            icon: 'success',
                            title: 'Success Save data'
                        });
                        Fire.$emit('successAction')

                    }else{
                        Swal.fire(
                            'Maaf',
                            'Failed Save data',
                            "error"
                        )
                    }
                    this.$Progress.finish()
                }).catch((err) => {
                    
                });
            },
            deleteData(id){
                Swal.fire({
                    title: 'Are sure',
                    text: 'Data will be delete',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    this.$Progress.start()
                    if (result.value) {
                        this.form.delete('/api/user/blogCategory/'+id)
                        .then((res)=>{
                            if(res.data.success){
                                Swal.fire(
                                    'success',
                                    'Data deleted',
                                    'success'
                                )
                                this.$Progress.finish()
                                Fire.$emit('successAction')
                            }else{
                                Swal.fire(
                                    'Gagal',
                                    'Internal server error',
                                    'warning'
                                )
                            }
                        })
                        .catch(()=>{
                            Swal.fire(
                                this.__('notif.fail'),
                                this.__('notif.delete.oops'),
                                'warning'
                            )
                        })
                    }
                })
            },
            //modal
            newModal(){
                this.editMode = false,
                this.form.reset();
                $('#tldModal').modal('show');
            },
            editModal(dt){
                this.editMode = true;
                this.form.reset()
                $('#tldModal').modal('show')
                this.form.fill(dt)
            }
        },
        async created(){
            await this.loadData();
            Fire.$on('successAction', () => {
                this.loadData();
            });
            //search
            Fire.$on('searchDataOnPage', () => {
                let q = this.$parent.searchData
                axios.get('/api/user/blogCategory/search?search='+ q).then((res) => {
                    if(res.data.success){
                        if(res.data.count > 0){
                            this.data = res.data.data
                            this.available = true
                        }else{
                            this.data = {}
                            this.available = false
                        }
                    }
                }).catch((err) => {

                })
            });
            //reset
            Fire.$on('resetDataOnPage', () => {
                this.loadData();
            });
        }
    }
</script>
