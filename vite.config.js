import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
       host: '0.0.0.0',
       port: 5173,
       strictPort: true,
       hmr: {
              host: '172.16.115.193', //ganti pake IP Address laptopku
         }, 
    },
});
