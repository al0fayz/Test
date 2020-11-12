<template>
    <div>
        <div class="card mt-2">
            <div class="card-header">
                <h3 class="card-title">Product List</h3>

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
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody v-if="available">
                    <tr v-for="dt in data.data" :key="dt.id">
                        <td>{{ dt.nama }}</td>
                        <td>{{ dt.deskripsi }}</td>
                        <td>{{ dt.category.category_name }}</td>
                        <td>{{ dt.stok }}</td>
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
                        <label for="zone" class="col-sm-4 control-label">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.nama" class="form-control" name="nama" :class="{ 'is-invalid': form.errors.has('nama') }">
                            <has-error :form="form" field="namas"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Description</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.deskripsi" class="form-control" name="deskripsi" :class="{ 'is-invalid': form.errors.has('deskripsi') }">
                            <has-error :form="form" field="deskripsi"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Stok</label>
                        <div class="col-sm-8">
                            <input type="number" v-model="form.stok" min="1" class="form-control" name="stok" :class="{ 'is-invalid': form.errors.has('stok') }">
                            <has-error :form="form" field="stok"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="role" class="col-sm-4 control-label">category</label>
                        <div class="col-sm-8">
                            <select class="form-control select2" v-model="form.category_id">
                                <option v-for="value in cat" :value="value.id" :key="value.id">{{ value.category_name }}</option>
                            </select>
                            <has-error :form="form" field="category_id"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">Images</label>
                        <div class="col-sm-8">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" :class="{ 'is-invalid': form.errors.has('file') }" ref="myFiles" @change="getFile"/>
                                <label class="custom-file-label" for="exampleInputFile">Documents Upload: {{ (this.fileUpload != null) ? this.fileUpload.name : ''}}</label>
                            </div>
                            <has-error :form="form" field="file"></has-error>
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
                    nama: '',
                    deskripsi: '',
                    stok: 0,
                    category_id: '',
                    file: '',
                }),
                available: false,
                editMode: false,
                run: false,
                cat: {},
                fileUpload: '',
            }
        },
        methods:{
            getFile(e){
                let file = e.target.files[0];
                this.fileUpload = file;
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    Swal.fire(
                        'maaf',
                        'file terlalu besar',
                        "error"
                    )
                    return false;
                }
                if(file['type'] === 'image/jpg' || file['type'] === 'image/png' || file['type'] === 'image/jpeg'){
                    reader.onloadend = (file) => {
                        this.form.file = reader.result
                    }
                    reader.readAsDataURL(file);
                }else{
                    Swal.fire(
                       'maaf',
                       'Format tidak di dukung',
                        "error"
                    )
                    return false;
                }
            },
            async loadCategory(){
                await axios.get('/api/user/category').then((res) => {
                    this.cat = res.data.data;

                }).catch((err)=> {

                });
            },
            async loadData(){
                await axios.get('/api/user/product').then((res) => {
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
                let q = this.$parent.searchData
                axios.get('/api/user/product?page=' + page).then((res) => {
                    this.data = res.data;
                }).catch((err) => {

                })
               
            },
            saveData(){ 
                this.$Progress.start()
                this.run = true
                this.form.post('/api/user/product').then((res) => {
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
                this.form.put('/api/user/product/'+this.form.id).then((res) => {
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
                        this.form.delete('/api/user/product/'+id)
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
            await this.loadCategory();
            Fire.$on('successAction', () => {
                this.loadData();
            });
        },
    }
</script>
