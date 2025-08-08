<template>
    <div class="bg-light min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <i class="bi bi-shield-fill-check text-warning me-2"></i>
                    <strong>Admin Dashboard</strong>
                </div>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" role="button"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-person-gear me-2"></i>
                            Welcome, {{ authStore.user?.name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Admin Profile</a>
                            </li>
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

        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Posts</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ stats.totalPosts }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-journal-text fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-success text-uppercase mb-1">Published Posts</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ stats.publishedPosts }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-warning text-uppercase mb-1">Draft Posts</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ stats.draftPosts }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Users</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ stats.totalUsers }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-people fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row">
                <div class="col-12">
                    <!-- Header Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="m-0 fw-bold text-primary">
                                    <i class="bi bi-journal-bookmark me-2"></i>
                                    All Posts Management
                                </h6>
                                <small class="text-muted">Create, update, and delete any post in the system</small>
                            </div>
                            <button @click="showCreateModal = true" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-plus-circle me-2"></i>
                                Create New Post
                            </button>
                        </div>
                    </div>

                    <!-- Filters and Search -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input v-model="searchQuery" @input="searchPosts" type="text"
                                            class="form-control"
                                            placeholder="Search posts by title, author, or content..." />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="statusFilter" @change="filterPosts" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select v-model="authorFilter" @change="filterPosts" class="form-select">
                                        <option value="">All Authors</option>
                                        <option value="User">Users</option>
                                        <option value="Admin">Admins</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Posts Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 fw-bold text-primary">Posts List</h6>
                        </div>
                        <div class="card-body">
                            <!-- Loading State -->
                            <div v-if="loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading posts...</p>
                            </div>

                            <div v-else>
                                <div v-if="filteredPosts.length > 0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Post Details</th>
                                                    <th>Author</th>
                                                    <th>Status</th>
                                                    <th>Dates</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="post in paginatedPosts" :key="post.id">
                                                    <td>
                                                        <div class="d-flex align-items-start">
                                                            <div v-if="post.image" class="me-3">
                                                                <img :src="post.image" alt="Post image"
                                                                    class="img-thumbnail"
                                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold mb-1">{{ post.title || 'Untitled' }}
                                                                </h6>
                                                                <p class="text-muted small mb-0">{{ post.content ?
                                                                    post.content.substring(0, 100) + '...' : 'No content' }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi me-2"
                                                                :class="post.author?.type === 'admin' ? 'bi-shield-check text-primary' : 'bi-person-circle text-success'"></i>
                                                            <div>
                                                                <div class="fw-semibold">{{ post.author?.name ||
                                                                    'Unknown' }}</div>
                                                                <small class="text-muted text-capitalize">{{
                                                                    post.author?.type || 'user' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge"
                                                            :class="post.status === 'published' ? 'bg-success' : 'bg-warning'">
                                                            <i class="bi"
                                                                :class="post.status === 'published' ? 'bi-check-circle' : 'bi-clock'"></i>
                                                            {{ post.status || 'draft' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <small class="text-muted d-block">
                                                                <i class="bi bi-calendar-plus me-1"></i>
                                                                Created: {{ formatDate(post.created_at) }}
                                                            </small>
                                                            <small class="text-muted d-block"
                                                                v-if="post.updated_at && post.updated_at !== post.created_at">
                                                                <i class="bi bi-calendar-check me-1"></i>
                                                                Updated: {{ formatDate(post.updated_at) }}
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
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
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <nav v-if="!loading && totalPages > 1" class="mt-4">
                                        <ul class="pagination justify-content-center">
                                            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                                <button class="page-link" @click="changePage(currentPage - 1)"
                                                    :disabled="currentPage === 1">
                                                    <i class="bi bi-chevron-left"></i>
                                                    Previous
                                                </button>
                                            </li>

                                            <li v-if="currentPage > 3" class="page-item">
                                                <button @click="changePage(1)" class="page-link">1</button>
                                            </li>

                                            <li v-if="currentPage > 4" class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>

                                            <li v-for="page in getVisiblePages" :key="page" class="page-item"
                                                :class="{ active: page === currentPage }">
                                                <button @click="changePage(page)" class="page-link">{{ page }}</button>
                                            </li>

                                            <li v-if="currentPage < totalPages - 3" class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>

                                            <li v-if="currentPage < totalPages - 2" class="page-item">
                                                <button @click="changePage(totalPages)" class="page-link">{{ totalPages
                                                    }}</button>
                                            </li>

                                            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                                <button class="page-link" @click="changePage(currentPage + 1)"
                                                    :disabled="currentPage === totalPages">
                                                    Next
                                                    <i class="bi bi-chevron-right"></i>
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="text-center mt-2">
                                            <small class="text-muted">
                                                Showing {{ paginationInfo.from }} to {{ paginationInfo.to }} of {{
                                                paginationInfo.total }} posts
                                                (Page {{ currentPage }} of {{ totalPages }})
                                            </small>
                                        </div>
                                    </nav>

                                    <div v-else class="text-center mt-3">
                                        <small class="text-muted">
                                            Showing {{ filteredPosts.length }} post{{ filteredPosts.length === 1 ? '' :
                                            's' }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-else class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="text-muted">
                                        {{ (searchQuery || statusFilter || authorFilter) ? 'No posts match your filters'
                                        : 'No posts found' }}
                                    </h5>
                                    <p class="text-muted mb-4">
                                        {{ (searchQuery || statusFilter || authorFilter) ? 'Try adjusting your searchcriteria.' : 'Get started by creating the first post.' }}
                                    </p>
                                    <button v-if="searchQuery || statusFilter || authorFilter" @click="resetFilters"
                                        class="btn btn-outline-primary me-2">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                                    </button>
                                    <button @click="showCreateModal = true" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>Create First Post
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi" :class="editingPost ? 'bi-pencil' : 'bi-plus-circle'"></i>
                            {{ editingPost ? 'Edit Post' : 'Create New Post' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" @click="closeModal"
                            :disabled="loading"></button>
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
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-fonts me-1"></i>Post Title
                                    </label>
                                    <input v-model="postForm.title" type="text" class="form-control form-control-lg"
                                        placeholder="Enter a compelling title for the post" required />
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-flag me-1"></i>Status
                                    </label>
                                    <select v-model="postForm.status" class="form-select form-select-lg">
                                        <option value="draft">
                                            <i class="bi bi-clock"></i> Draft
                                        </option>
                                        <option value="published">
                                            <i class="bi bi-check-circle"></i> Published
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-textarea-t me-1"></i>Content
                                    </label>
                                    <textarea v-model="postForm.content" class="form-control" rows="8"
                                        placeholder="Write the post content here..." required></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-image me-1"></i>Featured Image
                                    </label>
                                    <input ref="fileInput" type="file" accept="image/*" @change="handleFileUpload"
                                        class="form-control" />
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        JPG, PNG, GIF - Max 2MB
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" v-if="!editingPost">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Author Type
                                    </label>
                                    <select v-model="postForm.author_type" class="form-select">
                                        <option value="admin">Admin Post</option>
                                        <option value="user">User Post</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Choose who this post should be attributed to
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal" :disabled="loading">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
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
    name: 'AdminDashboard',
    setup() {
        const router = useRouter()
        const authStore = useAuthStore()
        //   const posts = ref([])
        const allPosts = ref([])
        const currentPage = ref(1)
        const postsPerPage = ref(5)
        const showCreateModal = ref(false)
        const showViewModal = ref(false)
        const editingPost = ref(null)
        const viewingPost = ref(null)
        const loading = ref(false)
        const error = ref('')
        const searchQuery = ref('')
        const statusFilter = ref('')
        const authorFilter = ref('')
        const fileInput = ref(null)

        const postForm = ref({
            title: '',
            content: '',
            status: 'draft',
            author_type: 'admin',
            image: null
        })

        const stats = computed(() => {
            return {
                totalPosts: allPosts.value.length,
                publishedPosts: allPosts.value.filter(p => p.status === 'published').length,
                draftPosts: allPosts.value.filter(p => p.status === 'draft').length,
                totalUsers: new Set(allPosts.value.map(p => p.author?.id)).size
            }
        })
        const getVisiblePages = computed(() => {
            const visible = []
            const start = Math.max(1, currentPage.value - 2)
            const end = Math.min(totalPages.value, currentPage.value + 2)

            for (let i = start; i <= end; i++) {
                visible.push(i)
            }

            return visible
        })

        const filteredPosts = computed(() => {
            let filtered = allPosts.value
            console.log('All posts for filtering:', filtered.length)

            if (searchQuery.value) {
                const query = searchQuery.value.toLowerCase()
                filtered = filtered.filter(post =>
                    post.title.toLowerCase().includes(query) ||
                    post.content.toLowerCase().includes(query) ||
                    post.author?.name.toLowerCase().includes(query)
                )
            }

            if (statusFilter.value) {
                filtered = filtered.filter(post => post.status === statusFilter.value)
                console.log('After status filter:', filtered.length)
            }

            if (authorFilter.value) {
                console.log('Applying author filter:', authorFilter.value)
                console.log('Sample post author data:', filtered[0]?.author)

                filtered = filtered.filter(post => {
                    try {
                        const authorType = post.author?.type || post.author_type || 'user'
                        console.log('Comparing:', authorType, 'with filter:', authorFilter.value)
                        return authorType === authorFilter.value
                    } catch (error) {
                        console.error('Author filter error for post:', post, error)
                        return false
                    }
                })
                console.log('After author filter:', filtered.length)
            }

            return filtered
        })

        const paginatedPosts = computed(() => {
            const start = (currentPage.value - 1) * postsPerPage.value
            const end = start + postsPerPage.value
            return filteredPosts.value.slice(start, end)
        })

        const totalPages = computed(() => {
            return Math.ceil(filteredPosts.value.length / postsPerPage.value)
        })

        const paginationInfo = computed(() => {
            return {
                current_page: currentPage.value,
                last_page: totalPages.value,
                total: filteredPosts.value.length,
                from: ((currentPage.value - 1) * postsPerPage.value) + 1,
                to: Math.min(currentPage.value * postsPerPage.value, filteredPosts.value.length)
            }
        })

        const fetchPosts = async () => {
            loading.value = true
            try {
                const response = await axios.get('/posts')
                allPosts.value = response.data.data || response.data


                currentPage.value = 1

            } catch (error) {
                console.error('Error fetching posts:', error)
                error.value = 'Failed to load posts'

            } finally {
                loading.value = false
            }
        }

        const searchPosts = () => {
            currentPage.value = 1
        }

        const filterPosts = () => {
            currentPage.value = 1
        }

        const resetFilters = () => {
            searchQuery.value = ''
            statusFilter.value = ''
            authorFilter.value = ''
            currentPage.value = 1
        }

        const changePage = (page) => {
            if (page >= 1 && page <= totalPages.value) {
                currentPage.value = page
            }
        }

        const savePost = async () => {
            loading.value = true
            error.value = ''

            try {
                const formData = new FormData()
                formData.append('title', postForm.value.title)
                formData.append('content', postForm.value.content)
                formData.append('status', postForm.value.status)

                if (!editingPost.value) {
                    formData.append('author_type', postForm.value.author_type)
                }

                if (postForm.value.image) {
                    formData.append('image', postForm.value.image)
                }

                let response
                if (editingPost.value) {
                    formData.append('_method', 'PUT')
                    response = await axios.post(`/posts/${editingPost.value.id}`, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                } else {
                    response = await axios.post('/posts', formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                }

                await fetchPosts()
                closeModal()
            } catch (err) {
                console.error('Error saving post:', err)
                error.value = err.response?.data?.message || 'Failed to save post'

                try {
                    if (editingPost.value) {
                        await axios.put(`/posts/${editingPost.value.id}`, {
                            title: postForm.value.title,
                            content: postForm.value.content,
                            status: postForm.value.status
                        })
                    } else {
                        await axios.post('/posts', {
                            title: postForm.value.title,
                            content: postForm.value.content,
                            status: postForm.value.status
                        })
                    }
                    await fetchPosts()
                    closeModal()
                    error.value = ''
                } catch (fallbackError) {
                    console.error('Fallback save also failed:', fallbackError)
                }
            } finally {
                loading.value = false
            }
        }

        const editPost = (post) => {
            editingPost.value = post
            postForm.value = {
                title: post.title,
                content: post.content,
                status: post.status,
                author_type: post.author?.type || 'admin',
                image: null
            }
            showCreateModal.value = true
        }

        const viewPost = (post) => {
            viewingPost.value = post
            showViewModal.value = true
        }

        const deletePost = async (id) => {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                try {
                    await axios.delete(`/posts/${id}`)
                    await fetchPosts()
                } catch (error) {
                    console.error('Error deleting post:', error)
                    try {
                        await axios.delete(`/posts/${id}`)
                        await fetchPosts()
                    } catch (fallbackError) {
                        alert('Failed to delete post. Please try again.')
                        console.error('Fallback delete also failed:', fallbackError)
                    }
                }
            }
        }

        const handleFileUpload = (event) => {
            const file = event.target.files[0]
            if (file) {
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    error.value = 'File size must be less than 2MB'
                    event.target.value = ''
                    return
                }

                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
                if (!allowedTypes.includes(file.type)) {
                    error.value = 'Only JPG, PNG, GIF, and WebP files are allowed'
                    event.target.value = ''
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
                author_type: 'admin',
                image: null
            }
            if (fileInput.value) {
                fileInput.value.value = ''
            }
        }

        const handleLogout = async () => {
            try {
                await authStore.logout()
                router.push('/admins/login')
            } catch (error) {
                console.error('Logout error:', error)
                authStore.clearAuth()
                router.push('/admins/login')
            }
        }

        const formatDate = (dateString) => {
            if (!dateString) return 'N/A'

            try {
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            } catch (error) {
                return 'Invalid Date'
            }
        }


        onMounted(() => {
            if (!authStore.isAuthenticated || !authStore.isAdmin) {
                router.push('/admins/login')
                return
            }

            fetchPosts()
        })

        return {
            // State
            authStore,
            // posts,
            allPosts,
            filteredPosts,
            paginatedPosts,
            currentPage,
            totalPages,
            paginationInfo,
            getVisiblePages,
            showCreateModal,
            showViewModal,
            editingPost,
            viewingPost,
            loading,
            error,
            searchQuery,
            statusFilter,
            authorFilter,
            fileInput,
            postForm,
            stats,

            fetchPosts,
            searchPosts,
            filterPosts,
            changePage,
            savePost,
            editPost,
            viewPost,
            deletePost,
            handleFileUpload,
            closeModal,
            handleLogout,
            formatDate,
            resetFilters
        }
    }
}
</script>

<style scoped>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

.content {
    white-space: pre-wrap;
    line-height: 1.6;
}

.img-thumbnail {
    transition: transform 0.2s ease;
}

.img-thumbnail:hover {
    transform: scale(1.1);
    cursor: pointer;
}

.card {
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.btn-group .btn {
    margin: 0 1px;
}

.modal-xl {
    max-width: 1200px;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.badge {
    font-size: 0.75em;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.1em;
}

.table td {
    vertical-align: middle;
}

.navbar-brand {
    font-size: 1.25rem;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }

    .btn-group .btn {
        margin: 1px 0;
        border-radius: 0.375rem !important;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .modal-xl {
        max-width: 95%;
    }
}
</style>