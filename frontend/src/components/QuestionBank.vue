<template>
  <div class="question-bank">
    <main>
      <div class="filter-bar">
        <FilterSelect
          v-model="selectedSubject"
          label="Asignatura:"
          placeholder="Todas"
          :options="[
            { value: 'Matemáticas', label: 'Matemáticas' },
            { value: 'Lenguaje', label: 'Lenguaje' },
            { value: 'Ciencias', label: 'Ciencias' },
            { value: 'Historia', label: 'Historia' }
          ]"
        />

        <FilterSelect
          v-model="selectedDifficulty"
          label="Dificultad:"
          placeholder="Todas"
          :options="[
            { value: 'Fácil', label: 'Fácil' },
            { value: 'Media', label: 'Media' },
            { value: 'Difícil', label: 'Difícil' }
          ]"
        />

        <SearchBar
          v-model="searchQuery"
          placeholder="Buscar pregunta..."
        />
      </div>

      <!-- Lista de preguntas usando QuestionDisplay -->
      <QuestionDisplay
        :questions="filteredQuestions"
        :show-correct-answers="true"
        :disabled="isLoading"
        :start-index="0"
        empty-message="No se encontraron preguntas con los filtros seleccionados."
      >
        <template #actions="{ question }">
          <button @click="openEditModal(question)" class="edit-btn">Editar</button>
          <button @click="deleteQuestion(question.id)" class="delete-btn">Eliminar</button>
        </template>
      </QuestionDisplay>

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
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineExpose } from 'vue';
import { useRouter } from 'vue-router';
import QuestionDisplay from './QuestionDisplay.vue';
import FilterSelect from './common/FilterSelect.vue'
import SearchBar from './common/SearchBar.vue'

const router = useRouter();
const user = ref(null);
const teacherSubject = computed(() => user.value?.subject || 'Sin asignatura asignada');

// Estados del componente
const isLoading = ref(false);

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
  background: #ffffff;
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.filter-bar {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  padding: 1.5rem;
  border-radius: 16px;
  box-shadow:
    0 8px 32px rgba(0, 0, 0, 0.1),
    0 2px 16px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
  flex-wrap: wrap;
  align-items: end;
}

.question-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.edit-btn,
.delete-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.edit-btn:disabled,
.delete-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.edit-btn {
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  color: white;
}

.edit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(14, 165, 233, 0.3);
}

.delete-btn {
  background: linear-gradient(45deg, #ef4444, #f87171);
  color: white;
}

.delete-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(239, 68, 68, 0.3);
}


.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.modal-content {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 2.5rem;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-content h2 {
  margin: 0 0 2rem 0;
  color: #1f2937;
  font-size: 1.5rem;
  font-weight: 700;
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.75rem;
  font-weight: 600;
  color: #374151;
  font-size: 0.95rem;
}

.form-group select,
.form-group textarea,
.form-group input[type="text"] {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.95rem;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(4px);
  box-sizing: border-box;
  transition: all 0.3s ease;
  outline: none;
}

.form-group select:focus,
.form-group textarea:focus,
.form-group input[type="text"]:focus {
  border-color: #0ea5e9;
  background: white;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
}

.option-input {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
  gap: 0.75rem;
  padding: 0.75rem;
  background: rgba(248, 250, 252, 0.5);
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.option-input .option-letter {
  flex-shrink: 0;
  width: 28px;
  height: 28px;
  font-size: 0.875rem;
}

.option-input input[type="text"] {
  flex: 1;
  margin: 0;
  border: 1px solid #d1d5db;
  padding: 0.5rem 0.75rem;
}

.option-input input[type="radio"] {
  margin: 0;
  width: 18px;
  height: 18px;

}

.option-input label {
  margin: 0;
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
  color: #6b7280;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(229, 231, 235, 0.5);
}

.save-btn,
.cancel-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.save-btn:disabled,
.cancel-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

.save-btn {
  background: linear-gradient(45deg, #10b981, #34d399);
  color: white;
}

.save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
}

.cancel-btn {
  background: linear-gradient(45deg, #6b7280, #9ca3af);
  color: white;
}

.cancel-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(107, 114, 128, 0.3);
}

.disabled-input {
  background-color: #f3f4f6 !important;
  color: #6b7280 !important;
  cursor: not-allowed !important;
}

/* Responsive Design */
@media (max-width: 1024px) {
  main {
    padding: 1.5rem;
  }

}

@media (max-width: 768px) {
  main {
    padding: 1rem;
  }

  .filter-bar {
    flex-direction: column;
    gap: 1rem;
    padding: 1.25rem;
  }

  .question-actions {
    justify-content: center;
    width: 100%;
  }

  .modal {
    padding: 0.5rem;
  }

  .modal-content {
    padding: 2rem;
    border-radius: 16px;
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

  .modal-content {
    padding: 1.5rem;
  }

  .filter-bar {
    padding: 1rem;
    border-radius: 12px;
  }
}
</style>