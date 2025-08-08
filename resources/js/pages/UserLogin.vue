<template>
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow-sm">
            <div class="card-body p-5">
              <div class="text-center mb-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width: 64px; height: 64px;">
                  <i class="bi bi-person-fill text-success" style="font-size: 1.8rem;"></i>
                </div>
                <h2 class="fw-bold">Welcome Back</h2>
                <p class="text-muted">Please sign in to your account</p>
              </div>
              
              <div v-if="error" class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>{{ error }}</div>
              </div>
  
              <form @submit.prevent="handleLogin">
                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input
                      id="email"
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      placeholder="you@example.com"
                      required
                    />
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input
                      id="password"
                      v-model="form.password"
                      type="password"
                      class="form-control"
                      placeholder="••••••••"
                      required
                    />
                  </div>
                </div>
                
                <div class="d-flex justify-content-between mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me">
                    <label class="form-check-label" for="remember-me">
                      Remember me
                    </label>
                  </div>
                  <a href="#" class="text-decoration-none text-success">Forgot password?</a>
                </div>
                
                <button
                  type="submit"
                  class="btn btn-success w-100 py-2"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {{ loading ? 'Signing in...' : 'Sign in' }}
                </button>
                
                <div class="text-center mt-4">
                  <router-link to="/admins/login" class="text-success text-decoration-none">
                    <i class="bi bi-shield me-1"></i>
                    Login as Admin instead
                  </router-link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '../stores/auth'
  
  export default {
    name: 'UserLogin',
    setup() {
      const router = useRouter()
      const authStore = useAuthStore()
      const loading = ref(false)
      const error = ref('')
      const form = ref({
        email: 'user@example.com',
        password: 'password'
      })
  
      const handleLogin = async () => {
        loading.value = true
        error.value = ''
        
        try {
          await authStore.userLogin(form.value)
          router.push('/users')
        } catch (err) {
          error.value = err.response?.data?.message || 'Login failed'
        } finally {
          loading.value = false
        }
      }
  
      return {
        form,
        loading,
        error,
        handleLogin
      }
    }
  }
  </script>