<template>
  <div class="teacher-dashboard">
    <header class="main-header">
      <div class="header-content">
        <h1>Panel de Docente - Ensayo PAES</h1>
        <div class="user-info">
          <span>{{ userName }} - Profesor de {{ teacherSubject }}</span>
          <button @click="logout" class="logout-btn">Cerrar Sesión</button>
        </div>
      </div>
    </header>
    
    <main>
      <div class="dashboard-tabs">
        <button 
          @click="activeTab = 'questions'" 
          :class="{ active: activeTab === 'questions' }"
        >
          Banco de Preguntas
        </button>
        <button 
          @click="activeTab = 'create'" 
          :class="{ active: activeTab === 'create' }"
        >
          Crear Pregunta
        </button>
        <button 
          @click="activeTab = 'tests'" 
          :class="{ active: activeTab === 'tests' }"
        >
          Ensayos
        </button>
      </div>
      
      <!-- Banco de Preguntas -->
      <div v-if="activeTab === 'questions'" class="tab-content">
        <h2>Banco de Preguntas</h2>
        
        <div class="filter-controls">
          <select v-model="selectedSubject">
            <option value="">Todas las asignaturas</option>
            <option value="math">Matemáticas</option>
            <option value="language">Lenguaje</option>
            <option value="science">Ciencias</option>
            <option value="history">Historia</option>
          </select>
          
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Buscar preguntas..." 
            class="search-input"
          />
        </div>
        
        <div class="questions-list">
          <div v-for="question in filteredQuestions" :key="question.id" class="question-item">
            <div class="question-header">
              <span class="subject-tag" :class="question.subject">
                {{ getSubjectName(question.subject) }}
              </span>
              <div class="question-actions">
                <button @click="editQuestion(question)" class="action-btn edit">Editar</button>
                <button @click="deleteQuestion(question.id)" class="action-btn delete">Eliminar</button>
              </div>
            </div>
            <p class="question-text">{{ question.text }}</p>
            <div class="alternatives">
              <div 
                v-for="(alt, index) in question.alternatives" 
                :key="index"
                class="alternative"
                :class="{ correct: index === question.correctAnswer }"
              >
                {{ index + 1 }}. {{ alt }}
                <span v-if="index === question.correctAnswer" class="correct-badge">Correcta</span>
              </div>
            </div>
          </div>
          
          <div v-if="filteredQuestions.length === 0" class="empty-state">
            No se encontraron preguntas. ¡Crea una nueva!
          </div>
        </div>
      </div>
      
      <!-- Crear Pregunta -->
      <div v-if="activeTab === 'create'" class="tab-content">
        <h2>Crear Nueva Pregunta</h2>
        
        <form @submit.prevent="saveQuestion" class="question-form">
          <div class="form-group">
            <label>Asignatura</label>
            <select v-model="newQuestion.subject" required>
              <option value="math">Matemáticas</option>
              <option value="language">Lenguaje</option>
              <option value="science">Ciencias</option>
              <option value="history">Historia</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Enunciado de la pregunta</label>
            <textarea 
              v-model="newQuestion.text" 
              rows="4" 
              required
              placeholder="Escribe el enunciado de la pregunta..."
            ></textarea>
          </div>
          
          <div class="form-group">
            <label>Alternativas</label>
            <div 
              v-for="(alt, index) in newQuestion.alternatives" 
              :key="index"
              class="alternative-input"
            >
              <div class="alternative-header">
                <span>Alternativa {{ index + 1 }}</span>
                <label class="correct-label">
                  <input 
                    type="radio" 
                    :value="index" 
                    v-model="newQuestion.correctAnswer"
                    name="correctAnswer" 
                  />
                  Correcta
                </label>
              </div>
              <textarea 
                v-model="newQuestion.alternatives[index]" 
                rows="2" 
                required
                :placeholder="`Texto de la alternativa ${index + 1}...`"
              ></textarea>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="resetForm" class="secondary-btn">Cancelar</button>
            <button type="submit" class="primary-btn">Guardar Pregunta</button>
          </div>
        </form>
      </div>
      
      <!-- Ensayos -->
      <div v-if="activeTab === 'tests'" class="tab-content">
        <h2>Gestión de Ensayos</h2>
        
        <p class="coming-soon">
          Funcionalidad en desarrollo. Próximamente podrás crear ensayos personalizados
          seleccionando preguntas del banco de preguntas.
        </p>
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
const activeTab = ref('questions');
const selectedSubject = ref('');
const searchQuery = ref('');

// Datos de ejemplo para las preguntas
const questions = ref([
  {
    id: 1,
    subject: 'math',
    text: '¿Cuál es el valor de x en la ecuación 2x + 5 = 15?',
    alternatives: [
      'x = 5',
      'x = 10',
      'x = 7',
      'x = 3'
    ],
    correctAnswer: 0
  },
  {
    id: 2,
    subject: 'language',
    text: 'Identifica el sujeto en la siguiente oración: "Los estudiantes presentaron un excelente proyecto".',
    alternatives: [
      'Los estudiantes',
      'presentaron',
      'un excelente proyecto',
      'excelente'
    ],
    correctAnswer: 0
  },
  {
    id: 3,
    subject: 'science',
    text: '¿Cuál de las siguientes NO es una parte de la célula eucariota?',
    alternatives: [
      'Núcleo',
      'Ribosomas',
      'Flagelo',
      'Plásmido'
    ],
    correctAnswer: 3
  }
]);

// Nueva pregunta
const newQuestion = ref({
  subject: 'math',
  text: '',
  alternatives: ['', '', '', ''],
  correctAnswer: null
});

