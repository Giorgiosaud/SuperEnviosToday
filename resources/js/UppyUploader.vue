<template>
    <form>
        <div class="image-container mb-3" v-if="previewPath">
            <img :src="previewPath" alt="Uploaded Image Preview">
        </div>
        <div class="form-group">
            <div ref="dashboardContainer"></div>
        </div>
            <section class="section"
            v-if="!autoProceed">
                <div class="buttons">
                    <b-button size="is-medium"
                              class="is-info"
                              @click="upload"
                              :disabled="disableUploadButton"
                              :loading="uploading"
                              icon-left="upload">
                        Upload
                    </b-button>
                </div>
            </section>
    </form>
</template>
<script>
    import Uppy from '@uppy/core';
    import UppyEs from '@uppy/locales/lib/Es_es';
    import XHRUpload from '@uppy/xhr-upload';
    import Dashboard from '@uppy/dashboard';
    import Form from '@uppy/form';
    import '@uppy/core/dist/style.css';
    import '@uppy/dashboard/dist/style.css';
    import '@uppy/webcam/dist/style.css'
    import Webcam from '@uppy/webcam'
    export default {
        props: {
            autoProceed:{
              type:Boolean,
              default:true
            },
            maxFileSizeInBytes: {
                type: Number,
                required: true
            },
            minNumberOfFiles:{
                type:Number,
                default:1
            },
            maxNumberOfFiles:{
                type:Number,
                default:10
            },
            value:{
                type:Array,
                required:true
            }
        },
        components:{
          Form
        },
        data() {
            return {
                payload: null,
                previewPath: null,
                disabled: true,
                tempUploadFiles:[],
                uploading:false
            }
        },
        mounted() {

                this.instantiateUppy()
        },
        computed:{
            tempFilesCount(){
                return this.tempUploadFiles.length;
            },
            disableUploadButton(){
              return this.tempFilesCount<this.minNumberOfFiles ||this.tempFilesCount>this.maxNumberOfFiles
            }
        },
        methods: {
            notify(type,message){
                this.$buefy.toast.open({
                    message: message,
                    type: type
                })

            },
            instantiateUppy() {
                this.uppy = Uppy({
                    locale:UppyEs,
                    autoProceed: this.autoProceed,
                    meta: {
                        username: 'John',
                        license: 'Creative Commons'
                    },
                    debug: true,
                    //autoProceed: true,
                    restrictions: {
                        maxFileSize: this.maxFileSizeInBytes,
                        minNumberOfFiles: this.minNumberOfFiles,
                        maxNumberOfFiles: this.maxNumberOfFiles,
                        allowedFileTypes: ['image/*', 'application/pdf']
                    }
                })
                    .use(Dashboard, {
                        hideUploadButton: true,
                        inline: true,
                        width:'100%',
                        target: this.$refs.dashboardContainer,
                        replaceTargetContent: true,
                        showProgressDetails: true,
                       // trigger: '.UppyModalOpenerBtn',
                        note: 'Solo Imagenes o pdf, hasta 10 archivos, de maximo 2 MB',
                        metaFields: [
                            { id: 'name', name: 'Name', placeholder: 'file name' },
                            { id: 'caption', name: 'Caption', placeholder: 'describe what the image is about' }
                        ],
                        browserBackButtonClose: true,
                        locale:{
                            strings:{
                                youCanOnlyUploadX: {
                                    0: 'Solo puedes subir %{smart_count} archivo',
                                    1: 'Solo puedes subir %{smart_count} archivos'
                                },
                                youHaveToAtLeastSelectX: {
                                    0: 'Debes seleccionar almenos %{smart_count} archivo',
                                    1: 'Debes seleccionar almenos %{smart_count} archivos'
                                },
                                exceedsSize2: 'Este archivo supera el tamaño permitido: %{size}',
                                youCanOnlyUploadFileTypes: 'Solo puedes subir archivos de tipo: %{types}',
                                companionError: 'Falló la coneccion',
                                dropPasteImport: 'Arrastre archivos aca, peguelos, o importelos desde %{browse}',
                                browse: 'su computador aqui',
                                poweredBy2:'Powered by Giorgiosaud'
                            },
                        }
                    })
                    .use(Webcam, { target: Dashboard})
                    .use(XHRUpload, {
                        limit: 1,
                        endpoint: '/api/file/upload',
                        formData: true,
                        fieldName: 'file',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // from <meta name="csrf-token" content="{{ csrf_token() }}">
                        }
                    });
                this.uppy.on('file-added', (file) => {
                    this.$emit('file-added',file)
                    this.tempUploadFiles.push(file.id)
                })
                this.uppy.on('file-removed', (file) => {
                    this.$emit('file-removed',file)
                    this.tempUploadFiles.splice(this.tempUploadFiles.indexOf(file),1);
                })
                this.uppy.on('complete', (event) => {
                    event.successful.forEach(file=>{
                        this.payload = file.response.body.path;
                        this.value.push(file.response.body);
                        this.$emit('input',this.value)
                        this.disabled = false;
                    })
                });


            },
            async upload(){
                try {
                    this.uploading = true;
                    await this.uppy.upload()
                }catch(error){
                    console.log(error)
                } finally {
                    this.uploading=false;
                }
            },
            updatePreviewPath({path}) {
                this.previewPath = path;

                return this;
            },
            resetUploader() {
                this.uppy.reset();
                this.disabled = true;
                this.$emit('input',[])

                return this;
            },

        }
    };
</script>

<style scoped>

</style>
