import * as Vue from 'vue'
const { createApp } = Vue

import './style.css'
import AppMain from './AppMain.vue'
import router from './router'

createApp(AppMain)
  .use(router)
  .mount('#app')