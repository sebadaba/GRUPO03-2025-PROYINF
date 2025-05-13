<template>
  <div class="resumen">
    <h2>Resumen de tus respuestas</h2>

    <p class="puntaje">
      Obtuviste {{ puntaje }} de {{ total }} respuestas correctas
    </p>

    <ul>
      <li v-for="(respuesta, index) in respuestas" :key="index">
        Pregunta {{ index + 1 }}: Alternativa {{ respuesta }} —
        <span :class="respuesta === 1 ? 'correcta' : 'incorrecta'">
          {{ respuesta === 1 ? "Correcta" : "Incorrecta" }}
        </span>
      </li>
    </ul>

    <button @click="$emit('reiniciar')">Hacer de nuevo el ensayo</button>
  </div>
</template>

<script setup>
defineProps(["respuestas", "puntaje", "total"]);
defineEmits(["reiniciar"]);
</script>

<style scoped>
.resumen {
  margin-top: 2rem;
  background: #eee;
  padding: 1rem;
  border-radius: 6px;
  font-size: 1rem;
}
.correcta {
  color: green;
  font-weight: bold;
}
.incorrecta {
  color: red;
  font-weight: bold;
}
.puntaje {
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 1rem;
}
button {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: #e67e22;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:hover {
  background: #d35400;
}
</style>
