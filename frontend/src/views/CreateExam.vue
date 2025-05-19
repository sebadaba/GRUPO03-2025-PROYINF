<template> 
  <div class="create-exam"> 
    <header class="main-header"> 
      <div class="header-content"> 
        <h1>Crear Ensayo - PAES</h1> 
        <div class="user-info"> 
          <span>{{ userName }}</span> 
          <button @click="logout" class="logout-btn">Cerrar Sesión</button> 
        </div> 
      </div> 
    </header> 
    
    <main> 
      <div class="actions-bar"> 
        <button @click="navigateTo('/teacher')" class="back-btn">Volver al Panel</button> 
      </div> 
      
      <div class="exam-form"> 
        <h2>Configuración del Ensayo de {{ teacherSubject }}</h2> 
        
        <div class="form-group"> 
          <label>Nombre del Ensayo:</label>
          <input type="text" v-model="examConfig.name" placeholder="Ej: Ensayo de {{ teacherSubject }} - Nivel Medio">
        </div>
        
        <!-- Asignatura fija (no se puede cambiar) -->
        <div class="form-group">
          <label>Asignatura:</label>
          <input type="text" :value="teacherSubject" disabled class="disabled-input">
        </div>
        
        <div class="form-group">
          <label>Dificultad:</label>
          <select v-model="examConfig.difficulty">
            <option value="Fácil">Fácil</option>
            <option value="Media">Media</option>
            <option value="Difícil">Difícil</option>
            <option value="Mixta">Mixta</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Número de preguntas:</label>
          <input type="number" v-model.number="examConfig.questionCount" min="1" max="50">
        </div>
        
        <div class="form-group">
          <label>Tiempo límite (minutos):</label>
          <input type="number" v-model.number="examConfig.timeLimit" min="0" max="180">
          <span class="help-text">0 = Sin límite de tiempo</span>
        </div>
        
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" v-model="examConfig.randomOrder">
            Mostrar preguntas en orden aleatorio
          </label>
        </div>
        
        <div class="form-group checkbox-group">
          <label class="checkbox-label">
            <input type="checkbox" v-model="examConfig.showResults">
            Mostrar resultados al finalizar
          </label>
        </div>
        
        <h3>Selección de Preguntas</h3>
        
        <div class="question-selection">
          <div class="selection-method">
            <label>
              <input type="radio" v-model="selectionMethod" value="auto">
              Selección automática
            </label>
            <label>
              <input type="radio" v-model="selectionMethod" value="manual">
              Selección manual
            </label>
          </div>
          
          <div v-if="selectionMethod === 'auto'" class="auto-selection">
            <p>El sistema seleccionará automáticamente {{ examConfig.questionCount }} preguntas 
              de {{ examConfig.subject }} con dificultad {{ examConfig.difficulty.toLowerCase() }}.</p>
            
            <div v-if="availableQuestions.length > 0" class="question-count">
              <p>Preguntas disponibles: {{ availableQuestions.length }}</p>
            </div>
            
            <div v-else class="no-questions">
              <p>No hay suficientes preguntas disponibles con los criterios seleccionados.</p>
              <button @click="navigateTo('/question-bank')" class="action-btn">
                Ir al Banco de Preguntas
              </button>
            </div>
          </div>
          
          <div v-if="selectionMethod === 'manual'" class="manual-selection">
            <div class="filter-bar">
              <div class="filter-group">
                <label>Filtrar por dificultad:</label>
                <select v-model="questionFilter.difficulty">
                  <option value="">Todas</option>
                  <option value="Fácil">Fácil</option>
                  <option value="Media">Media</option>
                  <option value="Difícil">Difícil</option>
                </select>
              </div>
              
              <div class="search-group">
                <input type="text" v-model="questionFilter.search" placeholder="Buscar pregunta...">
              </div>
            </div>
            
            <div class="questions-container">
              <div class="available-questions">
                <h4>Preguntas Disponibles</h4>
                <div v-if="filteredQuestions.length > 0" class="questions-list">
                  <div 
                    v-for="question in filteredQuestions" 
                    :key="question.id"
                    class="question-item"
                    :class="{ selected: isQuestionSelected(question.id) }"
                    @click="toggleQuestionSelection(question)"
                  >
                    <div class="question-header">
                      <span class="question-difficulty" :class="question.difficulty.toLowerCase()">
                        {{ question.difficulty }}
                      </span>
                    </div>
                    <p class="question-text">{{ question.text }}</p>
                  </div>
                </div>
                <div v-else class="no-questions">
                  No hay preguntas disponibles con los filtros seleccionados.
                </div>
              </div>
              
              <div class="selected-questions">
                <h4>Preguntas Seleccionadas ({{ selectedQuestions.length }}/{{ examConfig.questionCount }})</h4>
                <div v-if="selectedQuestions.length > 0" class="questions-list">
                  <div 
                    v-for="(question, index) in selectedQuestions" 
                    :key="question.id"
                    class="question-item"
                  >
                    <div class="question-header">
                      <span class="question-number">{{ index + 1 }}</span>
                      <span class="question-difficulty" :class="question.difficulty.toLowerCase()">
                        {{ question.difficulty }}
                      </span>
                      <button @click="removeQuestion(question)" class="remove-btn">×</button>
                    </div>
                    <p class="question-text">{{ question.text }}</p>
                  </div>
                </div>
                <div v-else class="no-questions">
                  No has seleccionado ninguna pregunta aún.
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-actions">
          <button @click="saveExam" class="save-btn" :disabled="!isFormValid">
            Guardar Ensayo
          </button>
          <button @click="navigateTo('/teacher')" class="cancel-btn">
            Cancelar
          </button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const userName = computed(() => user.value?.name || 'Docente');
