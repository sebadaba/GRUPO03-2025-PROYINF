<template>
  <div class="container">
    <div class="header">
      <h1>Ensayo PAES: {{ subjectName }}</h1>
      <button @click="volverAlMenu" class="back-btn">Volver al menú</button>
    </div>

    <div class="progress-bar" v-if="!mostrarResumen">
      <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
      <span class="progress-text">{{ answeredCount }}/{{ totalPreguntas }} respondidas</span>
    </div>

    <div v-if="!mostrarResumen">
      <QuestionDisplay
        :questions="[currentQuestion]"
        :selected-answers="selectedAnswers"
        :disabled="false"
        :show-correct-answers="false"
        :start-index="indicePregunta"
        mode="exam"
        @answer-selected="handleAnswerSelected"
      />

      <div class="navegacion">
        <button
          @click="anteriorPregunta"
          :disabled="indicePregunta === 0"
          class="nav-btn"
        >
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Anterior
        </button>

        <div class="question-counter">
          {{ indicePregunta + 1 }} de {{ totalPreguntas }}
        </div>

        <button
          @click="siguientePregunta"
          :disabled="indicePregunta === totalPreguntas - 1"
          class="nav-btn"
        >
          Siguiente
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
        </button>
      </div>

      <div class="finish-section">
        <button 
          @click="terminarEnsayo" 
          :disabled="!allQuestionsAnswered"
          class="finish-btn"
          :class="{ 'finish-btn-disabled': !allQuestionsAnswered }"
        >
          Terminar ensayo
        </button>
        <p v-if="!allQuestionsAnswered" class="finish-hint">
          Responde todas las preguntas para terminar el ensayo
        </p>
      </div>
    </div>

    <div v-else class="resumen">
      <h2>Resumen de tus respuestas</h2>

      <div class="score-summary">
        <div class="score-card">
          <div class="score-number">{{ puntajeFinal }}</div>
          <div class="score-total">de {{ totalPreguntas }}</div>
          <div class="score-label">respuestas correctas</div>
        </div>
        <div class="score-percentage">
          {{ Math.round((puntajeFinal / totalPreguntas) * 100) }}%
        </div>
      </div>

      <QuestionDisplay
        :questions="mockQuestions"
        :selected-answers="selectedAnswers"
        :disabled="true"
        :show-correct-answers="true"
        mode="review"
      />

      <div class="buttons-container">
        <button @click="reiniciarEnsayo" class="primary-btn">
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
import { useRouter } from 'vue-router'
import QuestionDisplay from '../components/QuestionDisplay.vue' 

const router = useRouter()
const totalPreguntas = 3
const indicePregunta = ref(0)
const respuestas = ref(Array(totalPreguntas).fill(null))
const mostrarResumen = ref(false)
const subjectName = ref('')

// Simulación de preguntas
const mockQuestions = ref([
  {
    id: 1,
    text: "¿Cuál es la capital de Chile?",
    options: [
      "Santiago",
      "Valparaíso", 
      "Concepción",
      "La Serena"
    ],
    correctOption: 0,
    subject: "Historia y Geografía",
    difficulty: "Fácil"
  },
  {
    id: 2,
    text: "¿Cuál es el resultado de 2 + 2?",
    options: [
      "3",
      "4",
      "5",
      "6"
    ],
    correctOption: 1,
    subject: "Matemáticas",
    difficulty: "Fácil"
  },
  {
    id: 3,
    text: "¿Quién escribió 'Cien años de soledad'?",
    options: [
      "Mario Vargas Llosa",
      "Gabriel García Márquez",
      "Isabel Allende",
      "Pablo Neruda"
    ],
    correctOption: 1,
    subject: "Lenguaje y Literatura",
    difficulty: "Media"
  }
])

onMounted(() => {
  const user = localStorage.getItem('user')
  if (!user) {
    router.push('/')
    return
  }
  
  const selectedSubject = localStorage.getItem('selectedSubject')
  if (!selectedSubject) {
    router.push('/home')
    return
  }
  
  subjectName.value = JSON.parse(selectedSubject).name
})

// Pregunta actual
const currentQuestion = computed(() => {
  return mockQuestions.value[indicePregunta.value]
})

// Respuestas seleccionadas en formato para QuestionDisplay
const selectedAnswers = computed(() => {
  const answers = {}
  respuestas.value.forEach((respuesta, index) => {
    if (respuesta !== null) {
      answers[mockQuestions.value[index].id] = respuesta - 1 // Convertir de 1-4 a 0-3
    }
  })
  return answers
})

