import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    type: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.type === 'admin',
    isUser: (state) => state.type === 'user'
  },

  actions: {
    async adminLogin(credentials) {
      try {
        const response = await axios.post('/admin/login', credentials)
        this.setAuth(response.data)
        this.setAxiosToken()
      } catch (error) {
        throw error
      }
    },

    async userLogin(credentials) {
      try {
        const response = await axios.post('/user/login', credentials)
        this.setAuth(response.data)
        this.setAxiosToken()
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        await axios.post('/logout')
      } finally {
        this.clearAuth()
      }
    },

    setAuth(data) {
      this.user = data.user
      this.token = data.token
      this.type = data.type
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      localStorage.setItem('type', data.type)
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.type = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('type')
      delete axios.defaults.headers.common['Authorization']
    },

    setAxiosToken() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    },

    initAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      const type = localStorage.getItem('type')

      if (token && user && type) {
        this.token = token
        this.user = JSON.parse(user)
        this.type = type
        this.setAxiosToken()
      }
    }
  }
})