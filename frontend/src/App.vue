<script setup>
import { computed, onMounted, ref } from 'vue'
import AuthPanel from './components/AuthPanel.vue'
import BaseModal from './components/BaseModal.vue'
import ClipForm from './components/ClipForm.vue'
import ClipList from './components/ClipList.vue'
import { useAuthStore } from './stores/auth'
import { useClipStore } from './stores/clips'

const authStore = useAuthStore()
const clipStore = useClipStore()

const searchTerm = ref('')
const statusFilter = ref('all')
const editingClip = ref(null)
const isClipModalOpen = ref(false)
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
const clipModalTitle = computed(() => (editingClip.value ? 'Edit clip' : 'Create clip'))
const clipModalDescription = computed(() =>
  editingClip.value
    ? 'Adjust clip metadata and save the update.'
    : 'Create a new clip entry with basic metadata.',
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
  closeClipModal()
  searchTerm.value = ''
  statusFilter.value = 'all'
  pageError.value = ''
}

function openCreateModal() {
  editingClip.value = null
  pageError.value = ''
  isClipModalOpen.value = true
}

function closeClipModal() {
  isClipModalOpen.value = false
  editingClip.value = null
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

  closeClipModal()
  pageError.value = ''
}

function handleEdit(clip) {
  pageError.value = ''
  editingClip.value = { ...clip }
  isClipModalOpen.value = true
}

async function handleDelete(clipId) {
  pageError.value = ''

  const isSuccessful = await clipStore.deleteClip(clipId)
  if (!isSuccessful) {
    pageError.value = clipStore.error
    return
  }

  if (editingClip.value?.id === clipId && isClipModalOpen.value) {
    closeClipModal()
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
        <div class="workspace__heading">
          <h1>Clip App</h1>
          <p>A minimal clip manager.</p>
        </div>

        <div class="workspace__actions">
          <button class="btn btn--primary" type="button" @click="openCreateModal">Create clip</button>
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

      <ClipList
        :clips="filteredClips"
        :loading="clipStore.loading"
        :deleting-id="clipStore.deletingId"
        @edit="handleEdit"
        @delete="handleDelete"
      />

      <BaseModal
        :open="isClipModalOpen"
        :title="clipModalTitle"
        :description="clipModalDescription"
        @close="closeClipModal"
      >
        <ClipForm
          :mode="formMode"
          :initial-values="formInitialValues"
          :busy="clipStore.saving"
          :external-error="pageError"
          @submit="handleSaveClip"
          @cancel="closeClipModal"
        />
      </BaseModal>

      <p v-if="pageError" class="error-message error-message--page">{{ pageError }}</p>
      <p v-else-if="clipStore.error" class="error-message error-message--page">{{ clipStore.error }}</p>
    </div>
  </main>
</template>
