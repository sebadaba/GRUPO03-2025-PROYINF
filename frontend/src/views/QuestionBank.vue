<template>
  <div class="question-bank">
    <header class="main-header">
      <div class="header-content">
        <h1>Banco de Preguntas - Ensayo PAES</h1>
        <div class="user-info">
          <span>{{ userName }}</span>
          <button @click="logout" class="logout-btn">Cerrar Sesión</button>
        </div>
      </div>
    </header>
    
    <main>
      <div class="actions-bar">
        <button @click="showAddQuestionForm = true" class="add-btn">Agregar Pregunta</button>
        <button @click="navigateTo('/teacher')" class="back-btn">Volver al Panel</button>
      </div>
      
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
      
      <div class="questions-list">
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
            <button @click="editQuestion(question)" class="edit-btn">Editar</button>
            <button @click="deleteQuestion(question.id)" class="delete-btn">Eliminar</button>
          </div>
        </div>
        
        <div v-if="filteredQuestions.length === 0" class="no-questions">
          No se encontraron preguntas con los filtros seleccionados.
        </div>
      </div>
    </main>
    

    <div v-if="showAddQuestionForm" class="modal">
      <div class="modal-content">
        <h2>Agregar Nueva Pregunta de {{ teacherSubject }}</h2>
        
        <form @submit.prevent="addQuestion">
          <!-- Asignatura fija (no se puede cambiar) -->
          <div class="form-group">
            <label>Asignatura:</label>
            <input type="text" :value="teacherSubject" disabled class="disabled-input">
          </div>
          
          <div class="form-group">
            <label>Dificultad:</label>
            <select v-model="newQuestion.difficulty">
              <option value="Fácil">Fácil</option>
              <option value="Media">Media</option>
              <option value="Difícil">Difícil</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Texto de la pregunta:</label>
            <textarea v-model="newQuestion.text" rows="4"></textarea>
          </div>
          
          <div class="form-group">
            <label>Opciones:</label>
            <div v-for="(_, index) in 4" :key="index" class="option-input">
              <span class="option-letter">{{ ['A', 'B', 'C', 'D'][index] }}</span>
              <input type="text" v-model="newQuestion.options[index]">
              <input 
                type="radio" 
                :value="index" 
                v-model="newQuestion.correctOption"
                :id="'option-' + index"
              >
              <label :for="'option-' + index">Correcta</label>
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="submit" class="save-btn">Guardar</button>
            <button type="button" @click="showAddQuestionForm = false" class="cancel-btn">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);
const userName = computed(() => user.value?.name || 'Docente');
const teacherSubject = computed(() => user.value?.subject || 'Sin asignatura asignada');

// Filtros
const selectedSubject = ref('');
const selectedDifficulty = ref('');
const searchQuery = ref('');

// Modal
const showAddQuestionForm = ref(false);
const isLoading = ref(false);
const error = ref(null);

// Preguntas
const questions = ref([]);

// Nueva pregunta (con asignatura predefinida)
const newQuestion = ref({
  difficulty: 'Media',
  text: '',
  options: ['', '', '', ''],
  correctOption: 0
  // No incluimos subject aquí, lo agregaremos al guardar
});

// Preguntas filtradas
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

// Cargar preguntas
const loadQuestions = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    // En modo desarrollo, usamos localStorage
    const storedQuestions = localStorage.getItem('questions');
    if (storedQuestions) {
      const allQuestions = JSON.parse(storedQuestions);
      // Filtrar solo las preguntas de la asignatura del profesor
      if (user.value && user.value.subject) {
        questions.value = allQuestions.filter(q => q.subject === user.value.subject);
      } else {
        questions.value = allQuestions;
      }
    } else {
      questions.value = [
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
      localStorage.setItem('questions', JSON.stringify(questions.value));
    }
  } catch (err) {
    console.error('Error al cargar preguntas:', err);
    error.value = 'Error al cargar las preguntas. Por favor, intenta nuevamente.';
  } finally {
    isLoading.value = false;
  }
};

// Función para agregar pregunta
const addQuestion = async () => {
  // Validar que todos los campos estén completos
  if (!newQuestion.value.text || 
      newQuestion.value.options.some(option => !option)) {
    alert('Por favor completa todos los campos');
    return;
  }
  
  isLoading.value = true;
  
  try {
    // Agregar la asignatura del profesor a la pregunta
    const questionToAdd = {
      ...newQuestion.value,
      subject: user.value.subject,
      id: Date.now()
    };
    
    // Guardar en localStorage
    const storedQuestions = localStorage.getItem('questions');
    const allQuestions = storedQuestions ? JSON.parse(storedQuestions) : [];
    allQuestions.push(questionToAdd);
    localStorage.setItem('questions', JSON.stringify(allQuestions));
    
    // Actualizar la lista local
    if (!user.value.subject || questionToAdd.subject === user.value.subject) {
      questions.value.push(questionToAdd);
    }
    
    // Reiniciar el formulario
    resetForm();
    
    // Cerrar el modal
    showAddQuestionForm.value = false;
  } catch (err) {
    console.error('Error al agregar pregunta:', err);
    alert('Error al guardar la pregunta. Por favor, intenta nuevamente.');
  } finally {
    isLoading.value = false;
  }
};

