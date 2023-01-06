require('./bootstrap')

import { createApp } from 'vue'
import Student from './components/Student/Index.vue'
import { createVuestic } from 'vuestic-ui'
import 'vuestic-ui/css'

// const app = createApp({})

// app.component('student-index', Student)

// app.mount('#app')

createApp(Student)
    .use(createVuestic())
    .mount('#app')
