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
  background-color: #f9f9f9;
  font-family: 'Segoe UI', sans-serif;
  color: #333;
}

.main-header {
  background-color: #ffffff;
  padding: 1rem 2rem;
  border-bottom: 1px solid #e0e0e0;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 960px;
  margin: 0 auto;
}

.header-content h1 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.95rem;
  color: #555;
}

.logout-btn {
  background-color: #f0f0f0;
  border: none;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
  font-size: 0.9rem;
}

.logout-btn:hover {
  background-color: #e0e0e0;
}

main {
  max-width: 960px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.welcome-section {
  text-align: center;
  margin-bottom: 2.5rem;
}

.welcome-section h2 {
  font-size: 1.4rem;
  font-weight: 500;
  margin-bottom: 0.3rem;
}

.welcome-section p {
  color: #777;
  font-size: 0.95rem;
}

.subjects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.5rem;
}

.subject-card {
  background-color: #ffffff;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease-in-out;
  cursor: pointer;
  text-align: center;
  border: 1px solid #ebebeb;
}

.subject-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}

.subject-icon {
  width: 50px;
  height: 50px;
  margin: 0 auto 1rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.math-icon {
  background-color: #4a90e2;
}
.math-icon::before {
  content: "∑";
}

.language-icon {
  background-color: #f06292;
}
.language-icon::before {
  content: "A";
}

.science-icon {
  background-color: #81c784;
}
.science-icon::before {
  content: "⚗";
}

.history-icon {
  background-color: #fbc02d;
}
.history-icon::before {
  content: "H";
}

.subject-card h3 {
  margin-bottom: 0.3rem;
  font-size: 1.1rem;
  font-weight: 500;
}

.subject-card p {
  font-size: 0.85rem;
  color: #666;
}
</style>
