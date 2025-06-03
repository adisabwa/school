// vite-plugin-workbox.js
import { generateSW } from 'workbox-build';
import { resolve } from 'path'

const folder = 'assets/vue'
const outDir = `./public/${folder}/files`
const dir = resolve(__dirname, outDir)
console.log(dir)

export default function workboxPlugin() {
  return {
    name: 'vite-plugin-workbox',
    apply: 'build',
    async generateBundle() {
      try {
        const serviceWorker = await generateSW({
          globDirectory: dir,
          swDest: 'public/sw.js',                   // Your custom SW template
          globPatterns: ['**/*'],             // Precache from Vite output only
          clientsClaim: true,    // Claim clients immediately
          skipWaiting: true,     // Skip waiting and activate new SW immediately 
          runtimeCaching: [
            {
            urlPattern: ({ request }) => request.destination === 'image', // Cache images with CacheFirst
            handler: 'CacheFirst',  // Use CacheFirst strategy for images
            options: {
                cacheName: 'image-cache',
                expiration: {
                maxEntries: 50, // Limit to 50 images in the cache
                },
            },
            },
            {
            urlPattern: ({ request }) => request.destination === 'script', // Cache JavaScript with CacheFirst
            handler: 'CacheFirst', // Cache JavaScript files
            options: {
                cacheName: 'script-cache',
                expiration: {
                maxEntries: 20, // Limit to 20 scripts in the cache
                },
            },
            },
            {
            urlPattern: /api\/.*\//, // NetworkFirst strategy for API calls
            handler: 'NetworkFirst',
            options: {
                cacheName: 'api-cache',
                expiration: {
                maxEntries: 30, // Limit to 30 API responses in the cache
                },
            },
            },
        ],
        });
        console.log('✔ Custom service worker injected successfully.');
        // Read the generated file and emit it into the Vite output
        //     globDirectory: dir,
        //     globPatterns: [
        //     '**/*',
        //     ],
        //     swDest: 'public/sw.js', // Destination for your service worker
        //     clientsClaim: true,    // Claim clients immediately
        //     skipWaiting: true,     // Skip waiting and activate new SW immediately 
            
        // });
        
        // Create the service worker in the dist folder
        this.emitFile({
            type: 'asset',
            fileName: 'sw.js',
            source: serviceWorker,
        });
      } catch (error) {
      console.error("Error during Workbox SW generation:", error);
      }
    },
  };
}
