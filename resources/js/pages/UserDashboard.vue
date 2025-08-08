<template>
    <div class="bg-light min-vh-100">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <i class="bi bi-speedometer2 text-success me-2"></i>
                    <strong>User Dashboard</strong>
                </div>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            Welcome, {{ authStore.user?.name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button @click="handleLogout" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold text-dark">My Posts</h2>
                            <p class="text-muted mb-0">Manage and create your blog posts</p>
                        </div>
                        <button @click="showCreateModal = true" class="btn btn-success d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i>
                            Create New Post
                        </button>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input v-model="searchQuery" type="text" class="form-control"
                                            placeholder="Search your posts by title, content, or author..." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Posts List -->
                    <div class="card">
                        <div class="card-body">
                            <div v-if="filteredPosts.length" class="row">
                                <div v-for="post in filteredPosts" :key="post.id" class="col-12 mb-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5 class="card-title fw-bold">{{ post.title }}</h5>
                                                    <div class="mb-2">
                                                        <span class="badge me-2"
                                                            :class="post.status === 'published' ? 'bg-success' : 'bg-warning'">
                                                            <i class="bi"
                                                                :class="post.status === 'published' ? 'bi-check-circle' : 'bi-clock'"></i>
                                                            {{ post.status }}
                                                        </span>
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{ formatDate(post.created_at) }}
                                                        </small>
                                                    </div>
                                                    <p class="card-text text-muted">{{ post.content.substring(0, 150)
                                                        }}...</p>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="d-flex flex-column h-100">
                                                        <div v-if="post.image" class="mb-3">
                                                            <img :src="post.image" alt="Post image"
                                                                class="img-fluid rounded"
                                                                style="max-height: 100px; width: 100%; object-fit: cover;">
                                                        </div>

                                                        <div class="mt-auto">
                                                            <div class="btn-group w-100" role="group">
                                                                <button @click="viewPost(post)"
                                                                    class="btn btn-outline-info btn-sm">
                                                                    <i class="bi bi-eye"></i> View
                                                                </button>
                                                                <button @click="editPost(post)"
                                                                    class="btn btn-outline-primary btn-sm">
                                                                    <i class="bi bi-pencil"></i> Edit
                                                                </button>
                                                                <button @click="deletePost(post.id)"
                                                                    class="btn btn-outline-danger btn-sm">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-journal-text text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted">No posts yet</h5>
                                <p class="text-muted mb-4">Get started by creating your first post.</p>
                                <button @click="showCreateModal = true" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>Create Your First Post
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi" :class="editingPost ? 'bi-pencil' : 'bi-plus-circle'"></i>
                            {{ editingPost ? 'Edit Post' : 'Create New Post' }}
                        </h5>
                        <button type="button" class="btn-close" @click="closeModal" :disabled="loading"></button>
                    </div>

                    <form @submit.prevent="savePost">
                        <div class="modal-body">
                            <div v-if="error" class="alert alert-danger d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ error }}
                            </div>

                            <div v-if="loading" class="alert alert-info d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                {{ editingPost ? 'Updating post...' : 'Creating post...' }}
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Post Title</label>
                                    <input v-model="postForm.title" type="text" class="form-control"
                                        placeholder="Enter an engaging title for your post" required />
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Content</label>
                                    <textarea v-model="postForm.content" class="form-control" rows="5"
                                        placeholder="Write your post content here..." required></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select v-model="postForm.status" class="form-select">
                                        <option value="draft">
                                            <i class="bi bi-clock"></i> Draft
                                        </option>
                                        <option value="published">
                                            <i class="bi bi-check-circle"></i> Published
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Featured Image</label>
                                    <input ref="fileInput" type="file" accept="image/*" @change="handleFileUpload"
                                        class="form-control" />
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i>
                                        JPG, PNG, GIF - Max 2MB
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="loading">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="loading">
                                <i class="bi" :class="editingPost ? 'bi-check-circle' : 'bi-plus-circle'"></i>
                                {{ editingPost ? 'Update Post' : 'Create Post' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- View Post Modal -->
        <div v-if="showViewModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-eye me-2"></i>View Post
                        </h5>
                        <button type="button" class="btn-close" @click="showViewModal = false"></button>
                    </div>
                    <div class="modal-body" v-if="viewingPost">
                        <div v-if="viewingPost.image" class="mb-3">
                            <img :src="viewingPost.image" alt="Post image" class="img-fluid rounded">
                        </div>
                        <h3 class="mb-3">{{ viewingPost.title }}</h3>
                        <div class="mb-3">
                            <span class="badge me-2"
                                :class="viewingPost.status === 'published' ? 'bg-success' : 'bg-warning'">
                                {{ viewingPost.status }}
                            </span>
                            <span class="text-muted">
                                By {{ viewingPost.author.name }} ({{ viewingPost.author.type }})
                            </span>
                        </div>
                        <div class="content">
                            {{ viewingPost.content }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from 'axios'

export default {
    name: 'UserDashboard',
    setup() {
        const router = useRouter()
        const authStore = useAuthStore()
        const allPosts = ref([])
        const posts = ref([])
        const showViewModal = ref(false)
        const showCreateModal = ref(false)
        const viewingPost = ref(null)
        const editingPost = ref(null)
        const loading = ref(false)
        const error = ref('')
        const searchQuery = ref('')
        const fileInput = ref(null)

        const postForm = ref({
            title: '',
            content: '',
            status: 'draft',
            image: null
        })

        const filteredPosts = computed(() => {
            if (!searchQuery.value.trim()) {
                return allPosts.value
            }

            const query = searchQuery.value.toLowerCase().trim()
            return allPosts.value.filter(post => {
                return (
                    (post.title && post.title.toLowerCase().includes(query)) ||
                    (post.content && post.content.toLowerCase().includes(query)) ||
                    (post.author?.name && post.author.name.toLowerCase().includes(query))
                )
            })
        })

        const fetchPosts = async () => {
            loading.value = true
            try {
                const response = await axios.get('/posts')
                allPosts.value = response.data.data || response.data
                posts.value = allPosts.value

            } catch (error) {
                console.error('Error fetching posts:', error)
                error.value = 'Failed to load posts'
                allPosts.value = []
                posts.value = []

            } finally {
                loading.value = false
            }
        }

        const searchPosts = () => {
        }

        const clearSearch = () => {
            searchQuery.value = ''
        }

        const savePost = async () => {
            loading.value = true
            error.value = ''

            try {
                const formData = new FormData()
                formData.append('title', postForm.value.title)
                formData.append('content', postForm.value.content)
                formData.append('status', postForm.value.status)

                if (postForm.value.image) {
                    formData.append('image', postForm.value.image)
                }

                if (editingPost.value) {
                    formData.append('_method', 'PUT')
                    await axios.post(`/posts/${editingPost.value.id}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                } else {
                    await axios.post('/posts', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                }

                await fetchPosts()
                closeModal()
            } catch (err) {
                console.error('Error saving post:', err)
                error.value = err.response?.data?.message || 'Failed to save post'
            } finally {
                loading.value = false
            }
        }

        const viewPost = (post) => {
            viewingPost.value = post
            showViewModal.value = true
        }

        const editPost = (post) => {
            editingPost.value = post
            postForm.value = {
                title: post.title,
                content: post.content,
                status: post.status,
                image: null
            }
            showCreateModal.value = true
        }

        const deletePost = async (id) => {
            if (confirm('Are you sure you want to delete this post?')) {
                try {
                    await axios.delete(`/posts/${id}`)
                    await fetchPosts()
                } catch (error) {
                    console.error('Error deleting post:', error)
                    alert('Failed to delete post')
                }
            }
        }

        const handleFileUpload = (event) => {
            const file = event.target.files[0]
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    error.value = 'File size must be less than 2MB'
                    return
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
                if (!allowedTypes.includes(file.type)) {
                    error.value = 'Only JPG, PNG, and GIF files are allowed'
                    return
                }

                postForm.value.image = file
                error.value = ''
            }
        }

        const closeModal = () => {
            showCreateModal.value = false
            editingPost.value = null
            loading.value = false
            error.value = ''
            postForm.value = {
                title: '',
                content: '',
                status: 'draft',
                image: null
            }
            if (fileInput.value) {
                fileInput.value.value = ''
            }
        }

        const handleLogout = async () => {
            try {
                await authStore.logout()
                router.push('/users/login')
            } catch (error) {
                console.error('Logout error:', error)
                authStore.clearAuth()
                router.push('/users/login')
            }
        }

        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }

        const hasSearchQuery = computed(() => {
            return searchQuery.value.trim().length > 0
        })

        const searchResultsCount = computed(() => {
            return filteredPosts.value.length
        })

        const totalPostsCount = computed(() => {
            return allPosts.value.length
        })

        onMounted(() => {
            if (!authStore.isAuthenticated || !authStore.isUser) {
                router.push('/users/login')
                return
            }
            fetchPosts()
        })

        return {
            authStore,
            allPosts,
            posts,
            filteredPosts,
            showViewModal,
            showCreateModal,
            viewingPost,
            editingPost,
            loading,
            error,
            searchQuery,
            fileInput,
            postForm,
            hasSearchQuery,
            searchResultsCount,
            totalPostsCount,

            fetchPosts,
            searchPosts,
            clearSearch,
            savePost,
            editPost,
            viewPost,
            deletePost,
            handleFileUpload,
            closeModal,
            handleLogout,
            formatDate
        }
    }
}
</script>