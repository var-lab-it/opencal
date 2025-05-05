import { createApp } from 'vue'
import './style/app.scss'
import router from './router/index';
import App from './App.vue'
import 'bootstrap'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { fab } from '@fortawesome/free-brands-svg-icons'
import { createI18n } from 'vue-i18n'
import en from './locales/en.json'

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        en
    }
})
library.add(fas, fab)

const app = createApp(App)

app.component('FontAwesomeIcon', FontAwesomeIcon)
app.use(router)
app.use(i18n)
app.mount('#opencal')
