<template>
  <div class="menu">
    <h2>Selecciona tu rol</h2>

    <div class="roles">
      <button @click="seleccionarRol('alumno')">Alumno</button>
      <button disabled>Docente (próximamente)</button>
      <button disabled>Visualizador de Reportes (próximamente)</button>
    </div>

    <div v-if="rol === 'alumno'" class="confirmacion">
      <p>¿Deseas comenzar un ensayo?</p>
      <button @click="$emit('comenzar')">Sí, comenzar</button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const rol = ref(null);

const seleccionarRol = (r) => {
  rol.value = r;
};
</script>

<style scoped>
.menu {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.roles {
  display: flex;
  flex-direction: column;
  gap: 1rem; /* Espacio entre los botones */
  width: 200px;
}

.roles button {
  padding: 0.75rem;
  border: 1px solid #ccc;
  background-color: #f5f5f5;
  font-size: 1rem;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.3s ease; /* Animación para el hover */
}

.roles button:hover {
  background-color: #e0e0e0; /* Color de fondo cuando el mouse pasa sobre el botón */
  transform: scale(1.05); /* Hace que el botón crezca ligeramente */
}

.roles button:disabled {
  background-color: #eee;
  color: #888;
  cursor: not-allowed;
}

.confirmacion {
  margin-top: 1rem;
  text-align: center;
}
</style>
