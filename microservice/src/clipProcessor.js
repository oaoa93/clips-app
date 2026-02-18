const VALID_STATUSES = new Set(['active', 'inactive'])

function processClipPayload(payload) {
  if (!payload || typeof payload !== 'object') {
    return {
      valid: false,
      error: 'A JSON body with clip data is required.',
    }
  }

  const title = normalizeString(payload.title)
  const description = normalizeString(payload.description)
  const status = normalizeString(payload.status) || 'active'

  if (!title) {
    return {
      valid: false,
      error: 'The title field is required.',
    }
  }

  if (!VALID_STATUSES.has(status)) {
    return {
      valid: false,
      error: 'The status field must be active or inactive.',
    }
  }

  const normalizedUrl = normalizeUrl(payload.url)

  return {
    valid: true,
    processed: {
      slug: createSlug(title),
      estimatedDurationSeconds: estimateDurationSeconds(title, description),
      normalizedUrl,
      titleLength: title.length,
      status,
      metadata: {
        hasDescription: description.length > 0,
        sourceHost: normalizedUrl ? new URL(normalizedUrl).hostname : null,
      },
    },
  }
}

function normalizeString(value) {
  return typeof value === 'string' ? value.trim() : ''
}

function normalizeUrl(value) {
  const candidate = normalizeString(value)

  if (!candidate) {
    return null
  }

  try {
    const parsed = new URL(candidate)

    if (!['http:', 'https:'].includes(parsed.protocol)) {
      return null
    }

    return parsed.toString()
  } catch {
    return null
  }
}

function createSlug(title) {
  return title
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

function estimateDurationSeconds(title, description) {
  const words = `${title} ${description}`.trim().split(/\s+/).filter(Boolean).length
  const estimatedMinutes = Math.max(1, Math.ceil(words / 130))

  return estimatedMinutes * 60
}

module.exports = {
  processClipPayload,
}
