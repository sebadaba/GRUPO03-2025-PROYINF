<template>
  <div class="historial-ensayos-page">
    <!-- Botón Volver y Encabezado de Página -->
    <header class="page-header">
      <button @click="goBack" class="back-btn">
        <ArrowLeft :size="20" />
        Volver
      </button>
      <h1>Historial de Ensayos Rendidos</h1>
      <p>Revisa el detalle de todos tus ensayos completados.</p>
    </header>

    <!-- Sección de Filtros -->
    <div class="filtros-container">
      <input type="text" placeholder="Buscar ensayo por nombre..." v-model="searchTerm" class="search-input" />
      <select v-model="selectedSubject" class="filter-select">
        <option value="">Todas las Asignaturas</option>
        <option v-for="subject in uniqueSubjects" :key="subject" :value="subject">{{ subject }}</option>
      </select>
      <select v-model="sortBy" class="filter-select">
        <option value="date_desc">Más Recientes</option>
        <option value="date_asc">Más Antiguos</option>
        <option value="score_desc">Mejor Puntaje</option>
        <option value="score_asc">Menor Puntaje</option>
      </select>
    </div>

    <!-- Lista de Ensayos -->
    <div class="ensayos-list-container">
      <div v-if="loading" class="loading-state">
        <p>Cargando historial...</p>
      </div>
      <div v-else-if="paginatedEnsayos.length === 0" class="empty-state">
        <p>No se encontraron ensayos con los filtros aplicados o aún no has rendido ninguno.</p>
      </div>
      <div v-else>
        <!-- Usa el componente Card para cada ítem del historial -->
        <Card
          v-for="ensayo in paginatedEnsayos"
          :key="ensayo.id"
          :title="ensayo.nombre"
          :subtitle="ensayo.asignatura"
          :icon="getSubjectIcon(ensayo.asignatura)"
          :icon-color="getSubjectColor(ensayo.asignatura)"
          :icon-background="getSubjectBgColor(ensayo.asignatura)"
          :expandable="false"
          class="historial-card-item"
        >
          <template #main-content>
            <div class="historial-item-details">
              <p class="fecha-label">Rendido el: {{ formatDate(ensayo.fecha) }}</p>
              <div class="ensayo-puntaje">
                <span class="puntaje">{{ ensayo.puntaje }} pts</span>
                <div v-if="ensayo.cambio !== null && ensayo.cambio !== undefined" class="cambio-puntaje" :class="ensayo.cambio >= 0 ? 'positive' : 'negative'">
                  <component :is="ensayo.cambio >= 0 ? TrendingUp : TrendingDown" :size="14" />
                  {{ Math.abs(ensayo.cambio) }} pts
                </div>
              </div>
            </div>
          </template>

          <template #footer-content>
            <button @click="verDetalleEnsayo(ensayo.id)" class="detalle-btn">
              Ver Detalles
              <ChevronRight :size="16" />
            </button>
          </template>
        </Card>
      </div>
    </div>

    <!-- Paginación -->
    <div class="paginacion-container" v-if="!loading && totalPages > 1">
      <button @click="prevPage" :disabled="currentPage === 1">Anterior</button>
      <span>Página {{ currentPage }} de {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages">Siguiente</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import Card from '../components/Card.vue';
import { ArrowLeft, ChevronRight, TrendingUp, TrendingDown, BookText, Sigma, FlaskConical, Landmark, FileText as DefaultIcon } from 'lucide-vue-next';

const router = useRouter();

// --- Datos y Lógica  ---
const todosLosEnsayos = ref([]);
const loading = ref(true);
const searchTerm = ref('');
const selectedSubject = ref('');
const sortBy = ref('date_desc'); // 'date_desc', 'date_asc', 'score_desc', 'score_asc'

const currentPage = ref(1);
const itemsPerPage = ref(10); // Cuántos ensayos mostrar por página

// Simulación de carga de datos
onMounted(async () => {
  loading.value = true;
  await new Promise(resolve => setTimeout(resolve, 1200)); // Simular delay
  todosLosEnsayos.value = [
    // Datos de ejemplo
    { id: 1, nombre: 'Ensayo PAES Competencia Matemática M1 - Modelo A', asignatura: 'Matemáticas', fecha: '2024-07-15T10:00:00Z', puntaje: 720, cambio: 15 },
    { id: 2, nombre: 'Ensayo PAES Competencia Lectora - Texto Narrativo', asignatura: 'Lenguaje', fecha: '2024-07-10T14:30:00Z', puntaje: 680, cambio: -5 },
    { id: 3, nombre: 'Mini Ensayo Ciencias - Módulo Biología Celular', asignatura: 'Ciencias', fecha: '2024-07-05T09:00:00Z', puntaje: 695, cambio: 20 },
    { id: 4, nombre: 'Ensayo PAES Historia - Chile Siglo XX', asignatura: 'Historia', fecha: '2024-07-01T11:00:00Z', puntaje: 710, cambio: 10 },
    { id: 5, nombre: 'Ensayo PAES Competencia Matemática M2 - Avanzado', asignatura: 'Matemáticas', fecha: '2024-06-28T16:00:00Z', puntaje: 750, cambio: 30 },
    { id: 6, nombre: 'Ensayo PAES Competencia Lectora - Texto Argumentativo', asignatura: 'Lenguaje', fecha: '2024-06-25T10:00:00Z', puntaje: 690, cambio: 10 },
    { id: 7, nombre: 'Mini Ensayo Ciencias - Módulo Química Orgánica', asignatura: 'Ciencias', fecha: '2024-06-20T15:00:00Z', puntaje: 670, cambio: -25 },
    { id: 8, nombre: 'Ensayo PAES Historia - Universal Contemporánea', asignatura: 'Historia', fecha: '2024-06-15T09:30:00Z', puntaje: 700, cambio: -10 },
    { id: 9, nombre: 'Ensayo PAES Competencia Matemática M1 - Modelo B', asignatura: 'Matemáticas', fecha: '2024-06-10T13:00:00Z', puntaje: 700, cambio: -20 },
    { id: 10, nombre: 'Ensayo PAES Competencia Lectora - Poesía', asignatura: 'Lenguaje', fecha: '2024-06-05T11:00:00Z', puntaje: 650, cambio: -40 },
    { id: 11, nombre: 'Mini Ensayo Ciencias - Módulo Física Ondas', asignatura: 'Ciencias', fecha: '2024-06-01T14:00:00Z', puntaje: 720, cambio: 50 },
    { id: 12, nombre: 'Ensayo PAES Historia - Formación Ciudadana', asignatura: 'Historia', fecha: '2024-05-28T10:30:00Z', puntaje: 680, cambio: -20 },
  ];
  loading.value = false;
});

const uniqueSubjects = computed(() => {
  if (!todosLosEnsayos.value) return [];
  const subjects = new Set(todosLosEnsayos.value.map(e => e.asignatura));
  return Array.from(subjects).sort();
});

// Mapeo de asignaturas a iconos y colores
const subjectVisuals = {
  'Matemáticas': { icon: Sigma, color: '#3498db', bgColor: '#e1f5fe' },
  'Lenguaje': { icon: BookText, color: '#e74c3c', bgColor: '#ffebee' },
  'Ciencias': { icon: FlaskConical, color: '#2ecc71', bgColor: '#e8f5e9' },
  'Historia': { icon: Landmark, color: '#f39c12', bgColor: '#fff8e1' },
  'Default': { icon: DefaultIcon, color: '#7f8c8d', bgColor: '#f1f5f9' }
};

const getSubjectIcon = (subjectName) => (subjectVisuals[subjectName] || subjectVisuals.Default).icon;
const getSubjectColor = (subjectName) => (subjectVisuals[subjectName] || subjectVisuals.Default).color;
const getSubjectBgColor = (subjectName) => (subjectVisuals[subjectName] || subjectVisuals.Default).bgColor;


const filteredAndSortedEnsayos = computed(() => {
  if (!todosLosEnsayos.value) return [];
  let ensayos = [...todosLosEnsayos.value];

  if (searchTerm.value) {
    ensayos = ensayos.filter(e => e.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()));
  }
  if (selectedSubject.value) {
    ensayos = ensayos.filter(e => e.asignatura === selectedSubject.value);
  }

  ensayos.sort((a, b) => {
    switch (sortBy.value) {
      case 'date_asc': return new Date(a.fecha) - new Date(b.fecha);
      case 'score_desc': return b.puntaje - a.puntaje;
      case 'score_asc': return a.puntaje - b.puntaje;
      case 'date_desc':
      default: return new Date(b.fecha) - new Date(a.fecha);
    }
  });
  return ensayos;
});

