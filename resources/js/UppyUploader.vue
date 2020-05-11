<template>
    <form>
        <div class="image-container mb-3" v-if="previewPath">
            <img :src="previewPath" alt="Uploaded Image Preview">
        </div>
        <div class="form-group">
            <div ref="dashboardContainer"></div>
        </div>
            <section class="section">
                <div class="buttons">
                    <b-button size="is-medium"
                              class="is-info"
                              @click="upload"
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
    import XHRUpload from '@uppy/xhr-upload';
    import Dashboard from '@uppy/dashboard';
    import Form from '@uppy/form';
    import '@uppy/core/dist/style.css';
    import '@uppy/dashboard/dist/style.css';
    import '@uppy/webcam/dist/style.css'
    import Webcam from '@uppy/webcam'
    export default {
        props: {
            maxFileSizeInBytes: {
                type: Number,
                required: true
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
                uploading:false
            }
        },
        mounted() {
            this.instantiateUppy()
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
                    debug: true,
                    //autoProceed: true,
                    restrictions: {
                        maxFileSize: this.maxFileSizeInBytes,
                        minNumberOfFiles: 1,
                        maxNumberOfFiles: 10,
                        allowedFileTypes: ['image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf']
                    }
                })
                    .use(Dashboard, {
                        hideUploadButton: true,
                        inline: true,
                        target: this.$refs.dashboardContainer,
                        replaceTargetContent: true,
                        showProgressDetails: true,
                        trigger: '.UppyModalOpenerBtn',
                        note: 'Images and video only, 2–3 files, up to 1 MB',
                        metaFields: [
                            { id: 'name', name: 'Name', placeholder: 'file name' },
                            { id: 'caption', name: 'Caption', placeholder: 'describe what the image is about' }
                        ],
                        browserBackButtonClose: true
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

                this.uppy.on('complete', (event) => {
                    event.successful.forEach(file=>{
                        this.payload = file.response.body.path;
                        this.value.push(file.response.body);
                        this.$emit('input',this.value)
                        this.disabled = false;
                    })
                });
                this.uppy.on('file-removed', (file) => {
                        console.log('Removed file', file)
                })
            },
            async upload(){
                this.uploading=true;
                await this.uppy.upload()
                this.uploading=false;
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
