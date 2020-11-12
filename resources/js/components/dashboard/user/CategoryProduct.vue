<template>
    <div>
        <div class="card mt-2">
            <div class="card-header">
                <h3 class="card-title">Categorys List</h3>

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
                    <th>Desc</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody v-if="available">
                    <tr v-for="dt in data.data" :key="dt.id">
                        <td>{{ dt.category_name }}</td>
                        <td>{{ dt.desc }}</td>
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
                        <td colspan="8">Data not found</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">{{ editMode ? 'edit' : 'New'}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Category Name</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.category_name" class="form-control" name="category_name" :class="{ 'is-invalid': form.errors.has('category_name') }">
                            <has-error :form="form" field="category_name"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Description</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.desc" class="form-control" name="desc" :class="{ 'is-invalid': form.errors.has('desc') }">
                            <has-error :form="form" field="desc"></has-error>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">cancel</button>
                    <button type="button" @click.prevent="editMode ? editData() : saveData()" class="btn btn-primary" :disabled="run">Save</button>
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
                    category_name: '',
                    desc: '',
                }),
                available: false,
                editMode: false,
                run: false,
            }
        },
        methods:{
            async loadData(){
                await axios.get('/api/user/category').then((res) => {
                    if(res.data.data.length > 0){
                        this.available = true;
                    }
                    this.data = res.data;

                }).catch((err)=> {

                });
            },
            getResults(page = 1) {
                let q = this.$parent.searchData
                axios.get('/api/user/categoryr?page=' + page).then((res) => {
                    this.data = res.data;
                }).catch((err) => {

                })
               
            },
            saveData(){ 
                this.$Progress.start()
                this.run = true
                this.form.post('/api/user/category').then((res) => {
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
                            'Internal server error',
                            "error"
                        )
                    }
                    this.run = false
                    this.$Progress.finish()
                }).catch((err) => {
                    this.run = false
                });
            },
            editData(){
                this.$Progress.start()
                this.run = true
                this.form.put('/api/user/category/'+this.form.id).then((res) => {
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
                            'Internal server error',
                            "error"
                        )
                    }
                    this.run = false
                    this.$Progress.finish()
                }).catch((err) => {
                    this.run = false
                });
            },
            deleteData(id){
                Swal.fire({
                    title: 'sory',
                    text: 'delete data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    this.$Progress.start()
                    if (result.value) {
                        this.form.delete('/api/user/category/'+id)
                        .then((res)=>{
                            if(res.data.success){
                                Swal.fire(
                                    'success',
                                   'Success',
                                    'success'
                                )
                                this.$Progress.finish()
                                Fire.$emit('successAction')
                            }else{
                                Swal.fire(
                                    'maaf',
                                    'Internal server errors',
                                    'warning'
                                )
                            }
                        })
                        .catch(()=>{
                            Swal.fire(
                                'maaf',
                                'Internal server errors',
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
        },
    }
</script>
