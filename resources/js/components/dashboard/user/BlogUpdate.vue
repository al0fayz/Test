<template>
    <div>
        <div class="card-body pad">
            <div class="mb-3">
            <form>
                <div class="form-group row">
                    <label for="title" class="col-sm-4 control-label">Title</label>

                    <div class="col-sm-8">
                        <input type="text" class="form-control" v-model="form.title" name="title" :class="{ 'is-invalid': form.errors.has('title') }" placeholder="Title">
                        <has-error :form="form" field="title"></has-error>
                        <!--<p class="text-help-info"><sup>*</sup>for title only alfabhet, number and spaces</p> -->
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
                        <label for="desc" class="col-sm-4 control-label">Image</label>
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
                                <input type="text" class="form-control" v-model="form.cover" name="cover" :class="{ 'is-invalid': form.errors.has('cover') }" placeholder="Image">
                                <has-error :form="form" field="cover"></has-error>
                                <p class="text-help-info"><sup>*</sup>hanya alfabhet, nomor dan spasi</p>
                            </div>
                        </div>
                    </div>
                <div class="form-group">
                    <label for="content">kontent</label>
                    <textarea id="summernote"></textarea>
                    <p class="text-help-info"><sup>*</sup>jika unggahan blog gagal, Anda dapat mencoba menyisipkan gambar dengan url. terkadang tidak cukup ruang!</p>
                </div>
            </form>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-success" @click.prevent="saveData" :disabled="run"> Updated</button>
        </div>
    </div>          
</template>

<script>
    export default{
        props : {
            dataIdProps: {
                type: Number,
                required: true
            }
        },
        data(){
            return{
                form: new Form({
                    content: '',
                    title: '',
                    file: null,
                    category_id: '',
                    cover: '',
                    select_image: true,
                }),
                run: false,
                file: null,
                category: {},
                SelectImage: true,
            }
        },
        methods: {
            async loadData(){
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
                        // callbacks: {
                        //     onImageUpload: function(image) {
                                
                        //     }
                        // },
                    });
                });
            },
            async getData(){
                this.$Progress.start()
                const id = this.dataIdProps
                if(id !== 0){
                    await axios.get('/api/user/blog/'+ id).then((res) => {
                        if(res.data.success){
                            let content = res.data.data.content
                            $('#summernote').summernote('code', res.data.data.content);
                            this.form.title = res.data.data.title;
                            this.form.category_id = res.data.data.category_id;
                        }
                    }).catch((err) => {

                    })
                }
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
                      'maaf',
                      'Format tidak di dukung',
                        "error"
                    )
                    return false;
                }
            },
            saveData(){
                this.$Progress.start()
                this.run = true
                const id = this.dataIdProps
                var content = $('#summernote').summernote('code');
                this.form.content = content;
                this.form.put('/api/user/blog/'+id).then((res) => {
                    if(res.data.success){
                        Swal.fire(
                            'success',
                            'Save data Success!',
                            'success'
                        )
                        this.form.reset()
                        this.$emit('showList', true)
                    }else{
                        Swal.fire(
                            'Maaf',
                            'Internal server error',
                            'error'
                        )
                    }
                    this.run = false
                }).catch((err) => {
                   Swal.fire(
                        'Maaf',
                        'Internal server error',
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
            this.getData()
        }
    }
</script>