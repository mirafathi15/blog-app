<template>
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
          <div class="card shadow-sm">
            <div class="card-body p-5">
              <div class="text-center mb-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width: 64px; height: 64px;">
                  <i class="bi bi-shield-fill-check text-primary" style="font-size: 1.8rem;"></i>
                </div>
                <h2 class="fw-bold">Admin Portal</h2>
                <p class="text-muted">Sign in to access the admin dashboard</p>
              </div>
              
              <div v-if="error" class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>{{ error }}</div>
              </div>
  
              <form @submit.prevent="login">
                <div class="mb-3">
                  <label for="email" class="form-label">Admin Email</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-person-badge"></i>
                    </span>
                    <input
                      id="email"
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      placeholder="admin@example.com"
                      required
                    />
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-key"></i>
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
                    <input class="form-check-input" type="checkbox" id="remember-admin">
                    <label class="form-check-label" for="remember-admin">
                      Remember me
                    </label>
                  </div>
                  <a href="#" class="text-decoration-none text-primary">Forgot password?</a>
                </div>
                
                <button
                  type="submit"
                  class="btn btn-primary w-100 py-2"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  <i v-if="!loading" class="bi bi-shield-lock me-2"></i>
                  {{ loading ? 'Signing in...' : 'Sign in as Admin' }}
                </button>
                
                <div class="text-center mt-4">
                  <a href="http://localhost:8000/users/login" class="text-success text-decoration-none">
                    <i class="bi bi-person me-1"></i>
                    Login as User instead
                  </a>
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
    name: 'AdminLogin',
    setup() {
      const router = useRouter()
      const authStore = useAuthStore()
      const loading = ref(false)
      const error = ref('')
      const form = ref({
        email: 'admin@example.com',
        password: 'password'
      })
  
      const login = async () => {
        loading.value = true
        error.value = ''
        
        try {
          await authStore.adminLogin(form.value)
          router.push('/admins')
        } catch (err) {
          error.value = err.response?.data?.message || 'Login failed'
          console.error('Login failed:', err)
        } finally {
          loading.value = false
        }
      }
  
      return {
        form,
        loading,
        error,
        login
      }
    }
  }
  </script>