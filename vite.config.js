import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default ({ mode }) => {
    // add Laravel .env variables (only the ones that start with VITE_) to process.env object
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    return defineConfig({
        css: {
            preprocessorOptions: {
                scss: {
                    additionalData: `$project-path: '${process.env.VITE_PROJECT_PATH}';`,
                },
            },
        },
        plugins: [
            viteStaticCopy({
                targets: [
                    {
                        src: 'node_modules/tinymce/',
                        dest: ''
                    },
                    {
                        src: 'robots.txt',
                        dest: '../'
                    },
                    {
                        src: ['resources/js/backend/indexes.js', 'resources/js/backend/model-delete.js'] ,
                        dest: 'js/backend'
                    },
                    {
                        src: 'resources/assets/backend/icons',
                        dest: 'assets/backend'
                    },
                    {
                        src: 'resources/assets/frontend/' + process.env.VITE_PROJECT_PATH,
                        dest: 'assets/frontend',
                        rename: process.env.VITE_PROJECT_PATH,
                    },
                    // TODO: is this needed? why?
                    {
                        src: path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free/webfonts'),
                        dest: 'assets/frontend/' + process.env.VITE_PROJECT_PATH,
                        rename: 'webfonts',
                    }
                ]
            }),
            laravel({
                input: [
                    'resources/js/frontend/' + process.env.VITE_PROJECT_PATH + '/app.js',
                    'resources/sass/frontend/' + process.env.VITE_PROJECT_PATH + '/app.scss',
                    'resources/js/backend/app.js',
                    'resources/sass/backend/app.scss',
                ],
                refresh: false,
            }),
        ],
        resolve: {
            alias: {
                '~jquery': path.resolve(__dirname, 'node_modules/jquery/dist/jquery.js'),
                '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
                '~sweetalert2': path.resolve(__dirname, 'node_modules/sweetalert2'),
            }
        },
    });
}