const teacherSubject = computed(() => user.value?.subject || 'Sin asignatura asignada');

// Configuración del examen (sin subject, lo agregaremos después)
const examConfig = ref({
  name: '',
  difficulty: 'Media',
  questionCount: 10,
  timeLimit: 30,
  randomOrder: true,
  showResults: true
});

// Método de selección de preguntas
const selectionMethod = ref('auto');

// Filtros para selección manual
const questionFilter = ref({
  difficulty: '',
  search: ''
});

// Preguntas disponibles (simuladas)
const questions = ref([
  {
    id: 1,
    subject: 'Matemáticas',
    difficulty: 'Media',
    text: '¿Cuál es la solución de la ecuación 2x + 5 = 15?',
    options: ['x = 5', 'x = 10', 'x = 7.5', 'x = 6'],
    correctOption: 0
  },
  {
    id: 2,
    subject: 'Matemáticas',
    difficulty: 'Fácil',
    text: 'Si un triángulo tiene lados de 3, 4 y 5 unidades, ¿qué tipo de triángulo es?',
    options: ['Equilátero', 'Isósceles', 'Escaleno', 'Rectángulo'],
    correctOption: 3
  },
  {
    id: 3,
    subject: 'Matemáticas',
    difficulty: 'Difícil',
    text: '¿Cuál es el valor de la suma infinita 1/2 + 1/4 + 1/8 + 1/16 + ...?',
    options: ['1/2', '3/4', '1', '2'],
    correctOption: 2
  },
  {
    id: 4,
    subject: 'Lenguaje',
    difficulty: 'Media',
    text: '¿Qué figura literaria se utiliza cuando se atribuyen características humanas a objetos inanimados?',
    options: ['Metáfora', 'Personificación', 'Hipérbole', 'Símil'],
    correctOption: 1
  },
  {
    id: 5,
    subject: 'Ciencias',
    difficulty: 'Difícil',
    text: '¿Cuál de las siguientes NO es una función de las mitocondrias?',
    options: ['Producción de ATP', 'Respiración celular', 'Síntesis de proteínas', 'Ciclo de Krebs'],
    correctOption: 2
  }
]);

// Preguntas seleccionadas
const selectedQuestions = ref([]);

// Preguntas disponibles según los criterios seleccionados
const availableQuestions = computed(() => {
  return questions.value.filter(q => 
    q.subject === user.value.subject && 
    (examConfig.value.difficulty === 'Mixta' || q.difficulty === examConfig.value.difficulty)
  );
});

// Preguntas filtradas para selección manual
const filteredQuestions = computed(() => {
  let filtered = availableQuestions.value;
  
  // Filtrar por dificultad
  if (questionFilter.value.difficulty) {
    filtered = filtered.filter(q => q.difficulty === questionFilter.value.difficulty);
  }
  
  // Filtrar por búsqueda
  if (questionFilter.value.search) {
    const search = questionFilter.value.search.toLowerCase();
    filtered = filtered.filter(q => q.text.toLowerCase().includes(search));
  }
  
  // Excluir preguntas ya seleccionadas
  filtered = filtered.filter(q => !selectedQuestions.value.some(sq => sq.id === q.id));
  
  return filtered;
});

// Validación del formulario
const isFormValid = computed(() => {
  if (!examConfig.value.name) return false;
  
  if (selectionMethod.value === 'auto') {
    return availableQuestions.value.length >= examConfig.value.questionCount;
  } else {
    return selectedQuestions.value.length === examConfig.value.questionCount;
  }
});

// Verificar si una pregunta está seleccionada
const isQuestionSelected = (id) => {
  return selectedQuestions.value.some(q => q.id === id);
};

// Alternar selección de pregunta
const toggleQuestionSelection = (question) => {
  if (isQuestionSelected(question.id)) {
    removeQuestion(question);
  } else {
    if (selectedQuestions.value.length < examConfig.value.questionCount) {
      selectedQuestions.value.push(question);
    }
  }
};

// Eliminar pregunta de la selección
const removeQuestion = (question) => {
  selectedQuestions.value = selectedQuestions.value.filter(q => q.id !== question.id);
};

