import { defineConfig, loadEnv, splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import { VitePWA } from 'vite-plugin-pwa';
// import ViteWorkboxPlugin from 'vite-plugin-workbox';
// import strip from '@rollup/plugin-strip'
// import cleanup from 'rollup-plugin-cleanup'

// https://vitejs.dev/config/
export default ({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  // console.log(env)
  // const baseUrl = mode == 'production' 
  //                   ? env.VITE_BASE_URL_PROD
  //                   : env.VITE_BASE_URL

  const folder = 'assets/vue'
  const outDir = `./public/${folder}/`

  const baseUrl = mode == 'production' 
                    ? env.VITE_BASE_URL_PROD
                    : env.VITE_BASE_URL

  const base = mode === 'production' 
                ? `${baseUrl}${folder}/`
                : './'

  // console.log(resolve(__dirname, outDir))
  console.log(baseUrl, __dirname, base)

  return defineConfig({
    optimizeDeps: {
      include: [
        'vue',
        'vue-router',
        '@iconify/vue',
      ]
    },
    plugins: [
      vue(),// ...
      AutoImport({
        resolvers: [ElementPlusResolver()],
        imports: [
          'vue',
          'vue-router',
          'pinia',              // auto-import Pinia APIs like `defineStore`, `useStore`
        ],
        dirs: ['frontend/config/store'],  // auto-import your stores from this folder
      }),
      Components({
        resolvers: [ElementPlusResolver()],
      }),
      splitVendorChunkPlugin(),
      VitePWA({
        // Enable manifest generation
        includeAssets: ['index.html', 'index.php','favicon.svg', 'robots.txt'],
        registerType: 'autoUpdate',
        devOptions: {
          enabled: true
        },
        manifest: {
          "name": "Sistem Catatan Ibadah Muhammadiyah Kendal",
          "short_name": "Diari-Mu",
          "start_url": baseUrl + "index.php",
          "scope": baseUrl,
          "display": "standalone",
          "background_color": "#ffffff",
          "theme_color": "#11716d",
          "icons": [
            {
              "src": baseUrl + "/assets/images/icons/android-chrome-192x192.png",
              "sizes": "192x192",
              "type": "image/png"
            },
            {
              "src": baseUrl + "/assets/images/icons/android-chrome-512x512.png",
              "sizes": "512x512",
              "type": "image/png"
            },
          ],
        },
        workbox: {
          // This configuration will inject Workbox into the generated service worker
          clientsClaim: true,
          navigateFallback: baseUrl + "index.php",
          skipWaiting: true,
          // swDest: 'public/sw.js',                   // Your custom SW template
          globPatterns: ['**/*.{js,css,html,png,svg,webmanifest}'],
          globIgnores: ['sw.js', 'workbox-*.js'],  // Precache from Vite output only
          globDirectory: "public/assets/vue",
          runtimeCaching: [
            {
              urlPattern: /^https:\/\/api\.example\.com\//, // Cache API calls
              handler: 'NetworkFirst',
              options: {
                cacheName: 'api-cache',
                expiration: {
                  maxEntries: 10, // Limit the cache to 10 entries
                },
              },
            },
            {
              urlPattern: /\.(?:png|jpg|jpeg|svg)$/, // Cache images
              handler: 'CacheFirst',
              options: {
                cacheName: 'image-cache',
                expiration: {
                  maxEntries: 20,
                },
              },
            },
          ],
        },
      }),
   
    ],
    resolve: {
      alias: {
        '@': resolve(__dirname, './frontend')
      }
    },
    base: base,
    server: {
      host:'127.0.0.1',
      // required to load scripts from custom host
      cors: true,
  
      // we need a strict port to match on PHP side
      // change freely, but update on PHP to match the same port
      strictPort: true,
      port: env.VITE_PORT,
      origin: "http://127.0.0.1:" + env.VITE_PORT,
    },
    build: {
      // output dir for production build
      outDir: resolve(__dirname, outDir),
      assetsDir:'files',
      emptyOutDir: true,
      copyPublicDir: false,
      cssCodeSplit: true,
      // emit manifest so PHP can find the hashed files
      manifest: true,
  
      // esbuild target
      target: 'es2020',
      
      // our entry
      rollupOptions: {
        input: {
          main: resolve(__dirname, './frontend/main.js'),
        },
        plugins: [
          // remove console.*, assets.* (default)
          // strip({
          //   include: ['**/*.js', '**/*.vue'],
          //   sourceMap: false
          // }),
          // cleanup({
          //   comments: 'none',
          //   sourcemap: false,
          //   extensions: ['js', 'vue']
          // })
        ]
      }
    }
  })
}