// Función para editar pregunta (implementación básica)
const editQuestion = async (question) => {
  // Implementación futura
  alert('La funcionalidad de edición estará disponible próximamente');
  console.log('Editar pregunta:', question);
};


const deleteQuestion = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
    return;
  }
  
  isLoading.value = true;
  error.value = null;
  
  try {
    // Actualizar localStorage
    const storedQuestions = localStorage.getItem('questions');
    if (storedQuestions) {
      const allQuestions = JSON.parse(storedQuestions);
      const updatedQuestions = allQuestions.filter(q => q.id !== id);
      localStorage.setItem('questions', JSON.stringify(updatedQuestions));
      
      // Actualizar la lista local
      questions.value = questions.value.filter(q => q.id !== id);
    }
  } catch (err) {
    console.error('Error al eliminar pregunta:', err);
    error.value = 'Error al eliminar la pregunta. Por favor, intenta nuevamente.';
    alert('Error al eliminar la pregunta. Por favor, intenta nuevamente.');
  } finally {
    isLoading.value = false;
  }
};

// Resetear formulario
const resetForm = () => {
  newQuestion.value = {
    difficulty: 'Media',
    text: '',
    options: ['', '', '', ''],
    correctOption: 0
  };
};

// Navegar a otra ruta
const navigateTo = (route) => {
  router.push(route);
};

// Cerrar sesión
const logout = () => {
  localStorage.removeItem('user');
  router.push('/');
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
  
  user.value = userData;
  
  // Cargar preguntas
  loadQuestions();
});
</script>

<style scoped>
.question-bank {
  width: 100%;
  min-height: 100vh;
  background-color: #f8f9fa;
}

/* ======= ENCABEZADO ======= */
.main-header {
  background-color: #2ecc71;
  color: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1200px;
  margin: 0 auto;
  font-weight: 500;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Botón logout */
.logout-btn {
  background-color: transparent;
  border: 1px solid white;
  color: white;
  padding: 0.4rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: 500;
}

.logout-btn:hover {
  background-color: rgba(255, 255, 255, 0.15);
}

/* ======= CONTENIDO PRINCIPAL ======= */
main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}

/* Barra de acciones */
.actions-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.add-btn, .back-btn {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.add-btn {
  background-color: #2ecc71;
}

.add-btn:hover {
  background-color: #27ae60;
}

.back-btn {
  background-color: #95a5a6;
}

.back-btn:hover {
  background-color: #7f8c8d;
}

/* Barra de filtros */
.filter-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  background-color: #ffffff;
  padding: 1.2rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  flex-wrap: wrap;
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
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* Lista de preguntas */
.questions-list {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

/* Tarjeta de pregunta */
.question-item {
  background-color: #fff;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

/* Encabezado de pregunta */
.question-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  font-size: 0.95rem;
}

.question-subject {
  font-weight: 600;
  color: #3498db;
}

.question-difficulty {
  padding: 0.4rem 0.8rem;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Niveles de dificultad */
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

/* Contenido de pregunta */
.question-content {
  margin-bottom: 1rem;
  font-size: 1rem;
  color: #333;
}

/* Opciones de respuesta */
.question-options {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.option {
  display: flex;
  align-items: center;
  padding: 0.6rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fafafa;
  font-size: 0.95rem;
  transition: background-color 0.2s;
}

.option:hover {
  background-color: #f0f8ff;
}

.option.correct {
  background-color: rgba(46, 204, 113, 0.1);
  border-color: #2ecc71;
}

.option-letter {
  width: 28px;
  height: 28px;
  background-color: #3498db;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 0.6rem;
}

/* Acciones por pregunta */
.question-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.edit-btn, .delete-btn {
  padding: 0.4rem 0.8rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
}

.edit-btn {
  background-color: #3498db;
  color: white;
}

.delete-btn {
  background-color: #e74c3c;
  color: white;
}

/* Mensaje si no hay preguntas */
.no-questions {
  text-align: center;
  padding: 3rem;
  background-color: white;
  border-radius: 12px;
  color: #7f8c8d;
  font-size: 1.1rem;
}

/* ========== MODAL ========== */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-content {
  background-color: white;
  border-radius: 16px;
  padding: 2rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

/* Formulario */
.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.form-group select,
.form-group textarea,
.form-group input[type="text"] {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
}

/* Entrada de opciones */
.option-input {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  gap: 0.5rem;
}

.option-input input[type="text"] {
  flex: 1;
}

/* Acciones del modal */
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.save-btn, .cancel-btn {
  padding: 0.5rem 1.2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.save-btn {
  background-color: #2ecc71;
  color: white;
}

.cancel-btn {
  background-color: #95a5a6;
  color: white;
}

/* Campo deshabilitado */
.disabled-input {
  background-color: #eee;
  color: #999;
  cursor: not-allowed;
}
</style>
