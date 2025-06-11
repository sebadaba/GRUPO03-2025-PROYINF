<template>
  <div class="question-bank">
    <main>
      <!-- Barra de filtros -->
      <div class="filter-bar">
        <div class="filter-group">
          <label>Asignatura:</label>
          <select v-model="selectedSubject">
            <option value="">Todas</option>
            <option value="Matemáticas">Matemáticas</option>
            <option value="Lenguaje">Lenguaje</option>
            <option value="Ciencias">Ciencias</option>
            <option value="Historia">Historia</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label>Dificultad:</label>
          <select v-model="selectedDifficulty">
            <option value="">Todas</option>
            <option value="Fácil">Fácil</option>
            <option value="Media">Media</option>
            <option value="Difícil">Difícil</option>
          </select>
        </div>
        
        <div class="search-group">
          <input type="text" v-model="searchQuery" placeholder="Buscar pregunta...">
        </div>
      </div>
      
      <!-- Lista de preguntas -->
      <div class="questions-list">
        <div v-if="isLoading" class="loading">
          Cargando preguntas...
        </div>
        
        <div v-else-if="error" class="error">
          {{ error }}
        </div>
        
        <template v-else>
          <div v-for="question in filteredQuestions" :key="question.id" class="question-item">
            <div class="question-header">
              <span class="question-subject">{{ question.subject }}</span>
              <span class="question-difficulty" :class="question.difficulty.toLowerCase()">
                {{ question.difficulty }}
              </span>
            </div>
            
            <div class="question-content">
              <p>{{ question.text }}</p>
            </div>
            
            <div class="question-options">
              <div v-for="(option, index) in question.options" :key="index" 
                   class="option" 
                   :class="{ correct: index === question.correctOption }">
                <span class="option-letter">{{ ['A', 'B', 'C', 'D'][index] }}</span>
                <span class="option-text">{{ option }}</span>
              </div>
            </div>
            
            <div class="question-actions">
              <button @click="openEditModal(question)" class="edit-btn" :disabled="isLoading">
                Editar
              </button>
              <button @click="deleteQuestion(question.id)" class="delete-btn" :disabled="isLoading">
                Eliminar
              </button>
            </div>
          </div>
          
          <div v-if="filteredQuestions.length === 0" class="no-questions">
            No se encontraron preguntas con los filtros seleccionados.
          </div>
        </template>
      </div>
    </main>
    
    <!-- Modal para agregar/editar pregunta -->
    <div v-if="showQuestionModal" class="modal">
      <div class="modal-content">
        <h2>{{ isEditMode ? 'Editar Pregunta' : `Agregar Nueva Pregunta de ${teacherSubject}` }}</h2>
        
        <form @submit.prevent="saveQuestion">
          <!-- Asignatura (solo lectura) -->
          <div class="form-group">
            <label>Asignatura:</label>
            <input type="text" :value="teacherSubject" disabled class="disabled-input">
          </div>
          
          <div class="form-group">
            <label>Dificultad:</label>
            <select v-model="questionForm.difficulty" required>
              <option value="Fácil">Fácil</option>
              <option value="Media">Media</option>
              <option value="Difícil">Difícil</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Texto de la pregunta:</label>
            <textarea v-model="questionForm.text" rows="4" required></textarea>
          </div>
          
          <div class="form-group">
            <label>Opciones:</label>
            <div v-for="(_, index) in 4" :key="index" class="option-input">
              <span class="option-letter">{{ ['A', 'B', 'C', 'D'][index] }}</span>
              <input 
                type="text" 
                v-model="questionForm.options[index]" 
                :placeholder="`Opción ${['A', 'B', 'C', 'D'][index]}`"
                required
              >
              <input 
                type="radio" 
                :value="index" 
                v-model="questionForm.correctOption"
                :id="'option-' + index"
                required
              >
              <label :for="'option-' + index">Correcta</label>
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="submit" class="save-btn" :disabled="isLoading">
              {{ isLoading ? 'Guardando...' : (isEditMode ? 'Actualizar' : 'Guardar') }}
            </button>
            <button type="button" @click="closeModal" class="cancel-btn" :disabled="isLoading">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineExpose } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const userName = computed(() => user.value?.name || 'Docente');
const teacherSubject = computed(() => user.value?.subject || 'Sin asignatura asignada');

// Estados del componente
const isLoading = ref(false);
const error = ref(null);

// Filtros
const selectedSubject = ref('');
const selectedDifficulty = ref('');
const searchQuery = ref('');

// Modal y formulario
const showQuestionModal = ref(false);
const isEditMode = ref(false);
const editingQuestionId = ref(null);

// Datos
const questions = ref([]);

// Formulario de pregunta
const questionForm = ref({
  difficulty: 'Media',
  text: '',
  options: ['', '', '', ''],
  correctOption: 0
});

