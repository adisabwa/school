<template>
  <div>
    <qrcode-stream
      @detect="onDetect"
      @error="onError"
    />
  </div>
</template>

<script>
import { QrcodeStream } from 'vue-qrcode-reader'

export default {
  name: 'QrScanner',
  emits:['detected', 'error'],
  components: {
    QrcodeStream
  },
  methods: {
    onDetect(result) {
      // result berupa array
      const code = result[0].rawValue
      this.$emit('detected',code)
      console.log('QR:', code)
    },
    onError(err) {
      console.error('Camera error:', err)
      this.$emit('error',err)
    }
  }
}
</script>
