<script setup>
import { reactive, ref, watch } from 'vue'

const props = defineProps({
  mode: {
    type: String,
    default: 'create',
  },
  initialValues: {
    type: Object,
    default: () => ({
      title: '',
      description: '',
      url: '',
      status: 'active',
    }),
  },
  busy: {
    type: Boolean,
    default: false,
  },
  externalError: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['submit', 'cancel'])

const localError = ref('')

const form = reactive({
  title: '',
  description: '',
  url: '',
  status: 'active',
})

watch(
  () => props.initialValues,
  (next) => {
    form.title = next.title ?? ''
    form.description = next.description ?? ''
    form.url = next.url ?? ''
    form.status = next.status ?? 'active'
    localError.value = ''
  },
  { immediate: true, deep: true },
)

function isValidUrl(value) {
  try {
    const parsed = new URL(value)
    return parsed.protocol === 'http:' || parsed.protocol === 'https:'
  } catch {
    return false
  }
}

function submit() {
  localError.value = ''

  if (!form.title.trim()) {
    localError.value = 'Title is required.'
    return
  }

  if (!form.description.trim()) {
    localError.value = 'Description is required.'
    return
  }

  if (!isValidUrl(form.url)) {
    localError.value = 'URL must be valid and start with http or https.'
    return
  }

  if (!['active', 'inactive'].includes(form.status)) {
    localError.value = 'Status must be active or inactive.'
    return
  }

  emit('submit', {
    title: form.title.trim(),
    description: form.description.trim(),
    url: form.url.trim(),
    status: form.status,
  })
}
</script>

<template>
  <form class="clip-form" @submit.prevent="submit">
    <label>
      Title
      <input v-model="form.title" maxlength="180" type="text" />
    </label>

    <label>
      Description
      <textarea v-model="form.description" rows="4" />
    </label>

    <label>
      URL
      <input v-model="form.url" type="url" />
    </label>

    <label>
      Status
      <select v-model="form.status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </label>

    <div class="clip-form__actions">
      <button class="btn btn--primary" :disabled="busy" type="submit">
        {{ busy ? 'Saving...' : mode === 'edit' ? 'Save changes' : 'Create clip' }}
      </button>

      <button class="btn btn--ghost" type="button" @click="emit('cancel')">Cancel</button>
    </div>

    <p v-if="localError || externalError" class="error-message">{{ localError || externalError }}</p>
  </form>
</template>