const totalPages = computed(() => {
  return Math.ceil(filteredAndSortedEnsayos.value.length / itemsPerPage.value);
});

const paginatedEnsayos = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredAndSortedEnsayos.value.slice(start, end);
});

watch([searchTerm, selectedSubject, sortBy], () => {
  currentPage.value = 1; // Resetear a la primera página si cambian los filtros
});

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('es-CL', options);
};

const goBack = () => router.back();
//crear esto
const verDetalleEnsayo = (ensayoId) => {
  
  router.push({ name: 'DetalleEnsayo', params: { id: ensayoId } }); // Asumiendo que tienes una ruta nombrada 'DetalleEnsayo'
  console.log(`Ver detalles del ensayo ID: ${ensayoId}`);
};
</script>

<style scoped>
.historial-ensayos-page {
  max-width: 900px;
  margin: 2rem auto;
  padding: 1.5rem;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  color: #374151;
}

.page-header {
  margin-bottom: 2.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #e9ecef;
  text-align: center;
}

.page-header h1 {
  font-size: 2.25rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.page-header p {
  font-size: 1.125rem;
  color: #6b7280;
  margin-top: 0;
}

.back-btn {
  background: #f0f4ff;
  color: #3182ce;
  border: none;
  border-radius: 0.5rem;
  padding: 0.75rem 1.25rem;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s ease;
  margin-bottom: 1.5rem;
}

.back-btn:hover {
  background-color: #e6f3ff;
}

.filtros-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background-color: #f9fafb;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
}

