<template>
  <div class="container">
    <div class="header">
      <h1>Ensayo PAES: {{ subjectName }}</h1>
      <button @click="volverAlMenu" class="back-btn">Volver al menú</button>
    </div>

    <div v-if="!mostrarResumen">
      <div class="pregunta">
        <h2>Enunciado {{ indicePregunta + 1 }}</h2>

        <div
          v-for="alternativa in 4"
          :key="alternativa"
          class="alternativa"
        >
          <label>
            <input
              type="radio"
              :name="'pregunta-' + indicePregunta"
              :value="alternativa"
              v-model="respuestas[indicePregunta]"
            />
            Alternativa {{ alternativa }}
          </label>
        </div>
      </div>

      <div class="navegacion">
        <button
          @click="anteriorPregunta"
          :disabled="indicePregunta === 0"
        >
          Anterior
        </button>

        <button
          @click="siguientePregunta"
          :disabled="indicePregunta === totalPreguntas - 1"
        >
          Siguiente
        </button>
      </div>

      <button @click="terminarEnsayo" :disabled="respuestas.includes(null)">
        Terminar ensayo
      </button>
    </div>

    <div v-else class="resumen">
      <h2>Resumen de tus respuestas</h2>

      <p class="puntaje">
        Obtuviste {{ puntajeFinal }} de {{ totalPreguntas }} respuestas correctas
      </p>

      <ul>
        <li v-for="(respuesta, index) in respuestas" :key="index">
          Pregunta {{ index + 1 }}: Alternativa {{ respuesta }} —
          <span :class="respuesta === 1 ? 'correcta' : 'incorrecta'">
            {{ respuesta === 1 ? 'Correcta' : 'Incorrecta' }}
          </span>
        </li>
      </ul>

      <div class="buttons-container">
        <button @click="reiniciarEnsayo">
          Hacer de nuevo el ensayo
        </button>
        <button @click="volverAlMenu" class="secondary-btn">
          Volver al menú principal
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router';

const router = useRouter();
const totalPreguntas = 3
const indicePregunta = ref(0)
const respuestas = ref(Array(totalPreguntas).fill(null))
const mostrarResumen = ref(false)
const subjectName = ref('');

onMounted(() => {
  const user = localStorage.getItem('user');
  if (!user) {
    router.push('/');
    return;
  }
  
  // Obtener la asignatura seleccionada
  const selectedSubject = localStorage.getItem('selectedSubject');
  if (!selectedSubject) {
    router.push('/home');
    return;
  }
  
  // Establecer el nombre de la asignatura
  subjectName.value = JSON.parse(selectedSubject).name;
});

const siguientePregunta = () => {
  if (indicePregunta.value < totalPreguntas - 1) {
    indicePregunta.value++
  }
}

const anteriorPregunta = () => {
  if (indicePregunta.value > 0) {
    indicePregunta.value--
  }
}

const terminarEnsayo = () => {
  mostrarResumen.value = true
}

const puntajeFinal = computed(() =>
  respuestas.value.filter(r => r === 1).length
)

const reiniciarEnsayo = () => {
  respuestas.value = Array(totalPreguntas).fill(null)
  indicePregunta.value = 0
  mostrarResumen.value = false
}

const volverAlMenu = () => {
  router.push('/home');
}
</script>

<style scoped>
.container {
  width: 100%;
  max-width: 800px;
  min-height: 500px;
  margin: 2rem auto;
  padding: 1.5rem;
  font-family: sans-serif;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.back-btn {
  background-color: #95a5a6;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.back-btn:hover {
  background-color: #7f8c8d;
}

.pregunta {
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f9f9f9;
  border-radius: 6px;
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

.buttons-container {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 1rem;
}

.secondary-btn {
  background-color: #95a5a6;
}

.secondary-btn:hover {
  background-color: #7f8c8d;
}
</style>
