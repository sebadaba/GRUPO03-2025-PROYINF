<template>
  <div class="home-page">
    <header class="main-header">
      <div class="header-content">
        <h1>Ensayo PAES</h1>
        <div class="user-info">
          <span>{{ userName }}</span>
          <button @click="goToStats" class="stats-btn">Ver estadísticas</button>
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
          <div class="subject-icon" :style="{ backgroundColor: subject.color }">
            <component :is="subject.icon" :size="36" />
          </div>
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
import { Sigma, BookText, FlaskConical, Landmark } from 'lucide-vue-next'

const router = useRouter();
const user = ref(null);

const userName = computed(() => user.value?.name || 'Estudiante');

// Datos de ejemplo para las asignaturas con íconos lucide
const subjects = ref([
  {
    id: 1,
    name: 'Matemáticas',
    description: 'Ensayo de matemáticas con preguntas de álgebra, geometría y estadística.',
    icon: Sigma,
    color: '#3498db'
  },
  {
    id: 2,
    name: 'Lenguaje',
    description: 'Ensayo de comprensión lectora y análisis de textos.',
    icon: BookText,
    color: '#e74c3c'
  },
  {
    id: 3,
    name: 'Ciencias',
    description: 'Ensayo de biología, química y física.',
    icon: FlaskConical,
    color: '#2ecc71'
  },
  {
    id: 4,
    name: 'Historia',
    description: 'Ensayo de historia, geografía y ciencias sociales.',
    icon: Landmark,
    color: '#f39c12'
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

// Función para ver estadísticas
const goToStats = () => {
  router.push('/dashboard');
};

// Función para cerrar sesión
const logout = () => {
  localStorage.removeItem('user');
  localStorage.removeItem('selectedSubject');
  router.push('/');
};
</script>

<style scoped>
/* Micro-interacciones y Layout mejora */

/* Responsive Design */
@media (max-width: 900px) {
  .subjects-grid {
    grid-template-columns: 1fr;
    grid-template-rows: auto;
    gap: 1.5rem;
  }

  .subject-card:nth-child(1),
  .subject-card:nth-child(2),
  .subject-card:nth-child(3),
  .subject-card:nth-child(4) {
    grid-column: 1;
    grid-row: auto;
  }

  .subject-card {
    min-height: 200px;
  }
}

@media (max-width: 700px) {
  .main-header {
    padding: 1rem;
  }

  .header-content {
    flex-direction: column;
    gap: 1rem;
  }

  main {
    padding: 2rem 1rem;
  }

  .welcome-section {
    margin-bottom: 2rem;
  }

  .welcome-section h2 {
    font-size: 1.75rem;
  }

  .welcome-section p {
    font-size: 1rem;
  }

  .subject-card {
    padding: 1.5rem;
    min-height: 180px;
  }

  .subject-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 1rem;
  }

  .subject-card h3 {
    font-size: 1.25rem;
  }

  .subject-card p {
    font-size: 0.9rem;
  }
}

@media (max-width: 600px) {
  .subjects-grid {
    grid-template-columns: 1fr !important;
    grid-template-rows: none !important;
    gap: 1.25rem;
  }
  .subject-card {
    grid-column: auto !important;
    grid-row: auto !important;
    min-height: 140px;
    padding: 1rem;
  }
  .subject-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 0.75rem;
  }
}

@media (max-width: 400px) {
  .main-header {
    padding: 1rem 0.5rem;
  }

  main {
    padding: 1.5rem 0.5rem;
  }

  .stats-btn, .logout-btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }

  .user-info {
    gap: 1rem;
  }

  .subject-card {
    padding: 1rem;
    min-height: 160px;
  }
}

/* Animaciones adicionales para los iconos */
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.subject-card:hover .subject-icon {
  animation: float 2s ease-in-out infinite;
}

/* Efecto de pulso para botones */
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.stats-btn:focus {
  animation: pulse 1.5s infinite;
}

.home-page {
  width: 100%;
  min-height: 100vh;
  background-color: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.main-header {
  background: linear-gradient(135deg, #3498db, #2980b9);
  color: white;
  padding: 1.5rem 2rem;
  box-shadow: 0 8px 32px rgba(52, 152, 219, 0.2);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
}

.header-content h1 {
  font-weight: 700;
  font-size: 1.75rem;
  letter-spacing: -0.5px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.user-info span {
  font-weight: 500;
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-btn, .logout-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  border: none;
  font-family: inherit;
}

.stats-btn {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
}

.stats-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px 0 rgba(16, 185, 129, 0.4);
}

.stats-btn:active {
  transform: translateY(0);
  transition: transform 0.1s;
}

.logout-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: white;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-1px);
}

main {
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 2rem;
}

.welcome-section {
  text-align: center;
  margin-bottom: 4rem;
}

.welcome-section h2 {
  color: #1a202c;
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 1rem;
  letter-spacing: -0.5px;
}

.welcome-section p {
  color: #718096;
  font-size: 1.125rem;
  font-weight: 400;
  max-width: 600px;
  margin: 0 auto;
}

/* Layout Bento Grid adaptado para 4 elementos */
.subjects-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: repeat(8, 60px);
  gap: 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

/* Posicionamiento específico para crear el efecto Bento */
.subject-card:nth-child(1) {
  grid-column: 1 / 7;
  grid-row: 1 / 5;
}

.subject-card:nth-child(2) {
  grid-column: 7 / 13;
  grid-row: 1 / 5;
}

.subject-card:nth-child(3) {
  grid-column: 1 / 7;
  grid-row: 5 / 9;
}

.subject-card:nth-child(4) {
  grid-column: 7 / 13;
  grid-row: 5 / 9;
}

.subject-card {
  background: white;
  border-radius: 20px;
  padding: 2rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

/* Micro-interacciones avanzadas */
.subject-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
  transform: translateX(-100%);
  transition: transform 0.6s ease;
}

.subject-card:hover::before {
  transform: translateX(100%);
}

.subject-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.subject-card:active {
  transform: translateY(-4px) scale(1.01);
  transition: transform 0.1s ease;
}

.subject-icon {
  width: 80px;
  height: 80px;
  margin-bottom: 1.5rem;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  position: relative;
  transition: all 0.3s ease;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.subject-card:hover .subject-icon {
  transform: scale(1.1) rotate(5deg);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
}

.subject-card h3 {
  margin-bottom: 0.75rem;
  color: #1a202c;
  font-size: 1.5rem;
  font-weight: 600;
  transition: color 0.3s ease;
}

.subject-card:hover h3 {
  color: #2d3748;
}

.subject-card p {
  color: #718096;
  font-size: 1rem;
  line-height: 1.6;
  max-width: 280px;
  transition: color 0.3s ease;
}

.subject-card:hover p {
  color: #4a5568;
}
</style>