.search-input, .filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 0.95rem;
  background-color: white;
  flex-grow: 1;
}

.filter-select {
  min-width: 180px;
  flex-grow: 0;
}

.ensayos-list-container {
  min-height: 200px;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #6b7280;
  font-size: 1.1rem;
  background-color: #f9fafb;
  border-radius: 0.75rem;
}

.historial-card-item {
  margin-bottom: 1.25rem;
}

.historial-item-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ensayo-info {
  flex-grow: 1;
}

.ensayo-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.asignatura-label {
  font-size: 0.8rem;
  color: #4b5563;
  background-color: #f3f4f6;
  padding: 0.25rem 0.6rem;
  border-radius: 0.375rem;
  display: inline-block;
  margin-bottom: 0.375rem;
  font-weight: 500;
}

.fecha-label {
  font-size: 0.875rem;
  color: #6b7280;
}

.ensayo-puntaje {
  text-align: right;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.puntaje {
  font-size: 1.375rem;
  font-weight: 700;
  color: #3b82f6;
  display: block;
  margin-bottom: 0.2rem;
}

.cambio-puntaje {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.3rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.cambio-puntaje.positive { color: #10b981; }
.cambio-puntaje.negative { color: #ef4444; }

.detalle-btn {
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  padding: 0.6rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.2s ease;
  width: 100%;
  justify-content: center;
}

.detalle-btn:hover {
  background-color: #2563eb;
}

.paginacion-container {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2.5rem;
  padding: 1rem 0;
}

.paginacion-container button {
  padding: 0.6rem 1rem;
  border: 1px solid #d1d5db;
  background-color: white;
  color: #3b82f6;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background-color 0.2s, color 0.2s;
  font-weight: 500;
}

.paginacion-container button:hover:not(:disabled) {
  background-color: #f3f4f6;
}

.paginacion-container button:disabled {
  color: #9ca3af;
  cursor: not-allowed;
  background-color: #f9fafb;
}

.paginacion-container span {
  color: #6b7280;
  font-size: 0.95rem;
}

@media (max-width: 768px) {
  .back-btn {
    position: static;
    margin-bottom: 1.5rem;
    transform: none;
  }
  
  .filtros-container {
    flex-direction: column;
  }
  
  .search-input, .filter-select {
    width: 100%;
  }

  .historial-item-details {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .ensayo-puntaje {
    align-items: flex-start;
  }
}
</style>
