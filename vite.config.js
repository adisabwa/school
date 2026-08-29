import { defineConfig, loadEnv, splitVendorChunkPlugin } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import { VitePWA } from 'vite-plugin-pwa'
import path from 'path'
import { pathToFileURL } from 'url'

// https://vitejs.dev
export default async ({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const school = env.VITE_SCHOOL

  // 2. Resolve the absolute path to your file
  const absolutePath = path.resolve(process.cwd(), `frontend/config/schools/${school}.js`)

  // 3. Import dynamically using a safe file URL conversion
  const { baseUrlProd, appName, appNameShort } = await import(pathToFileURL(absolutePath).href)
  console.log('School Base URL:', baseUrlProd)

  const folder = 'assets/vue'
  const outDir = `./public/${folder}/`

  const baseUrl = mode == 'production' 
                    ? baseUrlProd
                    : env.VITE_BASE_URL

  const base = mode === 'production' 
                ? `${baseUrl}${folder}/`
                : `http://127.0.0.1:${env.VITE_PORT}`

  console.log('Final Base Path:', base)

  return {
    base: base,
    optimizeDeps: {
      include: [
        'vue',
        'vue-router',
        '@iconify/vue',
        '@vueup/vue-quill', 'quill'
      ],
      esbuildOptions: {
        target: 'es2022',
        supported: { 
          'top-level-await': true 
        }
      }
    },
    plugins: [
      vue(),
      splitVendorChunkPlugin(), // Ditambahkan karena di-import di atas
      AutoImport({
        resolvers: [ElementPlusResolver()],
        imports: [
          'vue',
          'vue-router',
          'pinia',
        ],
        dirs: [
          resolve(__dirname,'frontend/helpers/**'),
          resolve(__dirname,'frontend/composables/**'),
          resolve(__dirname,'frontend/config/stores/**'),
        ],
        dts: './auto-imports.d.ts',
      }),
      Components({
        resolvers: [ElementPlusResolver()],
      }),
      VitePWA({
        // Diubah menjadi relative ke outDir agar VitePWA tidak kebingungan memetakan foldernya
        manifestFilename: 'manifest.webmanifest', 
        srcDir: '.',           
        filename: 'my-sw.js',     
        strategies: 'injectManifest',
        includeAssets: ['favicon.svg', 'robots.txt'],
        registerType: 'autoUpdate',
        devOptions: {
          enabled: true,
          type: 'module', 
        },
        useCredentials: true,
        build: true,
        manifest: {
          name: appName,
          short_name: appNameShort,
          start_url: baseUrl + "index.php",
          scope: baseUrl,
          display: "standalone",
          background_color: "#ffffff",
          theme_color: "#11716d",
          icons: [
            {
              src: baseUrl + "assets/images/icons/android-chrome-192x192.png",
              sizes: "192x192",
              type: "image/png"
            },
            {
              src: baseUrl + "assets/images/icons/android-chrome-512x512.png",
              sizes: "512x512",
              type: "image/png"
            },
          ],
        },
        injectManifest: {
          swSrc: resolve(__dirname, 'public/sw.js'),
          swDest: resolve(__dirname, 'public/sw.js'),
          injectionPoint: 'self.__WB_MANIFEST',
          globIgnores: ['sw.js', 'workbox-*.js', '**/manifest.webmanifest'],
          globDirectory: "public/assets/vue",
          globPatterns: [
            '**/*.{js,css,html,png,svg}'
          ],
          modifyURLPrefix: {
            '': baseUrl + 'assets/vue/' ,
          },
          dontCacheBustURLsMatching: /manifest\.webmanifest$/,
          rollupOptions: {
            output: {
              format: 'es', 
              inlineDynamicImports: true,
            },
          },
        },
      }),
    ],
    resolve: {
      alias: {
        '@': resolve(__dirname, './frontend'),
        '@modules': resolve(__dirname, './frontend/modules'),
        '@rapor': resolve(__dirname, './frontend/modules/rapor'),
      }
    },
    esbuild: {
      supported: {
        'top-level-await': true 
      }
    },
    server: {
      host: '127.0.0.1',
      cors: true,
      strictPort: true,
      port: env.VITE_PORT,
      origin: `http://127.0.0.1:${env.VITE_PORT}`,
      headers: {
        'Service-Worker-Allowed': '/' 
      },
      hmr: {
        host: '127.0.0.1',
      },
    },
    build: {
      outDir: resolve(__dirname, outDir),
      assetsDir: 'files',
      emptyOutDir: true,
      copyPublicDir: false,
      cssCodeSplit: true,
      manifest: true,
      target: 'es2022',
      rollupOptions: {
        input: {
          main: resolve(__dirname, './frontend/main.js'),
        },
        plugins: []
      } // <--- FIX: Menutup properti rollupOptions dengan benar
    } // <--- FIX: Menutup properti build dengan benar
  } // <--- FIX: Menutup return object dengan benar
} // <--- FIX: Menutup fungsi export default async dengan benar