// Progreso
const answeredCount = computed(() => {
  return respuestas.value.filter(r => r !== null).length
})

const progressPercentage = computed(() => {
  return (answeredCount.value / totalPreguntas) * 100
})

const allQuestionsAnswered = computed(() => {
  return !respuestas.value.includes(null)
})

const puntajeFinal = computed(() => {
  return respuestas.value.filter((respuesta, index) => {
    if (respuesta === null) return false
    return respuesta - 1 === mockQuestions.value[index].correctOption
  }).length
})

// Manejar selección de respuesta
const handleAnswerSelected = (event) => {
  const { questionId, optionIndex } = event
  const questionIndex = mockQuestions.value.findIndex(q => q.id === questionId)
  if (questionIndex !== -1) {
    respuestas.value[questionIndex] = optionIndex + 1 // Convertir de 0-3 a 1-4
  }
}

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

const reiniciarEnsayo = () => {
  respuestas.value = Array(totalPreguntas).fill(null)
  indicePregunta.value = 0
  mostrarResumen.value = false
}

const volverAlMenu = () => {
  router.push('/home')
}
</script>

<style scoped>
.container {
  width: 100%;
  max-width: 1000px;
  margin: 2rem auto;
  padding: 0;
  min-height: calc(100vh - 4rem);
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.header h1 {
  margin: 0;
  color: #1f2937;
  font-size: 1.75rem;
  font-weight: 700;
}

.back-btn {
  background: linear-gradient(45deg, #6b7280, #9ca3af);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.back-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(107, 114, 128, 0.4);
}

/* Barra de progreso */
.progress-bar {
  position: relative;
  width: 100%;
  height: 12px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 6px;
  margin-bottom: 2rem;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #10b981, #34d399);
  border-radius: 6px;
  transition: width 0.3s ease;
}

.progress-text {
  position: absolute;
  top: -30px;
  right: 0;
  color: white;
  font-size: 0.875rem;
  font-weight: 600;
}

/* Navegación */
.navegacion {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 2rem 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.nav-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

.nav-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
}

.nav-btn:disabled {
  background: #d1d5db;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.question-counter {
  font-size: 1.1rem;
  font-weight: 700;
  color: #374151;
  background: rgba(14, 165, 233, 0.1);
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  border: 2px solid rgba(14, 165, 233, 0.2);
}

/* Sección de finalizar */
.finish-section {
  text-align: center;
  margin: 2rem 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.finish-btn {
  background: linear-gradient(45deg, #16a34a, #22c55e);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 1rem 2rem;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(22, 197, 94, 0.3);
}

.finish-btn:hover:not(.finish-btn-disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(22, 197, 94, 0.4);
}

.finish-btn-disabled {
  background: #d1d5db !important;
  cursor: not-allowed !important;
  box-shadow: none !important;
  transform: none !important;
}

.finish-hint {
  margin-top: 1rem;
  color: #6b7280;
  font-size: 0.875rem;
  font-style: italic;
}

/* Resumen */
.resumen {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.resumen h2 {
  text-align: center;
  color: #1f2937;
  margin-bottom: 2rem;
  font-size: 1.875rem;
  font-weight: 700;
}

.score-summary {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
  margin-bottom: 3rem;
  padding: 2rem;
  background: linear-gradient(135deg, #0ea5e9, #06b6d4);
  border-radius: 16px;
  color: white;
}

.score-card {
  text-align: center;
}

.score-number {
  font-size: 3rem;
  font-weight: 900;
  line-height: 1;
}

.score-total {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0.5rem 0;
}

.score-label {
  font-size: 0.875rem;
  opacity: 0.9;
}

.score-percentage {
  font-size: 2rem;
  font-weight: 800;
  background: rgba(255, 255, 255, 0.2);
  padding: 1rem 1.5rem;
  border-radius: 12px;
}

.buttons-container {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 3rem;
}

.primary-btn {
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}

.primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
}

.secondary-btn {
  background: linear-gradient(45deg, #6b7280, #9ca3af);
  color: white;
  border: none;
  border-radius: 12px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.secondary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(107, 114, 128, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    margin: 1rem;
    padding: 1rem;
  }
  
  .header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .navegacion {
    flex-direction: column;
    gap: 1rem;
  }
  
  .score-summary {
    flex-direction: column;
    gap: 1rem;
  }
  
  .buttons-container {
    flex-direction: column;
  }
}
</style>