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
          @click="openAddQuestion"
        >
          Crear Pregunta
        </button>
        <button 
          @click="router.push('/create-ensayo')" 
          :class="{ active: activeTab === 'tests' }"
        >
          Ensayos
        </button>
      </div>
      
      <!-- Banco de Preguntas -->
      <div v-if="activeTab === 'questions'" class="tab-content">
        <QuestionBank ref="questionBankRef" />
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
import QuestionBank from '../components/QuestionBank.vue';

const router = useRouter();
const user = ref(null);
const userName = computed(() => user.value?.name || 'Docente');
const teacherSubject = computed(() => user.value?.subject || 'Sin asignatura asignada');
const activeTab = ref('questions');
const questionBankRef = ref(null);

const openAddQuestion = () => {
  activeTab.value = 'questions';
  setTimeout(() => {
    questionBankRef.value?.openAddQuestionForm();
  }, 0);
};

onMounted(() => {
  const storedUser = localStorage.getItem('user');
  if (!storedUser) {
    router.push('/');
    return;
  }
  
  user.value = JSON.parse(storedUser);
  
  if (user.value.role !== 'teacher') {
    router.push('/home');
    return;
  }
  
  if (!user.value.subject) {
    alert('No tienes una asignatura asignada. Contacta al administrador.');
    logout();
    return;
  }
});

const logout = () => {
  localStorage.removeItem('user');
  router.push('/');
};
</script>

<style scoped>
* {
  box-sizing: border-box;
}

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

.header-content h1 {
  margin: 0;
  font-size: 1.5rem;
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
  overflow-x: auto;
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
  white-space: nowrap;
  flex-shrink: 0;
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

.tab-content h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #333;
}

.coming-soon {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
  font-style: italic;
  line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 1024px) {
  main {
    padding: 1.5rem;
  }
  
  .tab-content {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .main-header {
    padding: 1rem;
  }
  
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .header-content h1 {
    font-size: 1.3rem;
  }
  
  main {
    padding: 1rem;
  }
  
  .tab-content {
    padding: 1rem;
  }
  
  .dashboard-tabs {
    border-bottom: none;
    background: white;
    border-radius: 8px;
    padding: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .dashboard-tabs button {
    padding: 0.8rem 1.2rem;
    font-size: 0.9rem;
    border-radius: 4px;
  }
  
  .dashboard-tabs button.active {
    background-color: #3498db;
    color: white;
    border-bottom: none;
  }
}

@media (max-width: 480px) {
  .main-header {
    padding: 0.8rem;
  }
  
  .header-content h1 {
    font-size: 1.1rem;
  }
  
  .user-info {
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .logout-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }
  
  main {
    padding: 0.5rem;
  }
  
  .tab-content {
    padding: 0.8rem;
  }
  
  .dashboard-tabs {
    padding: 0.3rem;
  }
  
  .dashboard-tabs button {
    padding: 0.6rem 0.8rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 320px) {
  .header-content h1 {
    font-size: 1rem;
  }
  
  .dashboard-tabs button {
    padding: 0.5rem 0.6rem;
    font-size: 0.8rem;
  }
}
</style>