<template>
  <div class="dashboard-container">
    <div class="header">
      <button class="home-btn" @click="$router.push('/home')">
        <ArrowLeft :size="16" />
        Volver al Inicio
      </button>
      <h1 class="header-title">
        <BarChart3 :size="32" class="inline mr-2" />
        Dashboard PAES
      </h1>
      <p class="header-subtitle">Seguimiento de rendimiento en ensayos</p>
    </div>
    <div class="bento-grid">
      <!-- tarjeta principal con el promedio general -->
      <PromedioGeneralCard
        :promedio="promedio"
        :mejora-since-last-test="25"
        :datos-evolucion="[620, 645, 660, 675, 685, 690, 695, 700, 705, 710]"
      />
      <!-- tarjeta con la cantidad de ensayos rendidos -->
      <EnsayoRendidosCard
        :ensayos="ensayos"
        objetivo="15"  
        ultimo-ensayo="3 días"
      />
      <TasaMejoraCard
        :mejora="mejora"
      />
      <PonderacionCard :ponderacion="ponderacion" />
      <ComparacionCompanerosCard
        :comparacion="comparacion"
        :icon="Users"
        icon-background="#f0f4ff"
        icon-color="#4a5568"
      />
      <PreguntasErradasCard :errores="errores" />
      <RendimientoPorAsignaturaCard :asignaturas="asignaturas" />
    </div>
  </div>
</template>

<script setup>
import PromedioGeneralCard from '../components/dashboard/PromedioGeneralCard.vue'
import TasaMejoraCard from '../components/dashboard/TasaMejoraCard.vue'
import EnsayoRendidosCard from '../components/dashboard/EnsayoRendidosCard.vue'
import ComparacionCompanerosCard from '../components/dashboard/ComparacionCompanerosCard.vue'
import PonderacionCard from '../components/dashboard/PonderacionCard.vue'
import PreguntasErradasCard from '../components/dashboard/PreguntasErradasCard.vue'
import RendimientoPorAsignaturaCard from '../components/dashboard/RendimientoPorAsignaturaCard.vue'
import { ref } from 'vue'
import { ArrowLeft, BarChart3, Users } from 'lucide-vue-next'

// variables reactivas que simulan respuesta de la api
const promedio = ref(685)
const ensayos = ref(12)
const mejora = ref({
  porcentaje: 8.5,
  datos: [620, 645, 660, 675, 685]
})
const ponderacion = ref({
  puntaje: 720,
  detalle: [
    { nombre: 'Matematica', valor: '30%' },
    { nombre: 'Lenguaje', valor: '25%' },
    { nombre: 'Historia', valor: '25%' },
    { nombre: 'Ciencias', valor: '20%' }
  ]
})
const comparacion = ref({
  tuPuntaje: 685,
  promedioCurso: 642,
  mejorCurso: 742,
  posicion: '3° de 25'
})
const errores = ref([
  { tipo: 'Comprension lectora - a', porcentaje: 35 },
  { tipo: 'Matematica - Geometria', porcentaje: 28 },
  { tipo: 'Historia - Siglo XX', porcentaje: 22 },
  { tipo: 'Ciencias - Quimica organica', porcentaje: 18 }
])
const asignaturas = ref([
  { nombre: 'Matematica', puntaje: 720, color: '#38a169', tendencia: 'positive', variacion: '+15' },
  { nombre: 'Comprension Lectora', puntaje: 685, color: '#3182ce', tendencia: 'positive', variacion: '+8' },
  { nombre: 'Historia', puntaje: 650, color: '#d69e2e', tendencia: 'neutral', variacion: '0' },
  { nombre: 'Ciencias', puntaje: 620, color: '#e53e3e', tendencia: 'negative', variacion: '-5' }
])
</script>

<style scoped>
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  min-height: 100vh;
}

.header {
  text-align: center;
  margin-bottom: 30px;
  color: white;
}

.header-title {
  font-size: 2.5rem;
  margin-bottom: 10px;
  color: black;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  color: grey;
}

/* grid principal bento con areas para cada tarjeta */
.bento-grid {
  align-items: start;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  grid-template-areas:
    "promedio promedio ensayos"
    "mejora mejora ponderacion"
    "comparacion errores errores"
    "detalle detalle detalle";
}
.bento-grid > *{
  align-self: start;
}

.metric-value {
  font-size: 2.5rem;
  font-weight: bold;
  color: #2d3748;
  margin-bottom: 10px;
}

.metric-label {
  color: #718096;
  font-size: 0.9rem;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.3s ease;
}

.goal-text {
  margin-top: 10px;
  color: #718096;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
}

.weights-list {
  margin-top: 15px;
  font-size: 0.9rem;
  color: #718096;
}

.weight-item {
  display: flex;
  align-items: center;
  padding: 4px 0;
}

.comparison-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #e2e8f0;
}

.comparison-item:last-child {
  border-bottom: none;
}

.error-type {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #e2e8f0;
}

.error-type:last-child {
  border-bottom: none;
}

.error-percentage {
  background: #fed7d7;
  color: #742a2a;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 500;
}

.subjects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
  margin-top: 20px;
}

.subject-card {
  background: #f7fafc;
  border-radius: 12px;
  padding: 15px;
  text-align: center;
}

.subject-score {
  font-size: 1.8rem;
  font-weight: bold;
  margin-bottom: 5px;
}

.improvement-indicator {
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  margin-top: 10px;
}

.positive { background: #c6f6d5; color: #22543d; }
.negative { background: #fed7d7; color: #742a2a; }
.neutral { background: #e2e8f0; color: #4a5568; }

/* Responsive 2 columnas en tablets, 1 columna en celu */
@media (max-width: 1200px) {
  .bento-grid {
    grid-template-columns: repeat(2, 1fr);
    grid-template-areas:
      "promedio ensayos"
      "mejora ponderacion"
      "comparacion errores"
      "detalle detalle";
  }
}
@media (max-width: 900px) {
  .bento-grid {
    grid-template-columns: 1fr;
    grid-template-areas:
      "promedio"
      "ensayos"
      "mejora"
      "ponderacion"
      "comparacion"
      "errores"
      "detalle";
  }
}

.home-btn {
  background: #f0f4ff;
  color: #3182ce;
  border: none;
  border-radius: 8px;
  padding: 8px 18px;
  font-size: 1rem;
  cursor: pointer;
  margin-bottom: 12px;
  margin-right: 16px;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.home-btn:hover {
  background: #e6f3ff;
}

/* Utility classes para flexbox */
.flex { display: flex; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.inline { display: inline; }
.mr-1 { margin-right: 0.25rem; }
.mr-2 { margin-right: 0.5rem; }
.mb-2 { margin-bottom: 0.5rem; }
.text-red-500 { color: #ef4444; }
</style>
