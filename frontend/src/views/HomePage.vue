<template>
  <div class="home-page">
    <header class="main-header">
      <div class="header-content">
        <h1>Ensayo PAES</h1>
        <div class="user-info">
          <span>{{ userName }}</span>
          <button @click="logout" class="logout-btn">Cerrar Sesión</button>
        </div>
      </div>
    </header>
    
    <main>
      <div class="welcome-section">
        <h2>Bienvenido, {{ userName }}</h2>
        <p>Selecciona una asignatura para comenzar tu ensayo PAES</p>
      </div>
      
      <div class="subjects-grid">
        <div 
          v-for="subject in subjects" 
          :key="subject.id" 
          class="subject-card"
          @click="selectSubject(subject)"
        >
          <div class="subject-icon" :class="subject.icon"></div>
          <h3>{{ subject.name }}</h3>
          <p>{{ subject.description }}</p>
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

const userName = computed(() => user.value?.name || 'Estudiante');

// Datos de ejemplo para las asignaturas
const subjects = ref([
  {
    id: 1,
    name: 'Matemáticas',
    description: 'Ensayo de matemáticas con preguntas de álgebra, geometría y estadística.',
    icon: 'math-icon'
  },
  {
    id: 2,
    name: 'Lenguaje',
    description: 'Ensayo de comprensión lectora y análisis de textos.',
    icon: 'language-icon'
  },
  {
    id: 3,
    name: 'Ciencias',
    description: 'Ensayo de biología, química y física.',
    icon: 'science-icon'
  },
  {
    id: 4,
    name: 'Historia',
    description: 'Ensayo de historia, geografía y ciencias sociales.',
    icon: 'history-icon'
  }
]);

onMounted(() => {
  // Verificar si hay un usuario en localStorage
  const storedUser = localStorage.getItem('user');
  if (!storedUser) {
    router.push('/');
    return;
  }
  
  user.value = JSON.parse(storedUser);
});

// Función para seleccionar una asignatura
const selectSubject = (subject) => {
  // Guardar la asignatura seleccionada en localStorage
  localStorage.setItem('selectedSubject', JSON.stringify(subject));
  
  // Redirigir al ensayo
  router.push('/ensayo');
};

// Función para cerrar sesión
const logout = () => {
  localStorage.removeItem('user');
  localStorage.removeItem('selectedSubject');
  router.push('/');
};
</script>

<style scoped>
.home-page {
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

.welcome-section {
  text-align: center;
  margin-bottom: 3rem;
}

.welcome-section h2 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.welcome-section p {
  color: #7f8c8d;
}

.subjects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 2rem;
}

.subject-card {
  background-color: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  text-align: center;
}

.subject-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.subject-icon {
  width: 60px;
  height: 60px;
  margin: 0 auto 1rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
}

.math-icon {
  background-color: #3498db;
}

.math-icon::before {
  content: "∑";
}

.language-icon {
  background-color: #e74c3c;
}

.language-icon::before {
  content: "A";
}

.science-icon {
  background-color: #2ecc71;
}

.science-icon::before {
  content: "⚗";
}

.history-icon {
  background-color: #f39c12;
}

.history-icon::before {
  content: "H";
}

.subject-card h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.subject-card p {
  color: #666;
  font-size: 0.9rem;
}
</style>