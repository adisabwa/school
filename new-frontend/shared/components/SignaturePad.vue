<template>
  <div class="p-4 max-w-xl">
    <div class="controls mb-3 flex gap-2 items-center">
      <el-button @click="undo" :disabled="!canUndo" class="btn" type="danger">Undo</el-button>
      <el-button @click="clearPad" class="btn" type="warning">Clear</el-button>
      <el-button @click="downloadEnhanced" type="primary">Simpan Tanda Tangan</el-button>
    </div>

    <div ref="wrapper" class="canvas-wrap border rounded min-w-[600px] min-h-[260px]">
      <canvas ref="canvas" class="w-full h-full" />
    </div>

    <div v-if="preview" class="mt-3">
      <h4 class="mb-1">Preview:</h4>
      <img :src="preview" alt="enhanced" class="border" />
    </div>
  </div>
</template>

<script>
import SignaturePad from "signature_pad";

export default {
  name: "SignaturePadEnhanced",
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
  emits: ['saved','error'],
  data() {
    return {
      canvas: null,
      sigpad: null,

      history: [],
      maxHistory: 20,

      preview: null,

      // configs
      SCALE: 4,
      MARGIN: 20,
      BRIGHT_THRESHOLD: 245,
      MIN_ALPHA: 40,
    };
  },

  computed: {
    canUndo() {
      return this.history.length > 0;
    },
  },

  mounted() {
    const c = this.$refs.canvas;
    this.canvas = c;

    const rect = c.getBoundingClientRect();
    c.width = Math.round(rect.width);
    c.height = Math.round(rect.height);

    const ctx = c.getContext("2d", { alpha: true });
    ctx.clearRect(0, 0, c.width, c.height);

    this.sigpad = new SignaturePad(c, {
      minWidth: 1.5,
      maxWidth: 2.5,
      penColor: "rgb(0,0,0)",
      backgroundColor: "rgba(0,0,0,0)",
      velocityFilterWeight: 0.7,
      throttle: 20,
    });

    this.history.push(JSON.stringify(this.sigpad.toData()));
  },

  beforeUnmount() {
    if (this.sigpad && this.sigpad.off) this.sigpad.off();
  },

  methods: {
    resizeCanvas() {
      const canvas = this.$refs.canvas
      const wrapper = this.$refs.wrapper

      if (!canvas || !wrapper) return

      // ukuran parent sebenarnya
      const width = wrapper.clientWidth
      const height = wrapper.clientHeight

      const ratio = Math.max(window.devicePixelRatio || 1, 1)

      canvas.width = width * ratio
      canvas.height = height * ratio
      canvas.getContext("2d").scale(ratio, ratio)
    },
    pushHistory() {
      try {
        const data = this.sigpad.toData();
        if (!data) return;
        this.history.push(JSON.stringify(data));
        if (this.history.length > this.maxHistory) {
          this.history.shift();
        }
      } catch (e) {}
    },

    undo() {
      if (!this.history.length) return;

      this.history.pop();
      const prev = this.history.length ? JSON.parse(this.history[this.history.length - 1]) : [];
      this.sigpad.clear();
      if (prev && prev.length) this.sigpad.fromData(prev);
    },

    clearPad() {
      this.pushHistory();
      this.sigpad.clear();
      this.preview = null;
    },

    async getEnhancedDataURL() {
      if (!this.sigpad || this.sigpad.isEmpty()) throw new Error("Signature is empty");

      const src = this.sigpad._canvas || this.sigpad.canvas || this.canvas;
      await new Promise((r) => setTimeout(r, 0));

      const ow = src.width;
      const oh = src.height;

      const srcCtx = src.getContext("2d");
      const img = srcCtx.getImageData(0, 0, ow, oh);
      const d = img.data;

      let minX = ow,
        minY = oh,
        maxX = 0,
        maxY = 0,
        found = false;

      for (let y = 0; y < oh; y++) {
        for (let x = 0; x < ow; x++) {
          const idx = (y * ow + x) * 4;
          const r = d[idx],
            g = d[idx + 1],
            b = d[idx + 2],
            a = d[idx + 3];
          const lum = 0.299 * r + 0.587 * g + 0.114 * b;

          if (a > 10 && lum < this.BRIGHT_THRESHOLD) {
            found = true;
            if (x < minX) minX = x;
            if (y < minY) minY = y;
            if (x > maxX) maxX = x;
            if (y > maxY) maxY = y;
          }
        }
      }

      if (!found) throw new Error("No ink detected");

      minX = Math.max(0, minX - this.MARGIN);
      minY = Math.max(0, minY - this.MARGIN);
      maxX = Math.min(ow, maxX + this.MARGIN);
      maxY = Math.min(oh, maxY + this.MARGIN);

      const cw = maxX - minX;
      const ch = maxY - minY;

      const out = document.createElement("canvas");
      out.width = Math.max(1, Math.round(cw * this.SCALE));
      out.height = Math.max(1, Math.round(ch * this.SCALE));
      const octx = out.getContext("2d", { alpha: true });

      octx.imageSmoothingEnabled = true;
      octx.imageSmoothingQuality = "high";

      octx.drawImage(src, minX, minY, cw, ch, 0, 0, out.width, out.height);

      let img2 = octx.getImageData(0, 0, out.width, out.height);
      octx.putImageData(img2, 0, 0);

      const final = document.createElement("canvas");
      final.width = out.width;
      final.height = out.height;
      const fctx = final.getContext("2d", { alpha: true });
      fctx.drawImage(out, 0, 0);

      return final.toDataURL("image/png");
    },
    async compressDataUrl(dataUrl, maxWidth = 1000, maxSizeMB = 2) {
      const img = new Image();
      img.src = dataUrl;

      await new Promise(resolve => img.onload = resolve);

      let canvas = document.createElement("canvas");
      let ctx = canvas.getContext("2d", { alpha: true });

      // Step 1 — scale down if too big
      let scale = 1;
      if (img.width > maxWidth) {
        scale = maxWidth / img.width;
      }

      canvas.width = Math.round(img.width * scale);
      canvas.height = Math.round(img.height * scale);

      ctx.imageSmoothingEnabled = true;
      ctx.imageSmoothingQuality = "high";
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

      // Step 2 — reduce color depth (PNG lossless optimization)
      let imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      let d = imgData.data;
      for (let i = 0; i < d.length; i += 4) {
        // reduce grayscale to 32 levels = 5-bit
        d[i] = Math.round(d[i] / 8) * 8;
        d[i+1] = Math.round(d[i+1] / 8) * 8;
        d[i+2] = Math.round(d[i+2] / 8) * 8;
        // alpha untouched for transparency
      }
      ctx.putImageData(imgData, 0, 0);

      // Step 3 — compress loop until < 2MB
      let out = canvas.toDataURL("image/png");
      const maxBytes = maxSizeMB * 1024 * 1024;

      while ((out.length * 3) / 4 > maxBytes) {
        // progressively downscale 10%
        canvas = scaleDown(canvas, 0.9);
        out = canvas.toDataURL("image/png");
      }

      return out;
    },
    async downloadEnhanced() {
      this.pushHistory();
      try {
        const dataUrl = await this.getEnhancedDataURL();
        const compressedData = await this.compressDataUrl(dataUrl);
        const blob = await (await fetch(compressedData)).blob();
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
    },
  },

  // expose public functions
};
</script>
