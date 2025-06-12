<template>
  <div class="dashboard-container">
    <div class="header">
      <button class="home-btn" @click="$router.push('/home')">
        <ArrowLeft :size="16" />
        volver al home
      </button>
      <h1 class="header-title">
        <BarChart3 :size="32" class="inline mr-2" />
        Dashboard PAES
      </h1>
      <p class="header-subtitle">Seguimiento de rendimiento en ensayos</p>
    </div>
    <div class="bento-grid">
      <!-- tarjeta principal con el promedio general -->
      <div class="bento-card promedio-general">
        <div class="card-header">
          <div class="card-icon" style="background: #fef5e7; color: #d69e2e;">
            <Award :size="24" />
          </div>
          <div class="card-title">Promedio General</div>
        </div>
        <div class="metric-value">{{ promedio }}</div>
        <div class="metric-label">Puntaje promedio</div>
        <div class="improvement-indicator positive">
          <TrendingUp :size="16" class="mr-1" />
          +25 pts desde ultimo ensayo
        </div>
      </div>
      <!-- tarjeta con la cantidad de ensayos rendidos -->
      <div class="bento-card ensayos-rendidos">
        <div class="card-header">
          <div class="card-icon" style="background: #e6fffa; color: #00a693;">
            <FileText :size="24" />
          </div>
          <div class="card-title">Ensayos Rendidos</div>
        </div>
        <div class="metric-value">{{ ensayos }}</div>
        <div class="metric-label">Total completados</div>
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: '80%', background: '#00a693' }"></div>
        </div>
        <div class="goal-text">
          <Target :size="14" class="inline mr-1" />
          Objetivo: 15 ensayos
        </div>
      </div>
      <!-- tarjeta con la tasa de mejora y grafico -->
      <div class="bento-card tasa-mejora">
        <div class="card-header">
          <div class="card-icon" style="background: #f0fff4; color: #38a169;">
            <TrendingUp :size="24" />
          </div>
          <div class="card-title">Tasa de Mejora</div>
        </div>
        <div class="metric-value">+{{ mejora.porcentaje }}%</div>
        <div class="metric-label">Ultimos 5 ensayos</div>
        <div class="chart-container">
          <canvas ref="mejoraChart"></canvas>
        </div>
      </div>
      <!-- tarjeta con la ponderacion actual -->
      <div class="bento-card ponderacion">
        <div class="card-header">
          <div class="card-icon" style="background: #e6f3ff; color: #3182ce;">
            <Target :size="24" />
          </div>
          <div class="card-title">Ponderacion Actual</div>
        </div>
        <div class="metric-value">{{ ponderacion.puntaje }}</div>
        <div class="metric-label">Puntaje ponderado</div>
        <div class="weights-list">
          <div v-for="item in ponderacion.detalle" :key="item.nombre" class="weight-item">
            <Calculator :size="14" class="mr-2" />
            {{ item.nombre }}: {{ item.valor }}
          </div>
        </div>
      </div>
      <!-- tarjeta con la comparacion con compañeros -->
      <div class="bento-card comparacion-compañeros">
        <div class="card-header">
          <div class="card-icon" style="background: #edf2f7; color: #4a5568;">
            <Users :size="24" />
          </div>
          <div class="card-title">Comparacion con Compañeros</div>
        </div>
        <div class="comparison-item">
          <span class="flex items-center">
            <User :size="16" class="mr-2" />
            Tu puntaje
          </span>
          <strong style="color: #3182ce;">{{ comparacion.tuPuntaje }}</strong>
        </div>
        <div class="comparison-item">
          <span class="flex items-center">
            <BarChart :size="16" class="mr-2" />
            Promedio curso
          </span>
          <span>{{ comparacion.promedioCurso }}</span>
        </div>
        <div class="comparison-item">
          <span class="flex items-center">
            <Crown :size="16" class="mr-2" />
            Mejor del curso
          </span>
          <span>{{ comparacion.mejorCurso }}</span>
        </div>
        <div class="comparison-item">
          <span class="flex items-center">
            <Medal :size="16" class="mr-2" />
            Tu posicion
          </span>
          <strong style="color: #38a169;">{{ comparacion.posicion }}</strong>
        </div>
      </div>
      <!-- tarjeta con los tipos de preguntas mas erradas -->
      <div class="bento-card tipos-errores">
        <div class="card-header">
          <div class="card-icon" style="background: #fed7d7; color: #e53e3e;">
            <AlertCircle :size="24" />
          </div>
          <div class="card-title">Tipos de Preguntas Mas Erradas</div>
        </div>
        <div class="error-type" v-for="err in errores" :key="err.tipo">
          <span class="flex items-center">
            <XCircle :size="16" class="mr-2 text-red-500" />
            {{ err.tipo }}
          </span>
          <div class="error-percentage">{{ err.porcentaje }}%</div>
        </div>
      </div>
      <!-- tarjeta con el detalle por asignaturas -->
      <div class="bento-card detalle-asignaturas">
        <div class="card-header">
          <div class="card-icon" style="background: #e6f3ff; color: #3182ce;">
            <BookOpen :size="24" />
          </div>
          <div class="card-title">Rendimiento por Asignatura</div>
        </div>
        <div class="subjects-grid">
          <div class="subject-card" v-for="asig in asignaturas" :key="asig.nombre">
            <div class="subject-score" :style="{ color: asig.color }">{{ asig.puntaje }}</div>
            <div class="flex items-center justify-center mb-2">
              <component :is="getSubjectIcon(asig.nombre)" :size="16" class="mr-1" />
              {{ asig.nombre }}
            </div>
            <div class="improvement-indicator" :class="asig.tendencia">
              <component :is="getTrendIcon(asig.tendencia)" :size="14" class="mr-1" />
              {{ asig.variacion }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import Chart from 'chart.js/auto'
import { 
  ArrowLeft, BarChart3, Award, TrendingUp, TrendingDown, 
  FileText, Target, Users, User, BarChart, Crown, Medal,
  AlertCircle, XCircle, BookOpen, Calculator, Minus,
  Calculator as MathIcon, BookA, Clock, Atom
} from 'lucide-vue-next'

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

const mejoraChart = ref(null)

// Funciones para iconos dinámicos
const getSubjectIcon = (subject) => {
  const iconMap = {
    'Matematica': MathIcon,
    'Comprension Lectora': BookA,
    'Historia': Clock,
    'Ciencias': Atom
  }
  return iconMap[subject] || BookOpen
}

const getTrendIcon = (trend) => {
  const trendMap = {
    'positive': TrendingUp,
    'negative': TrendingDown,
    'neutral': Minus
  }
  return trendMap[trend] || Minus
}

onMounted(async () => {
  await nextTick()
  if (mejoraChart.value) {
    new Chart(mejoraChart.value.getContext('2d'), {
      type: 'line',
      data: {
        labels: ['Ensayo 8', 'Ensayo 9', 'Ensayo 10', 'Ensayo 11', 'Ensayo 12'],
        datasets: [{
          label: 'Puntaje',
          data: mejora.value.datos,
          borderColor: '#38a169',
          backgroundColor: 'rgba(56, 161, 105, 0.15)',
          borderWidth: 4,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#38a169',
          pointRadius: 7,
          pointHoverRadius: 10,
          pointBorderWidth: 3,
          fill: true,
          tension: 0.45,
          shadowOffsetX: 0,
          shadowOffsetY: 4,
          shadowBlur: 10,
          shadowColor: 'rgba(56,161,105,0.15)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#2d3748',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: '#38a169',
            borderWidth: 1,
            padding: 12,
            caretSize: 8,
            displayColors: false
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            min: 600,
            grid: {
              color: '#e2e8f0',
              borderDash: [4, 4]
            },
            ticks: {
              color: '#718096',
              font: { size: 13 }
            }
          },
          x: {
            grid: { display: false },
            ticks: {
              color: '#718096',
              font: { size: 13 }
            }
          }
        }
      }
    })
  }
})
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
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 28px;
  grid-template-areas:
    "promedio promedio ensayos"
    "mejora mejora ponderacion"
    "comparacion errores errores"
    "detalle detalle detalle";
}

/* Tarjetas principales */
.bento-card {
  background: linear-gradient(135deg, #fff 80%, #f0f4ff 100%);
  box-shadow: 0 4px 24px rgba(76, 110, 245, 0.08);
  border-radius: 24px;
  padding: 32px 24px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.bento-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 32px rgba(76, 110, 245, 0.15);
}

/* Header de cada tarjeta */
.card-header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.card-icon {
  background: rgba(76, 110, 245, 0.08);
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
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

.progress-bar {
  background: #e2e8f0;
  border-radius: 10px;
  height: 8px;
  margin: 15px 0;
  overflow: hidden;
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

.chart-container {
  position: relative;
  height: 200px;
  margin-top: 20px;
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

/* Áreas del grid para cada tarjeta */
.promedio-general { grid-area: promedio; }
.ensayos-rendidos { grid-area: ensayos; }
.tasa-mejora { grid-area: mejora; }
.ponderacion { grid-area: ponderacion; }
.comparacion-compañeros { grid-area: comparacion; }
.tipos-errores { grid-area: errores; }
.detalle-asignaturas { grid-area: detalle; }

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