// Preguntas filtradas
const filteredQuestions = computed(() => {
  let result = [...questions.value];
  
  if (selectedSubject.value) {
    result = result.filter(q => q.subject === selectedSubject.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(q => 
      q.text.toLowerCase().includes(query) || 
      q.alternatives.some(alt => alt.toLowerCase().includes(query))
    );
  }
  
  return result;
});

// Obtener nombre de la asignatura
const getSubjectName = (subject) => {
  const subjects = {
    math: 'Matemáticas',
    language: 'Lenguaje',
    science: 'Ciencias',
    history: 'Historia'
  };
  return subjects[subject] || subject;
};

onMounted(() => {
  // Verificar si hay un usuario en localStorage
  const storedUser = localStorage.getItem('user');
  if (!storedUser) {
    router.push('/');
    return;
  }
  
  user.value = JSON.parse(storedUser);
  
  // Verificar si el usuario es docente
  if (user.value.role !== 'teacher') {
    router.push('/home');
    return;
  }
  
  // Verificar si el docente tiene una asignatura asignada
  if (!user.value.subject) {
    alert('No tienes una asignatura asignada. Contacta al administrador.');
    logout();
    return;
  }
  
  // Cargar preguntas del localStorage si existen
  loadQuestions();
});

// Función para cargar preguntas
const loadQuestions = () => {
  const storedQuestions = localStorage.getItem('questions');
  if (storedQuestions) {
    const allQuestions = JSON.parse(storedQuestions);
    // Filtrar solo las preguntas de la asignatura del profesor
    questions.value = allQuestions.filter(q => q.subject === user.value.subject);
  }
};

// Guardar pregunta
const saveQuestion = () => {
  // Validar que todos los campos estén completos
  if (!newQuestion.value.text || 
      newQuestion.value.alternatives.some(alt => !alt) || 
      newQuestion.value.correctAnswer === null) {
    alert('Por favor completa todos los campos');
    return;
  }
  
  // Si estamos editando, actualizar la pregunta existente
  if (newQuestion.value.id) {
    const index = questions.value.findIndex(q => q.id === newQuestion.value.id);
    if (index !== -1) {
      questions.value[index] = { ...newQuestion.value };
    }
  } else {
    // Si es una nueva pregunta, agregar al array
    const newId = questions.value.length > 0 
      ? Math.max(...questions.value.map(q => q.id)) + 1 
      : 1;
    
    questions.value.push({
      ...newQuestion.value,
      id: newId
    });
  }
  
  // Guardar en localStorage
  localStorage.setItem('questions', JSON.stringify(questions.value));
  
  // Resetear el formulario
  resetForm();
  
  // Cambiar a la pestaña de preguntas
  activeTab.value = 'questions';
};

// Editar pregunta
const editQuestion = (question) => {
  newQuestion.value = { ...question };
  activeTab.value = 'create';
};

// Eliminar pregunta
const deleteQuestion = (id) => {
  if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
    questions.value = questions.value.filter(q => q.id !== id);
    localStorage.setItem('questions', JSON.stringify(questions.value));
  }
};

// Resetear formulario
const resetForm = () => {
  newQuestion.value = {
    subject: 'math',
    text: '',
    alternatives: ['', '', '', ''],
    correctAnswer: null
  };
};

// Cerrar sesión
const logout = () => {
  localStorage.removeItem('user');
  router.push('/');
};
</script>

<style scoped>
.teacher-dashboard {
  width: 100%;
  min-height: 100vh;
  background-color: #f5f5f5;
}

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

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.dashboard-tabs {
  display: flex;
  margin-bottom: 2rem;
  border-bottom: 1px solid #ddd;
}

.dashboard-tabs button {
  padding: 1rem 2rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  color: #666;
  border-bottom: 3px solid transparent;
  transition: all 0.3s;
}

.dashboard-tabs button:hover {
  color: #3498db;
}

.dashboard-tabs button.active {
  color: #3498db;
  border-bottom-color: #3498db;
}

.tab-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.filter-controls {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filter-controls select,
.filter-controls .search-input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.search-input {
  flex-grow: 1;
}

.questions-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.question-item {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1.5rem;
  background: #f9f9f9;
}

.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.subject-tag {
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  color: white;
}

.subject-tag.math {
  background-color: #3498db;
}

.subject-tag.language {
  background-color: #e74c3c;
}

.subject-tag.science {
  background-color: #2ecc71;
}

.subject-tag.history {
  background-color: #f39c12;
}

.question-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  padding: 0.3rem 0.8rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
}

.action-btn.edit {
  background-color: #3498db;
  color: white;
}

.action-btn.delete {
  background-color: #e74c3c;
  color: white;
}

.question-text {
  font-size: 1.1rem;
  margin-bottom: 1rem;
}

.alternatives {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.alternative {
  padding: 0.8rem;
  border-radius: 4px;
  background: white;
  border: 1px solid #ddd;
  position: relative;
}

.alternative.correct {
  border-color: #2ecc71;
  background-color: rgba(46, 204, 113, 0.1);
}

.correct-badge {
  position: absolute;
  right: 0.8rem;
  top: 50%;
  transform: translateY(-50%);
  background-color: #2ecc71;
  color: white;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
}

.empty-state {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
}

.question-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: bold;
  color: #333;
}

.form-group select,
.form-group textarea {
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
  font-size: 1rem;
}

.alternative-input {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f9f9f9;
}

.alternative-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.correct-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: normal;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.primary-btn,
.secondary-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.primary-btn {
  background-color: #3498db;
  color: white;
}

.secondary-btn {
  background-color: #95a5a6;
  color: white;
}

.coming-soon {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
  font-style: italic;
}
</style>