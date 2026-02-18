<script setup>
defineProps({
  clips: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  deletingId: {
    validator: (value) => value === null || typeof value === 'number',
    default: null,
  },
})

const emit = defineEmits(['edit', 'delete'])
</script>

<template>
  <section class="panel">
    <header class="panel__header panel__header--inline">
      <h2>Clips</h2>
      <span class="pill">{{ clips.length }} total</span>
    </header>

    <p v-if="loading" class="empty-state">Loading clips...</p>
    <p v-else-if="clips.length === 0" class="empty-state">No clips match your filter.</p>

    <div v-else class="clip-grid">
      <article v-for="clip in clips" :key="clip.id" class="clip-card">
        <header class="clip-card__header">
          <h3>{{ clip.title }}</h3>
          <span :class="['status-dot', `status-dot--${clip.status}`]">{{ clip.status }}</span>
        </header>

        <p class="clip-card__description">{{ clip.description }}</p>

        <a :href="clip.url" class="clip-card__link" target="_blank" rel="noreferrer">
          {{ clip.url }}
        </a>

        <div class="clip-card__actions">
          <button class="btn btn--secondary" type="button" @click="emit('edit', clip)">Edit</button>
          <button
            class="btn btn--danger"
            :disabled="deletingId === clip.id"
            type="button"
            @click="emit('delete', clip.id)"
          >
            {{ deletingId === clip.id ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </article>
    </div>
  </section>
</template>
