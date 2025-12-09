// app/plugins/api.ts
import { defineNuxtPlugin } from 'nuxt/app'
import axios from 'axios'
import type { AxiosInstance } from 'axios'

export default defineNuxtPlugin(() => {
  const api: AxiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
  })

  return {
    provide: {
      api,
    },
  }
})
