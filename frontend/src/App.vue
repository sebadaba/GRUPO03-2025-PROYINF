<template>
  <div class="app-container">
    <router-view />
    <footer class="main-footer">
      © {{ new Date().getFullYear() }} PAES ensayo FOOOTER
    </footer>
  </div>
</template>

<script setup>
import {ref, computed, onMounted} from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);

const isAuthenticated = computed(() => user.value !== null);
const userName = computed(() => user.value?.name || '');

onMounted(() => {
  // Verificar si hay un usuario en localStorage
  const storedUser = localStorage.getItem('user');
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }
});

const logout = () => {
  // Eliminar la información del usuario
  localStorage.removeItem('user');
  user.value = null;
  
  // Redirigir a la página de login
  router.push('/');
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family:
    'Inter',
    'Roboto',
    -apple-system,
    BlinkMacSystemFont,
    'Segoe UI',
    'Helvetica Neue',
    Arial,
    sans-serif;
  background-color: #f5f5f5;
}

.app-container {
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

header {
  background-color: #3498db;
  color: white;
  padding: 1rem 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logout-btn {
  background-color: transparent;
  border: 1px solid white;
  color: white;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.logout-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.main-footer {
  width: 100%;
  padding: 1em 0;
  background: #f5f5f5;
  color: #666;
  text-align: center;
  border-top: 1px solid #e0e0e0;
  margin-top: auto;
  font-size: 0.95em;
}
</style>