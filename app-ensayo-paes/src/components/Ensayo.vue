<template>
  <div>
    <h2>Pregunta {{ indice + 1 }}</h2>
    <div class="enunciado">
      <p>
        Enunciado {{ indice + 1 }} - Lorem ipsum dolor sit amet, consectetur
        adipiscing elit. Ut nec velit sit amet turpis varius hendrerit. Morbi
        sit amet dignissim dolor. Sed aliquet eget lectus et bibendum.
      </p>
    </div>
    <div class="alternativas">
      <div v-for="alternativa in 4" :key="alternativa" class="alternativa">
        <label>
          <input
            type="radio"
            :name="'pregunta-' + indice"
            :value="alternativa"
            v-model="respuestas[indice]"
          />
          Alternativa {{ alternativa }}
        </label>
      </div>
    </div>

    <div class="navegacion">
      <button @click="$emit('anterior')" :disabled="indice === 0">
        Anterior
      </button>
      <button @click="$emit('siguiente')" :disabled="indice === total - 1">
        Siguiente
      </button>
    </div>

    <button @click="$emit('terminar')" :disabled="respuestas.includes(null)">
      Terminar ensayo
    </button>
  </div>
</template>

<script setup>
defineProps(["indice", "respuestas", "total"]);
defineEmits(["siguiente", "anterior", "terminar"]);
</script>

<style scoped>
.enunciado {
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 6px;
  text-align: left;
}
.alternativas {
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 6px;
  text-align: left;
}
.alternativa {
  margin: 0.5rem 0;
}
.navegacion {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}
button {
  padding: 0.5rem 1rem;
  background: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
