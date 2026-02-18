<script setup>
import { reactive, ref } from 'vue'

defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['login', 'register'])

const mode = ref('login')
const localError = ref('')

const loginForm = reactive({
  email: '',
  password: '',
})

const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  passwordConfirmation: '',
})

function isValidEmail(value) {
  return /^\S+@\S+\.\S+$/.test(value)
}

function submitLogin() {
  localError.value = ''

  if (!isValidEmail(loginForm.email)) {
    localError.value = 'Please enter a valid email.'
    return
  }

  if (!loginForm.password) {
    localError.value = 'Password is required.'
    return
  }

  emit('login', {
    email: loginForm.email,
    password: loginForm.password,
  })
}

function submitRegister() {
  localError.value = ''

  if (!registerForm.name.trim()) {
    localError.value = 'Name is required.'
    return
  }

  if (!isValidEmail(registerForm.email)) {
    localError.value = 'Please enter a valid email.'
    return
  }

  if (registerForm.password.length < 8) {
    localError.value = 'Password must be at least 8 characters.'
    return
  }

  if (registerForm.password !== registerForm.passwordConfirmation) {
    localError.value = 'Password confirmation does not match.'
    return
  }

  emit('register', {
    name: registerForm.name,
    email: registerForm.email,
    password: registerForm.password,
    password_confirmation: registerForm.passwordConfirmation,
  })
}
</script>

<template>
  <section class="auth-card">
    <header>
      <h1>Clips Manager</h1>
      <p>Sign in or create an account to manage your video clips.</p>
    </header>

    <div class="auth-tabs">
      <button
        :class="['auth-tab', { 'auth-tab--active': mode === 'login' }]"
        type="button"
        @click="mode = 'login'"
      >
        Login
      </button>
      <button
        :class="['auth-tab', { 'auth-tab--active': mode === 'register' }]"
        type="button"
        @click="mode = 'register'"
      >
        Register
      </button>
    </div>

    <form v-if="mode === 'login'" class="auth-form" @submit.prevent="submitLogin">
      <label>
        Email
        <input v-model="loginForm.email" type="email" autocomplete="email" />
      </label>

      <label>
        Password
        <input v-model="loginForm.password" type="password" autocomplete="current-password" />
      </label>

      <button class="btn btn--primary" :disabled="loading" type="submit">
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>

    <form v-else class="auth-form" @submit.prevent="submitRegister">
      <label>
        Name
        <input v-model="registerForm.name" type="text" autocomplete="name" />
      </label>

      <label>
        Email
        <input v-model="registerForm.email" type="email" autocomplete="email" />
      </label>

      <label>
        Password
        <input v-model="registerForm.password" type="password" autocomplete="new-password" />
      </label>

      <label>
        Confirm password
        <input
          v-model="registerForm.passwordConfirmation"
          type="password"
          autocomplete="new-password"
        />
      </label>

      <button class="btn btn--primary" :disabled="loading" type="submit">
        {{ loading ? 'Creating account...' : 'Create account' }}
      </button>
    </form>

    <p v-if="localError || error" class="error-message">{{ localError || error }}</p>
  </section>
</template>
