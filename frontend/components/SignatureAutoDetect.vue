<template>
  <div class="p-4 space-y-4">
    <input type="file" accept="image/*" @change="onFileChange" />

    <div v-if="imageUrl">
      <img ref="imageRef" :src="imageUrl" class="max-w-full" />
    </div>

    <button 
      v-if="imageUrl" 
      @click="saveCroppedImage"
      class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
    >
      Save Cropped Image
    </button>

    <div v-if="output">
      <h3 class="font-bold mt-4">Saved Result:</h3>
      <img :src="output" class="border p-2 bg-white max-w-full" />
    </div>
  </div>
</template>

<script>
import Cropper from 'cropperjs'

export default {
  name: 'SignatureEnhancer',
  
  props: {
    href:{
      type:String,
      default:'',
    },
    params:{
      type:Object,
      default(){ return {} },
    },
  },

  data() {
    return {
      imageUrl: null,
      output: null,
      cropper: null
    }
  },

  methods: {
    async onFileChange(e) {
      const file = e.target.files[0]
      if (!file) return

      this.imageUrl = URL.createObjectURL(file)

      // Menunggu DOM selesai di-render ulang untuk memunculkan tag <img>
      await this.$nextTick()

      // Hapus instance cropper lama jika ada
      if (this.cropper) {
        this.cropper.destroy()
      }

      // Gunakan this.$refs untuk mengambil elemen gambar
      this.cropper = new Cropper(this.$refs.imageRef, {
        aspectRatio: NaN,
        viewMode: 1,
        background: false,
        autoCropArea: 1
      })
    },
    // FUNGSI UTAMA: Mengambil gambar tepat di area Crop Handle
    async saveCroppedImage() {
      // 1. Ambil elemen seleksi (kotak handle) yang ada di DOM
      const cropperSelection = this.$el.querySelector('cropper-selection');
      
      if (!cropperSelection) {
        alert("Crop handle belum siap atau gambar belum dimuat!");
        return;
      }

      try {
        // 2. Minta Cropper v2 membuat Canvas khusus berbasis koordinat handle saat ini
        // Menggunakan $toCanvas() yang mengembalikan Promise (wajib await)
        const canvas = await cropperSelection.$toCanvas({
          // Anda bisa menentukan batas resolusi maksimal hasil file di sini (opsional)
          maxWidth: 2000,
          maxHeight: 2000
        });

        if (!canvas) {
          throw new Error("Gagal mengonversi seleksi menjadi canvas.");
        }

        // 3. Konversi canvas menjadi Data URL (base64) untuk ditampilkan atau disimpan
        this.output = canvas.toDataURL("image/png");

        // OPTIONAL: Jika Anda ingin mendownload file-nya langsung ke komputer user:
        this.downloadTrigger(this.output);

      } catch (error) {
        console.error("Gagal menyimpan potongan gambar:", error);
      }
    },

    // Fungsi Tambahan jika Anda ingin hasil potongannya langsung ter-download otomatis
    async downloadTrigger(base64Data) {
      try {
        const blob = await (await fetch(base64Data)).blob();
        const form = window.jsonToFormData(this.params);
        form.append("signature", blob, "signature.png");

        this.$http.post(this.href, form, {
          headers: { 'Content-Type': 'multipart/form-data' }
        } )
          .then(result => {
            this.saving = false;
            var psb = result.data;
            this.dataId = psb.id
            this.$emit('saved', psb);
          })
          .catch(err => {
            this.saving = false;
            console.log(err)
            var res = err.response;
            var code = res.status;
            this.$emit('error', false);
          });
      } catch (err) {
        alert(err.message || "Failed to generate enhanced signature");
      }
    }
  },
  mounted() {
    // Inisialisasi Cropper v2 saat komponen sudah ter-mount
    this.imageUrl = null; // Pastikan tidak ada gambar awal
    this.cropper = null; // Pastikan instance cropper awalnya null
    this.output = null; // Pastikan output awalnya null
  }
}
</script>