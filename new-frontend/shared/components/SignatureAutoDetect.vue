<template>
  <div class="p-4 space-y-4">
    <input type="file" accept="image/*" @change="onFileChange" />

    <div v-if="imageUrl">
      <img ref="imageRef" :src="imageUrl" class="max-w-full" />
    </div>

    <button 
      v-if="imageUrl" 
      @click="processSignatureEnhanced"
      class="px-4 py-2 bg-blue-600 text-white rounded"
    >
      Process Enhanced Signature
    </button>

    <div v-if="output">
      <h3 class="font-bold mt-4">Enhanced Result:</h3>
      <img :src="output" class="border p-2 bg-white" />
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import Cropper from 'cropperjs'

const imageUrl = ref(null)
const imageRef = ref(null)
const output = ref(null)
let cropper = null

const onFileChange = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  imageUrl.value = URL.createObjectURL(file)

  await nextTick()

  if (cropper) cropper.destroy()

  cropper = new Cropper(imageRef.value, {
    aspectRatio: NaN,
    viewMode: 1,
    background: false,
    autoCropArea: 1
  })
}

// Otsu threshold helper
function otsuThreshold(grayscaleData) {
  const histogram = Array(256).fill(0)

  grayscaleData.forEach(v => histogram[v]++)

  let total = grayscaleData.length
  let sum = 0
  for (let i = 0; i < 256; i++) sum += i * histogram[i]

  let sumB = 0
  let wB = 0
  let maxVariance = 0
  let threshold = 0

  for (let i = 0; i < 256; i++) {
    wB += histogram[i]
    if (wB === 0) continue

    const wF = total - wB
    if (wF === 0) break

    sumB += i * histogram[i]

    const mB = sumB / wB
    const mF = (sum - sumB) / wF

    const variance = wB * wF * (mB - mF) ** 2

    if (variance > maxVariance) {
      maxVariance = variance
      threshold = i
    }
  }

  return threshold
}

const processSignatureEnhanced = () => {
  const canvas = cropper.getCroppedCanvas({
    maxWidth: 1200,
    maxHeight: 1200,
    fillColor: "white"
  })

  const ctx = canvas.getContext("2d")
  const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height)
  const data = imgData.data

  // Step 1 — Convert to grayscale array first
  const grayscale = []
  for (let i = 0; i < data.length; i += 4) {
    grayscale.push((data[i] + data[i + 1] + data[i + 2]) / 3)
  }

  // Step 2 — Otsu threshold for clean BW
  const threshold = otsuThreshold(grayscale)

  for (let i = 0; i < data.length; i += 4) {
    const gray = (data[i] + data[i + 1] + data[i + 2]) / 3

    // Remove background (white & light colors)
    if (gray > threshold + 15) {
      data[i + 3] = 0 // transparent
      continue
    }

    // Set signature to black
    data[i] = 0
    data[i + 1] = 0
    data[i + 2] = 0
  }

  // Step 3 — Dilation (tebalkan garis tanda tangan)
  const w = canvas.width
  const h = canvas.height
  const getIndex = (x, y) => (y * w + x) * 4

  const dilated = new Uint8ClampedArray(data)
  const radius = 1

  for (let y = 1; y < h - 1; y++) {
    for (let x = 1; x < w - 1; x++) {
      let hasInk = false

      for (let ky = -radius; ky <= radius; ky++) {
        for (let kx = -radius; kx <= radius; kx++) {
          const idx = getIndex(x + kx, y + ky)
          if (data[idx + 3] > 0) hasInk = true
        }
      }

      if (hasInk) {
        const idx = getIndex(x, y)
        dilated[idx] = 0
        dilated[idx + 1] = 0
        dilated[idx + 2] = 0
        dilated[idx + 3] = 255
      }
    }
  }

  imgData.data.set(dilated)
  ctx.putImageData(imgData, 0, 0)

  output.value = canvas.toDataURL("image/png")
}
</script>
