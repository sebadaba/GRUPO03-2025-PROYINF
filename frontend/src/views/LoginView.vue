<template>
  <div class="login-view">
    <h1>Ensayo PAES</h1>
    <p class="subtitle">Plataforma de preparación para la Prueba de Acceso a la Educación Superior</p>
    
    <LoginForm />
  </div>
</template>

<script setup>
import LoginForm from '../components/LoginForm.vue';
</script>
<style scoped>
.login-view {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

h1 {
  margin-bottom: 0.5rem;
  color: #3498db;
}

.subtitle {
  margin-bottom: 3rem;
  color: #666;
}
</style>