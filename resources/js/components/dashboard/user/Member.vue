<template>
    <div>
        <div class="card mt-2">
            <div class="card-header">
                <h3 class="card-title">Member List</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-success" @click.prevent="newModal">New <i class="fas fa-plus"></i></button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody v-if="available">
                    <tr v-for="dt in data.data" :key="dt.id">
                        <td>{{ dt.detail.first_name }}</td>
                        <td>{{ dt.detail.last_name }}</td>
                        <td>{{ dt.email }}</td>
                        <td>{{ dt.detail.tanggal_lahir }}</td>
                        <td>{{ dt.detail.jenis_kelamin }}</td>

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
                        <label for="zone" class="col-sm-4 control-label">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.first_name" class="form-control" name="first_name" :class="{ 'is-invalid': form.errors.has('first_name') }">
                            <has-error :form="form" field="first_name"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.last_name" class="form-control" name="last_name" :class="{ 'is-invalid': form.errors.has('last_name') }">
                            <has-error :form="form" field="last_name"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="zone" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-8">
                            <input type="text" v-model="form.email" class="form-control" name="email" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="disabled" class="col-sm-4 control-label">Jenis Kelamin</label>
                        <div class="col-sm-8">
                            <select name="jenis_kelamin" v-model="form.jenis_kelamin" class="form-control">
                                <option value="L">Laki Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <has-error :form="form" field="jenis_kelamin"></has-error>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 control-label">Tanggal Lahir</label>
                        <div class="col-sm-8">
                            <date-picker v-model="form.tanggal_lahir" valueType="format"></date-picker>
                        </div>
                        <has-error :form="form" field="tanggal_lahir"></has-error>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-sm-4 control-label">Password</label>
                        <div class="col-sm-8">
                            <input type="password" v-model="form.password" class="form-control" name="password" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password">
                            <has-error :form="form" field="password"></has-error>
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
import DatePicker from 'vue2-datepicker';
    export default {
        data(){
            return{
                data: {},
                form: new Form({
                    id: '',
                    email: '',
                    jenis_kelamin: 'L',
                    first_name: '',
                    last_name: '',
                    tanggal_lahir: '',
                    password: '',
                }),
                available: false,
                editMode: false,
                run: false,
            }
        },
        methods:{
            async loadData(){
                await axios.get('/api/user/member').then((res) => {
                    if(res.data.data.length > 0){
                        this.available = true;
                    }
                    this.data = res.data;

                }).catch((err)=> {

                });
            },
            getResults(page = 1) {
                let q = this.$parent.searchData
                if(q == ''){
                    axios.get('/api/user/member?page=' + page).then((res) => {
                        this.data = res.data;
                    }).catch((err) => {

                    })
                }else{
                    let q = this.$parent.searchData
                    axios.get('/api/user/member/search?search='+ q + '&page=' + page).then((res) => {
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
                }
            },
            saveData(){ 
                this.$Progress.start()
                this.run = true
                this.form.post('/api/user/member').then((res) => {
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
                this.form.put('/api/user/member/'+this.form.id).then((res) => {
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
                        this.form.delete('/api/user/member/'+id)
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
                this.form.first_name = dt.detail.first_name;
                this.form.last_name = dt.detail.last_name;
                this.form.jenis_kelamin = dt.detail.jenis_kelamin;
                this.form.tanggal_lahir = dt.detail.tanggal_lahir;
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
                axios.get('/api/user/member/search?search='+ q).then((res) => {
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
        },
        components: { DatePicker },
    }
</script>
