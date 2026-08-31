<template>
  <div class="p-4 space-y-4 select-none">
    <div class="border rounded-lg bg-white shadow relative">
      <canvas
        ref="canvasRef"
        class="w-full h-64 touch-none"
        @pointerdown="startDrawing"
        @pointermove="draw"
        @pointerup="endDrawing"
        @pointercancel="endDrawing"
        @pointerleave="endDrawing"
      ></canvas>
    </div>

    <div class="flex gap-2">
      <button @click="clearCanvas" class="px-4 py-2 bg-red-600 text-white rounded">Clear</button>
      <button @click="exportEnhanced" class="px-4 py-2 bg-blue-600 text-white rounded">Export Enhanced PNG</button>
    </div>

    <div v-if="output" class="mt-4">
      <h3 class="font-bold">Enhanced Result:</h3>
      <img :src="output" class="border p-2 bg-white max-w-full" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import getStroke from 'perfect-freehand'

const canvasRef = ref(null)
let ctx = null
let points = []
let isDrawing = false
const output = ref(null)

// ------------------------
// Perfect-freehand → SVG path
// ------------------------
function getSvgPathFromStroke(stroke) {
  if (!stroke.length) return ''
  return (
    'M' +
    stroke.map(([x, y], i) => `${i === 0 ? '' : 'L'}${x} ${y}`).join(' ')
  )
}

function drawStroke() {
  const canvas = canvasRef.value
  ctx.clearRect(0, 0, canvas.width, canvas.height)

  const stroke = getStroke(points, {
    size: 4,
    thinning: 0.7,
    smoothing: 0.75,
    streamline: 0.55
  })

  const path = getSvgPathFromStroke(stroke)
  const p = new Path2D(path)

  ctx.fillStyle = 'black'
  ctx.fill(p)
}

// ------------------------
// Drawing handlers
// ------------------------
function startDrawing(e) {
  isDrawing = true
  points = []
  addPoint(e)
}

function draw(e) {
  if (!isDrawing) return
  addPoint(e)
  drawStroke()
}

function endDrawing() {
  isDrawing = false
}

function addPoint(e) {
  const rect = canvasRef.value.getBoundingClientRect()
  points.push([
    e.clientX - rect.left,
    e.clientY - rect.top
  ])
}

// ------------------------
// Auto-crop + transparent background + enhance
// ------------------------
function exportEnhanced() {
  const canvas = canvasRef.value
  const w = canvas.width
  const h = canvas.height

  const img = ctx.getImageData(0, 0, w, h)
  const d = img.data

  // --- 1. Detect real ink ---
  let minX = w, minY = h, maxX = 0, maxY = 0
  let found = false

  for (let y = 0; y < h; y++) {
    for (let x = 0; x < w; x++) {

      const i = (y * w + x) * 4
      const r = d[i], g = d[i+1], b = d[i+2], a = d[i+3]

      // Perfect-freehand output = pure black ink (0-20)
      const isInk = (r < 35 && g < 35 && b < 35 && a > 20)

      if (isInk) {
        found = true
        if (x < minX) minX = x
        if (y < minY) minY = y
        if (x > maxX) maxX = x
        if (y > maxY) maxY = y
      }
    }
  }

  if (!found) {
    alert("Tidak ada tinta yang terdeteksi.")
    return
  }

  // SAFE margin
  const margin = 15
  minX = Math.max(0, minX - margin)
  minY = Math.max(0, minY - margin)
  maxX = Math.min(w, maxX + margin)
  maxY = Math.min(h, maxY + margin)

  const cropW = maxX - minX
  const cropH = maxY - minY

  // --- 2. Cropped canvas ---
  const out = document.createElement("canvas")
  out.width = cropW
  out.height = cropH

  const octx = out.getContext("2d")
  octx.putImageData(
    ctx.getImageData(minX, minY, cropW, cropH),
    0, 0
  )

  // --- 3. Enhance: keep only pure ink, remove everything else ---
  const img2 = octx.getImageData(0, 0, cropW, cropH)
  const d2 = img2.data

  octx.putImageData(img2, 0, 0)

  output.value = out.toDataURL("image/png")
}

function clearCanvas() {
  const canvas = canvasRef.value
  ctx.clearRect(0, 0, canvas.width, canvas.height)
  output.value = null
}

// ------------------------
// Init
// ------------------------
onMounted(() => {
  const canvas = canvasRef.value
  canvas.width = canvas.offsetWidth
  canvas.height = canvas.offsetHeight
  ctx = canvas.getContext('2d')
  ctx.lineCap = 'round'
})
</script>

<style scoped>
canvas {
  touch-action: none;
}
</style>