// Preguntas de ejemplo
const SAMPLE_QUESTIONS = [
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
    subject: 'Lenguaje',
    difficulty: 'Fácil',
    text: '¿Qué figura literaria se utiliza cuando se atribuyen características humanas a objetos inanimados?',
    options: ['Metáfora', 'Personificación', 'Hipérbole', 'Símil'],
    correctOption: 1
  },
  {
    id: 3,
    subject: 'Ciencias',
    difficulty: 'Difícil',
    text: '¿Cuál de las siguientes NO es una función de las mitocondrias?',
    options: ['Producción de ATP', 'Respiración celular', 'Síntesis de proteínas', 'Ciclo de Krebs'],
    correctOption: 2
  }
];

// Computed
const filteredQuestions = computed(() => {
  return questions.value.filter(question => {
    const matchesSubject = selectedSubject.value ? question.subject === selectedSubject.value : true;
    const matchesDifficulty = selectedDifficulty.value ? question.difficulty === selectedDifficulty.value : true;
    const matchesSearch = searchQuery.value 
      ? question.text.toLowerCase().includes(searchQuery.value.toLowerCase()) 
      : true;
    
    return matchesSubject && matchesDifficulty && matchesSearch;
  });
});

// Funciones utilitarias
const resetForm = () => {
  questionForm.value = {
    difficulty: 'Media',
    text: '',
    options: ['', '', '', ''],
    correctOption: 0
  };
};

const validateForm = () => {
  if (!questionForm.value.text.trim()) {
    alert('Por favor ingresa el texto de la pregunta');
    return false;
  }
  
  if (questionForm.value.options.some(option => !option.trim())) {
    alert('Por favor completa todas las opciones');
    return false;
  }
  
  return true;
};

const updateLocalStorage = (updatedQuestions) => {
  try {
    localStorage.setItem('questions', JSON.stringify(updatedQuestions));
  } catch (err) {
    console.error('Error al guardar en localStorage:', err);
    throw new Error('Error al guardar los datos');
  }
};

// Funciones de datos
const loadQuestions = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    const storedQuestions = localStorage.getItem('questions');
    let allQuestions = storedQuestions ? JSON.parse(storedQuestions) : SAMPLE_QUESTIONS;
    
    // Si no hay preguntas guardadas, usar las de ejemplo
    if (!storedQuestions) {
      updateLocalStorage(allQuestions);
    }
    
    // Filtrar por asignatura del profesor si está definida
    if (user.value?.subject) {
      questions.value = allQuestions.filter(q => q.subject === user.value.subject);
    } else {
      questions.value = allQuestions;
    }
  } catch (err) {
    console.error('Error al cargar preguntas:', err);
    error.value = 'Error al cargar las preguntas. Por favor, intenta nuevamente.';
  } finally {
    isLoading.value = false;
  }
};

const saveQuestion = async () => {
  if (!validateForm()) return;
  
  isLoading.value = true;
  
  try {
    const storedQuestions = localStorage.getItem('questions');
    const allQuestions = storedQuestions ? JSON.parse(storedQuestions) : [];
    
    if (isEditMode.value) {
      // Actualizar pregunta existente
      const questionIndex = allQuestions.findIndex(q => q.id === editingQuestionId.value);
      if (questionIndex !== -1) {
        const updatedQuestion = {
          ...allQuestions[questionIndex],
          ...questionForm.value,
          subject: user.value.subject
        };
        
        allQuestions[questionIndex] = updatedQuestion;
        
        // Actualizar en la lista local
        const localIndex = questions.value.findIndex(q => q.id === editingQuestionId.value);
        if (localIndex !== -1) {
          questions.value[localIndex] = updatedQuestion;
        }
      }
    } else {
      // Crear nueva pregunta
      const newQuestion = {
        ...questionForm.value,
        subject: user.value.subject,
        id: Date.now()
      };
      
      allQuestions.push(newQuestion);
      
      // Agregar a la lista local si coincide con la asignatura
      if (!user.value.subject || newQuestion.subject === user.value.subject) {
        questions.value.push(newQuestion);
      }
    }
    
    updateLocalStorage(allQuestions);
    closeModal();
    
  } catch (err) {
    console.error('Error al guardar pregunta:', err);
    alert('Error al guardar la pregunta. Por favor, intenta nuevamente.');
  } finally {
    isLoading.value = false;
  }
};

const deleteQuestion = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
    return;
  }
  
  isLoading.value = true;
  
  try {
    const storedQuestions = localStorage.getItem('questions');
    if (storedQuestions) {
      const allQuestions = JSON.parse(storedQuestions);
      const updatedQuestions = allQuestions.filter(q => q.id !== id);
      
      updateLocalStorage(updatedQuestions);
      questions.value = questions.value.filter(q => q.id !== id);
    }
  } catch (err) {
    console.error('Error al eliminar pregunta:', err);
    alert('Error al eliminar la pregunta. Por favor, intenta nuevamente.');
  } finally {
    isLoading.value = false;
  }
};

// Funciones del modal
const openAddModal = () => {
  isEditMode.value = false;
  editingQuestionId.value = null;
  resetForm();
  showQuestionModal.value = true;
};

