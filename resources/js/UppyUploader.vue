<template>
  <form>
    <div
      v-if="previewPath"
      class="image-container mb-3">
      <img
        :src="previewPath"
        alt="Uploaded Image Preview">
    </div>
    <div class="form-group">
      <div ref="dashboardContainer"/>
    </div>
    <section
      v-if="!autoProceed"
      class="section">
      <div class="buttons">
        <b-button
          size="is-medium"
          class="is-info"
          :disabled="disableUploadButton"
          :loading="uploading"
          icon-left="upload"
          @click="upload">
          Upload
        </b-button>
      </div>
    </section>
  </form>
</template>
<script>
import Uppy from '@uppy/core';
import UppyEs from '@uppy/locales/lib/es_ES';
import XHRUpload from '@uppy/xhr-upload';
import Dashboard from '@uppy/dashboard';
import Form from '@uppy/form';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import '@uppy/webcam/dist/style.css';
import Webcam from '@uppy/webcam';

export default {
  components: {
    // eslint-disable-next-line vue/no-unused-components
    Form,
  },
  props: {
    autoProceed: {
      type: Boolean,
      default: true,
    },
    maxFileSizeInBytes: {
      type: Number,
      required: true,
    },
    minNumberOfFiles: {
      type: Number,
      default: 1,
    },
    maxNumberOfFiles: {
      type: Number,
      default: 10,
    },
    value: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      payload: null,
      previewPath: null,
      disabled: true,
      tempUploadFiles: [],
      uploading: false,
      localAutoProceed: true,
    };
  },
  computed: {
    tempFilesCount() {
      return this.tempUploadFiles.length;
    },
    disableUploadButton() {
      return this.tempFilesCount < this.minNumberOfFiles || this.tempFilesCount > this.maxNumberOfFiles;
    },
  },
  created() {
    this.localAutoProceed = this.autoProceed;
  },
  mounted() {
    this.instantiateUppy();
  },
  methods: {
    notify(type, message) {
      this.$buefy.toast.open({
        message,
        type,
      });
    },
    async instantiateUppy() {
      this.uppy = Uppy({
        locale: UppyEs,
        autoProceed: this.localAutoProceed,
        meta: {
          username: 'Jorge Saud',
          license: 'Creative Commons',
        },
        debug: true,
        // autoProceed: true,
        restrictions: {
          maxFileSize: this.maxFileSizeInBytes,
          minNumberOfFiles: this.minNumberOfFiles,
          maxNumberOfFiles: this.maxNumberOfFiles,
          allowedFileTypes: ['image/*', 'application/pdf'],
        },
      })
        .use(Dashboard, {
          hideUploadButton: true,
          inline: true,
          width: '100%',
          target: this.$refs.dashboardContainer,
          replaceTargetContent: true,
          showProgressDetails: true,
          // trigger: '.UppyModalOpenerBtn',
          note: 'Solo Imagenes o pdf, hasta 10 archivos, de maximo 2 MB',
          metaFields: [
            { id: 'name', name: 'Name', placeholder: 'file name' },
            { id: 'caption', name: 'Caption', placeholder: 'describe what the image is about' },
          ],
          browserBackButtonClose: true,
          showRemoveButtonAfterComplete: true,

          locale: {
            strings: {
              youCanOnlyUploadX: {
                0: 'Solo puedes subir %{smart_count} archivo',
                1: 'Solo puedes subir %{smart_count} archivos',
              },
              youHaveToAtLeastSelectX: {
                0: 'Debes seleccionar almenos %{smart_count} archivo',
                1: 'Debes seleccionar almenos %{smart_count} archivos',
              },
              exceedsSize2: 'Este archivo supera el tamaño permitido: %{size}',
              youCanOnlyUploadFileTypes: 'Solo puedes subir archivos de tipo: %{types}',
              companionError: 'Falló la coneccion',
              dropPasteImport: 'Arrastre archivos aca, peguelos, o importelos desde %{browse}',
              browse: 'su computador aqui',
              poweredBy2: 'Powered by Giorgiosaud',
            },
          },
        })
        .use(Webcam, { target: Dashboard })
        .use(XHRUpload, {
          limit: 1,
          endpoint: '/api/file/upload',
          formData: true,
          fieldName: 'file',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
              .getAttribute('content'),
          },
        });
      this.uppy.on('file-added', (file) => {
        this.$emit('file-added', file);
        this.tempUploadFiles.push(file.id);
      });
      this.uppy.on('file-removed', (file) => {
        this.$emit('file-removed', file);
        this.tempUploadFiles.splice(this.tempUploadFiles.indexOf(file), 1);
      });
      this.uppy.on('complete', (event) => {
        event.successful.forEach((file) => {
          this.payload = file.response.body.path;
          // eslint-disable-next-line vue/no-mutating-props
          this.value.push(file.response.body);
          this.$emit('input', this.value);
          this.disabled = false;
        });
      });
      if (this.value.length) {
        await this.value.map(async (file) => {
          await this.getBlob(file);
        });
        this.uppy.getFiles().forEach((file) => {
          this.uppy.setFileState(file.id, {
            progress: { uploadComplete: true, uploadStarted: false },
          });
        });
        if (this.localAutoProceed === false) {
          this.localAutoProceed = true;
        }
      }
      this.uppy.on('file-removed', async (file, reason) => {
        if (reason === 'removed-by-user') {
          await this.sendDeleteRequestForFile(file);
        }
      });
    },
    async sendDeleteRequestForFile(file) {
      this.$emit('remove-file', file.meta.id);
    },
    async getBlob({ path, id }) {
      return fetch(path)
        .then((response) => response.blob()) // returns a Blob
        .then((blob) => {
          this.uppy.addFile({
            meta: { id },
            name: 'image.jpg',
            type: blob.type,
            data: blob,
            source: path,
          });
        });
    },
    async upload() {
      try {
        this.uploading = true;
        await this.uppy.upload();
      } finally {
        this.uploading = false;
      }
    },
    updatePreviewPath({ path }) {
      this.previewPath = path;

      return this;
    },
    resetUploader() {
      this.uppy.reset();
      this.disabled = true;
      this.$emit('input', []);

      return this;
    },
  },
};

</script>

<style scoped>

</style>
