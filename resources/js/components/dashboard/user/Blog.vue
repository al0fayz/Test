<template>
    <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                Blog List
              </h3>
              
            </div>
            <!-- /.card-header -->
            <div class="card-body pad">
              <div class="mb-3">
                <form @submit.prevent="saveData">
                    <div class="form-group row">
                        <label for="title" class="col-sm-4 control-label">Judul</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" v-model="form.title" name="title" :class="{ 'is-invalid': form.errors.has('title') }" placeholder="title">
                            <has-error :form="form" field="title"></has-error>
                            <p class="text-help-info"><sup>*</sup>untuk judul hanya alfabhet, nomor dan spasi</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="desc" class="col-sm-4 control-label">Kategori</label>
                        <div class="col-sm-8">
                        <select class="form-control select2" v-model="form.category_id">
                            <option v-for="value in category" :value="value.id" :key="value.id">{{ value.category_name }}</option>
                        </select>
                        <has-error :form="form" field="category_id"></has-error>
                        <p class="text-help-info"><sup>*</sup>Jika kategori tidak di temukan!, kamu dapat menambahkan di kategori blog</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="desc" class="col-sm-4 control-label">Gambar Sampul</label>
                        <div class="col-sm-8">
                            <toggle-button @change="ImageOrText" :value="true" />
                            <div v-if="SelectImage">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file" @change="getFile" :class="{ 'is-invalid': form.errors.has('file') }">
                                    <label class="custom-file-label" for="inputGroupFile01">{{ (this.file != null) ? 'Documents Upload:' + this.file.name : 'Choose file '}}</label>
                                </div>
                                <p class="text-help-info"><sup>*</sup>Menerima format file: jpg, png;</p>
                                <has-error :form="form" field="file"></has-error>
                            </div>
                            <div v-else="SelectImage">
                                <input type="text" class="form-control" v-model="form.cover" name="cover" :class="{ 'is-invalid': form.errors.has('cover') }" placeholder="title">
                                <has-error :form="form" field="cover"></has-error>
                                <p class="text-help-info"><sup>*</sup>hanya alfabhet, nomor dan spasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="content">Kontent</label>
                        <textarea id="summernote"></textarea>
                        <p class="text-help-info"><sup>*</sup>jika unggahan blog gagal, Anda dapat mencoba menyisipkan gambar dengan url. terkadang tidak cukup ruang!</p>
                    </div>
                </form>
              </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-success" @click.prevent="saveData" :disabled="run"> Save</button>
            </div>
          </div>
        </div>
        <!-- /.col-->
      </div>
</template>

<script>
    export default {
        data(){
            return{
                form: new Form({
                    title: '',
                    category_id: '',
                    content: '',
                    file: null,
                    images: {},
                    cover: '',
                    select_image: true,
                }),
                category: {},
                file: null,
                run: false,
                SelectImage: true,
            }
        },
        methods: {
            
            async loadData(){
                this.$Progress.start()
                await axios.get('/api/user/getCategory').then((res) => {
                    this.category = res.data;
                }).catch((err) => {

                });
                $(document).ready(function() {
                    $('#summernote').summernote({
                        toolbar: [
                            // [groupName, [list of button]]
                            ['style', ['bold', 'italic', 'underline', 'clear', 'h1']],
                            ['font', ['strikethrough', 'superscript', 'subscript']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph', 'style']],
                            ['height', ['height']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video', 'hr']],
                            ['view', ['fullscreen', 'codeview','undo', 'redo','help']],
                        ],
                        height: 500,
                        tabsize: 2,
                        placeholder: 'write here...',
                        popover: {
                            image: [],
                            link: [],
                            air: []
                        },
                        maximumImageFileSize: 10524288,       // 1512KBc
                        // callbacks: {
                        //     onImageUpload: function(files, editor, welEditable){
                        //         let reader = new FileReader();
                        //         let limit = 1024 * 1024 * 2; 
                        //         var image = files[0];
                                
                        //         if(image['size'] > limit){
                        //             Swal.fire(
                        //                 "Sorry..!",
                        //                 "Your file is to large!",
                        //                 "error"
                        //             )
                        //             return false;
                        //         }else{
                        //             reader.onloadend = (image) => {
                        //                 let dt = reader.result;
                        //                 //save image and get url
                        //                 axios.post('/api/user/blog/images/upload',{
                        //                     file: dt
                        //                 }).then((res) => {
                        //                     if(res.data.success){
                        //                         //insert image to sumernote
                        //                         var show = $('<img>').attr('src', res.data.url);
                        //                         $('#summernote').summernote("insertNode", show[0]);
                        //                     }
                        //                 }).catch((err) => {

                        //                 });
                        //             }
                        //             reader.readAsDataURL(image);
                                    
                        //         }
                        //     }
                        // },
                    });
                });
                this.$Progress.finish()
            },
            
            getFile(e){
                let file = e.target.files[0];
                this.file = file;
                let reader = new FileReader();
                let limit = 1024 * 1024 * 2;
                if(file['size'] > limit){
                    Swal.fire(
                        'Maaf',
                        'File terlalu besar',
                        "error"
                    )
                    return false;
                }
                if(file['type'] === 'image/jpg' || file['type'] === 'image/png' || file['type'] == 'image/jpeg'){
                    reader.onloadend = (file) => {
                        this.form.file = reader.result
                    }
                    reader.readAsDataURL(file);
                }else{
                    Swal.fire(
                       'Maaf',
                        'Format tidak di dukung',
                        "error"
                    )
                    return false;
                }
            },
            saveData(){
                this.$Progress.start()
                this.run = true
                var content = $('#summernote').summernote('code');
                this.form.content = content;
                this.form.post('/api/user/blog').then((res) => {
                    if(res.data.success){
                        Swal.fire(
                            'Success',
                            'Success Simpan Data',
                            'success'
                        )
                        this.form.reset()
                        this.$router.push('/user/blogList')
                    }else{
                        Swal.fire(
                            'Maaf',
                            'Internal server Error',
                            'error'
                        )
                    }
                    this.run = false
                }).catch((err) => {
                    Swal.fire(
                        'Maaf',
                        'Internal server Error',
                        'error'
                    )
                    this.run = false
                });
                this.$Progress.finish()
            },
            ImageOrText(e){
                let val = e.value;
                this.SelectImage = val;
                this.form.select_image = val;
            },
        }, 
        created(){
            this.loadData()
        }
    }
</script>
