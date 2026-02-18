<script setup>
import { computed, onMounted, ref } from 'vue'
import AuthPanel from './components/AuthPanel.vue'
import ClipForm from './components/ClipForm.vue'
import ClipList from './components/ClipList.vue'
import { useAuthStore } from './stores/auth'
import { useClipStore } from './stores/clips'

const authStore = useAuthStore()
const clipStore = useClipStore()

const searchTerm = ref('')
const statusFilter = ref('all')
const editingClip = ref(null)
const pageError = ref('')

const filteredClips = computed(() => {
  const normalizedTitle = searchTerm.value.trim().toLowerCase()

  return clipStore.clips.filter((clip) => {
    const matchesTitle =
      normalizedTitle.length === 0 || clip.title.toLowerCase().includes(normalizedTitle)
    const matchesStatus = statusFilter.value === 'all' || clip.status === statusFilter.value

    return matchesTitle && matchesStatus
  })
})

const formMode = computed(() => (editingClip.value ? 'edit' : 'create'))
const formInitialValues = computed(
  () =>
    editingClip.value ?? {
      title: '',
      description: '',
      url: '',
      status: 'active',
    },
)

onMounted(async () => {
  if (authStore.isAuthenticated) {
    const hasValidSession = await authStore.fetchCurrentUser()

    if (hasValidSession) {
      await clipStore.fetchClips()
    }
  }
})

async function handleLogin(payload) {
  const isSuccessful = await authStore.login(payload)

  if (isSuccessful) {
    pageError.value = ''
    await clipStore.fetchClips()
  }
}

async function handleRegister(payload) {
  const isSuccessful = await authStore.register(payload)

  if (isSuccessful) {
    pageError.value = ''
    await clipStore.fetchClips()
  }
}

async function handleLogout() {
  await authStore.logout()
  clipStore.reset()
  editingClip.value = null
  searchTerm.value = ''
  statusFilter.value = 'all'
  pageError.value = ''
}

async function handleSaveClip(payload) {
  let isSuccessful = false

  if (editingClip.value) {
    isSuccessful = await clipStore.updateClip(editingClip.value.id, payload)
  } else {
    isSuccessful = await clipStore.createClip(payload)
  }

  if (!isSuccessful) {
    pageError.value = clipStore.error
    return
  }

  editingClip.value = null
  pageError.value = ''
}

function handleEdit(clip) {
  pageError.value = ''
  editingClip.value = { ...clip }
}

async function handleDelete(clipId) {
  pageError.value = ''

  const isSuccessful = await clipStore.deleteClip(clipId)
  if (!isSuccessful) {
    pageError.value = clipStore.error
    return
  }

  if (editingClip.value?.id === clipId) {
    editingClip.value = null
  }
}
</script>

<template>
  <main class="layout">
    <AuthPanel
      v-if="!authStore.isAuthenticated"
      :loading="authStore.loading"
      :error="authStore.error"
      @login="handleLogin"
      @register="handleRegister"
    />

    <div v-else class="workspace">
      <header class="workspace__header panel">
        <div>
          <h1>Clips Dashboard</h1>
          <p>Manage and filter your video clips in real time.</p>
        </div>

        <div class="workspace__actions">
          <span class="user-pill">{{ authStore.user?.email ?? 'Authenticated user' }}</span>
          <button class="btn btn--ghost" :disabled="authStore.loading" @click="handleLogout">
            Logout
          </button>
        </div>
      </header>

      <section class="panel filters">
        <label>
          Search by title
          <input v-model="searchTerm" type="text" placeholder="Start typing..." />
        </label>

        <label>
          Filter by status
          <select v-model="statusFilter">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </label>
      </section>

      <ClipForm
        :mode="formMode"
        :initial-values="formInitialValues"
        :busy="clipStore.saving"
        @submit="handleSaveClip"
        @cancel="editingClip = null"
      />

      <ClipList
        :clips="filteredClips"
        :loading="clipStore.loading"
        :deleting-id="clipStore.deletingId"
        @edit="handleEdit"
        @delete="handleDelete"
      />

      <p v-if="pageError" class="error-message error-message--page">{{ pageError }}</p>
      <p v-else-if="clipStore.error" class="error-message error-message--page">{{ clipStore.error }}</p>
    </div>
  </main>
</template>