// Guardar ensayo
const saveExam = () => {
  // Preparar las preguntas del ensayo
  let examQuestions;
  
  if (selectionMethod.value === 'auto') {
    // Seleccionar aleatoriamente las preguntas
    const shuffled = [...availableQuestions.value].sort(() => 0.5 - Math.random());
    examQuestions = shuffled.slice(0, examConfig.value.questionCount);
  } else {
    examQuestions = [...selectedQuestions.value];
  }
  
  // Crear objeto de ensayo (agregando la asignatura del profesor)
  const exam = {
    id: Date.now(),
    ...examConfig.value,
    subject: user.value.subject,  // Asignatura del profesor
    questions: examQuestions,
    createdAt: new Date().toISOString(),
    createdBy: user.value.name
  };
  
  // Guardar en localStorage
  const exams = JSON.parse(localStorage.getItem('exams') || '[]');
  exams.push(exam);
  localStorage.setItem('exams', JSON.stringify(exams));
  
  // Redirigir al panel de docente
  alert('Ensayo creado con éxito');
  router.push('/teacher');
};

onMounted(() => {
  // Verificar si hay un usuario en localStorage
  const storedUser = localStorage.getItem('user');
  if (!storedUser) {
    router.push('/');
    return;
  }
  
  const userData = JSON.parse(storedUser);
  // Verificar si el usuario es docente
  if (userData.role !== 'teacher') {
    router.push('/');
    return;
  }
  
  // Verificar si el docente tiene una asignatura asignada
  if (!userData.subject) {
    alert('No tienes una asignatura asignada. Contacta al administrador.');
    logout();
    return;
  }
  
  user.value = userData;
  
  // Cargar preguntas del localStorage si existen
  const storedQuestions = localStorage.getItem('questions');
  if (storedQuestions) {
    // Filtrar solo las preguntas de la asignatura del profesor
    const allQuestions = JSON.parse(storedQuestions);
    questions.value = allQuestions.filter(q => q.subject === user.value.subject);
  }
});

const navigateTo = (route) => {
  router.push(route);
};

const logout = () => {
  localStorage.removeItem('user');
  router.push('/');
};
</script>

<style scoped>
/* CONTENEDOR GENERAL */
.create-exam {
  width: 100%;
  min-height: 100vh;
  background-color: #f5f5f5;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #222; /* Texto por defecto más oscuro */
}

/* ENCABEZADO */
.main-header {
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

/* CONTENIDO PRINCIPAL */
main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.actions-bar {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 1.5rem;
}

.back-btn {
  background-color: #95a5a6;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

/* FORMULARIO */
.exam-form {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  color: #222; /* Asegura alto contraste */
}

/* ELEMENTOS DE FORMULARIO */
.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #111;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group select {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  color: #111;
  background-color: #fff;
}

.help-text {
  margin-top: 0.5rem;
  font-size: 0.8rem;
  color: #7f8c8d;
}

/* CHECKBOX */
.checkbox-group {
  display: flex;
  align-items: center;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: normal;
  cursor: pointer;
}

/* ENCABEZADOS */
h3, h4 {
  margin: 2rem 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
  color: #222;
}

h4 {
  margin-top: 0;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #ddd;
}

/* SELECCIÓN DE PREGUNTAS */
.selection-method {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
}

.selection-method label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.auto-selection {
  background-color: #f9f9f9;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  color: #111;
}

.question-count {
  margin-top: 1rem;
  font-weight: bold;
  color: #2ecc71;
}

.no-questions {
  margin-top: 1rem;
  color: #e74c3c;
}

/* BOTÓN ACCIÓN */
.action-btn {
  display: inline-block;
  margin-top: 0.5rem;
  padding: 0.5rem 1rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

/* FILTRO Y BÚSQUEDA */
.filter-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  background-color: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
}

.filter-group, .search-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.search-group {
  flex-grow: 1;
}

.search-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  color: #111;
}

/* LISTA DE PREGUNTAS */
.questions-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.available-questions, .selected-questions {
  background-color: #f9f9f9;
  border-radius: 8px;
  padding: 1rem;
  height: 400px;
  overflow-y: auto;
  color: #111;
}

.questions-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.question-item {
  background-color: white;
  border-radius: 4px;
  padding: 0.8rem;
  border: 1px solid #ddd;
  cursor: pointer;
  transition: all 0.2s;
}

.question-item:hover {
  border-color: #3498db;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.question-item.selected {
  background-color: #e8f4fc;
  border-color: #3498db;
}

/* DETALLE DE PREGUNTA */
.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.question-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  background-color: #3498db;
  color: white;
  border-radius: 50%;
  font-size: 0.8rem;
}

.question-difficulty {
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.7rem;
  color: white;
}

.fácil {
  background-color: #2ecc71;
}

.media {
  background-color: #f39c12;
}

.difícil {
  background-color: #e74c3c;
}

.question-text {
  font-size: 1rem;
  margin: 0;
  color: #111;
}

.remove-btn {
  background: none;
  border: none;
  color: #e74c3c;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}

/* ACCIONES DEL FORMULARIO */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.save-btn, .cancel-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.save-btn {
  background-color: #2ecc71;
  color: white;
}

.save-btn:disabled {
  background-color: #95a5a6;
  cursor: not-allowed;
}

.cancel-btn {
  background-color: #e74c3c;
  color: white;
}
</style>