const openEditModal = (question) => {
  isEditMode.value = true;
  editingQuestionId.value = question.id;
  
  questionForm.value = {
    difficulty: question.difficulty,
    text: question.text,
    options: [...question.options],
    correctOption: question.correctOption
  };
  
  showQuestionModal.value = true;
};

const closeModal = () => {
  showQuestionModal.value = false;
  isEditMode.value = false;
  editingQuestionId.value = null;
  resetForm();
};

// Función expuesta para el componente padre
const openAddQuestionForm = () => {
  openAddModal();
};

// Ciclo de vida
onMounted(() => {
  const storedUser = localStorage.getItem('user');
  if (!storedUser) {
    router.push('/');
    return;
  }
  
  const userData = JSON.parse(storedUser);
  if (userData.role !== 'teacher') {
    router.push('/');
    return;
  }
  
  user.value = userData;
  loadQuestions();
});

// Exposer funciones para el componente padre
defineExpose({ openAddQuestionForm });
</script>

<style scoped>
.question-bank {
  width: 100%;
  min-height: 100vh;
  background-color: #f5f5f5;
  box-sizing: border-box;
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* Barra de filtros */
.filter-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  background-color: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  flex-wrap: wrap;
}

.filter-group, .search-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  min-width: 150px;
}

.search-group {
  flex: 1;
  min-width: 200px;
}

.filter-group label {
  font-weight: 500;
  font-size: 0.9rem;
  color: #555;
}

.filter-group select, .search-group input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
}

/* Estados de carga y error */
.loading, .error {
  text-align: center;
  padding: 2rem;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.loading {
  color: #3498db;
}

.error {
  color: #e74c3c;
}

/* Lista de preguntas */
.questions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.question-item {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
}

.question-item:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.question-subject {
  font-weight: bold;
  color: #3498db;
  font-size: 1rem;
}

.question-difficulty {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
  white-space: nowrap;
}

.fácil {
  background-color: #2ecc71;
  color: white;
}

.media {
  background-color: #f39c12;
  color: white;
}

.difícil {
  background-color: #e74c3c;
  color: white;
}

.question-content {
  margin-bottom: 1.5rem;
}

.question-content p {
  font-size: 1rem;
  line-height: 1.5;
  margin: 0;
}

.question-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.option {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.option:hover {
  background-color: #f8f9fa;
}

.option.correct {
  background-color: rgba(46, 204, 113, 0.1);
  border-color: #2ecc71;
}

.option-letter {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background-color: #3498db;
  color: white;
  border-radius: 50%;
  margin-right: 0.75rem;
  font-weight: bold;
  font-size: 0.9rem;
  flex-shrink: 0;
}

.option-text {
  flex: 1;
  line-height: 1.4;
}

.question-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.edit-btn, .delete-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.edit-btn:disabled, .delete-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.edit-btn {
  background-color: #3498db;
  color: white;
}

.edit-btn:hover:not(:disabled) {
  background-color: #2980b9;
}

.delete-btn {
  background-color: #e74c3c;
  color: white;
}

.delete-btn:hover:not(:disabled) {
  background-color: #c0392b;
}

.no-questions {
  text-align: center;
  padding: 3rem;
  background-color: white;
  border-radius: 8px;
  color: #7f8c8d;
  font-size: 1.1rem;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  padding: 2rem;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-content h2 {
  margin: 0 0 1.5rem 0;
  color: #333;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555;
}

.form-group select, 
.form-group textarea, 
.form-group input[type="text"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  box-sizing: border-box;
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
}

.option-input {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  gap: 0.5rem;
}

.option-input .option-letter {
  flex-shrink: 0;
}

.option-input input[type="text"] {
  flex: 1;
  margin: 0;
}

.option-input input[type="radio"] {
  margin: 0;
}

.option-input label {
  margin: 0;
  font-size: 0.9rem;
  white-space: nowrap;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.save-btn, .cancel-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.save-btn:disabled, .cancel-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.save-btn {
  background-color: #2ecc71;
  color: white;
}

.save-btn:hover:not(:disabled) {
  background-color: #27ae60;
}

.cancel-btn {
  background-color: #95a5a6;
  color: white;
}

.cancel-btn:hover:not(:disabled) {
  background-color: #7f8c8d;
}

.disabled-input {
  background-color: #f5f5f5;
  color: #666;
  cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 1024px) {
  main {
    padding: 1.5rem;
  }
  
  .question-options {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  main {
    padding: 1rem;
  }
  
  .filter-bar {
    flex-direction: column;
    gap: 1rem;
  }
  
  .filter-group, .search-group {
    min-width: auto;
  }
  
  .question-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .question-actions {
    justify-content: center;
  }
  
  .modal {
    padding: 0.5rem;
  }
  
  .modal-content {
    padding: 1.5rem;
  }
  
  .option-input {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  main {
    padding: 0.75rem;
  }
  
  .question-item {
    padding: 1rem;
  }
  
  .modal-content {
    padding: 1rem;
  }
}
</style>