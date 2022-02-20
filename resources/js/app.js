require('./bootstrap');

import { createApp, h } from 'vue'
import { app, plugin } from '@inertiajs/inertia-vue3'
import { InertiaProgress as progress } from '@inertiajs/progress'

const el = document.getElementById('app')

progress.init()

createApp({
    render: () => h(app, {
        initialPage: JSON.parse(el.dataset.page),
        resolveComponent: name => import(`./pages/${name}.vue`).then(m => m.default),
    })
})
    .mixin({ methods: {
            route: window.route,
            title: title => `Laravel Feed`,
        }})
    .use(plugin)
    .mount(el)
