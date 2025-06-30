<template>
  <Card
    title="Promedio General"
    subtitle="Puntaje promedio en ensayos"
    :icon="Award"
    icon-background="#fef5e7"
    icon-color="#d69e2e"
    grid-area="promedio-general"
    :expandable="true"
    @expanded="onExpanded"
    @collapsed="onCollapsed"
  >
    <!-- Contenido principal (siempre visible) -->
    <template #main-content>
      <div class="metric-value">{{ promedio }}</div>
      <div class="metric-label">Puntaje promedio</div>
      <div class="improvement-indicator positive">
        <TrendingUp :size="16" class="mr-1" />
        +{{ mejoraSinceLastTest }} pts desde último ensayo
      </div>
    </template>

    <!-- Contenido expandido (solo visible cuando se expande) -->
    <template #expanded-content>
      <div class="expanded-metrics">
        <h4 class="expanded-title">Detalles del Rendimiento</h4>
        
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-icon">
              <Target :size="20" />
            </div>
            <div class="metric-info">
              <div class="metric-number">{{ promedioMaximo }}</div>
              <div class="metric-desc">Máximo alcanzado</div>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon">
              <TrendingDown :size="20" />
            </div>
            <div class="metric-info">
              <div class="metric-number">{{ promedioMinimo }}</div>
              <div class="metric-desc">Mínimo registrado</div>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon">
              <Calendar :size="20" />
            </div>
            <div class="metric-info">
              <div class="metric-number">{{ diasDesdeUltimoEnsayo }}</div>
              <div class="metric-desc">Días desde último ensayo</div>
            </div>
          </div>
          
          <div class="metric-card">
            <div class="metric-icon">
              <Zap :size="20" />
            </div>
            <div class="metric-info">
              <div class="metric-number">{{ rachaPositiva }}</div>
              <div class="metric-desc">Ensayos mejorando</div>
            </div>
          </div>
        </div>

        <!-- Gráfico de evolución -->
        <div class="evolution-chart">
          <h5 class="chart-title">Evolución últimos 10 ensayos</h5>
          <div class="chart-container">
            <canvas ref="evolutionChart"></canvas>
          </div>
        </div>

        <!-- Objetivos -->
        
      </div>
    </template>

    <!-- Footer con acciones rápidas -->
    <template #footer-content>
      <div class="quick-actions">
        <button class="action-btn primary" @click="verDetalleCompleto">
          <BarChart3 :size="16" />
          Ver análisis completo
        </button>
      </div>
    </template>
  </Card>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import Chart from 'chart.js/auto'
import Card from '../Card.vue'
import { 
  Award, TrendingUp, TrendingDown, Target, Calendar, 
  Zap, BarChart3 
} from 'lucide-vue-next'

// Props del componente
const props = defineProps({
  promedio: {
    type: Number,
    default: 685
  },
  mejoraSinceLastTest: {
    type: Number,
    default: 25
  },
  datosEvolucion: {
    type: Array,
    default: () => [580, 610, 635, 650, 665, 680, 690, 675, 685, 695]
  }
})

// Datos adicionales para el contenido expandido
const promedioMaximo = ref(720)
const promedioMinimo = ref(580)
const diasDesdeUltimoEnsayo = ref(3)
const rachaPositiva = ref(4)

const objetivos = ref([
  { nombre: 'Universidad Top', puntaje: 750, color: '#38a169' },
  { nombre: 'Universidad Objetivo', puntaje: 680, color: '#3182ce' },
  { nombre: 'Universidad Mínima', puntaje: 600, color: '#d69e2e' }
])

// Referencias para el gráfico
const evolutionChart = ref(null)

// Eventos
const onExpanded = () => {
  console.log('Tarjeta expandida')
  // Renderizar gráfico cuando se expande
  nextTick(() => {
    if (evolutionChart.value) {
      renderEvolutionChart()
    }
  })
}

const onCollapsed = () => {
  console.log('Tarjeta colapsada')
}

// Acciones del footer
const verDetalleCompleto = () => {
  console.log('Navegando a detalle completo...')
  // Aquí podrías navegar a otra página o mostrar un modal
}

const compararConMetas = () => {
  console.log('Mostrando comparación con metas...')
  // Aquí podrías mostrar un modal o expandir más contenido
}

// Renderizar gráfico de evolución
const renderEvolutionChart = () => {
  if (!evolutionChart.value) return

  new Chart(evolutionChart.value.getContext('2d'), {
    type: 'line',
    data: {
      labels: ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10'],
      datasets: [{
        label: 'Puntaje',
        data: props.datosEvolucion,
        borderColor: '#d69e2e',
        backgroundColor: 'rgba(214, 158, 46, 0.1)',
        borderWidth: 3,
        pointBackgroundColor: '#fff',
        pointBorderColor: '#d69e2e',
        pointRadius: 5,
        pointHoverRadius: 8,
        pointBorderWidth: 2,
        fill: true,
        tension: 0.4
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
          borderColor: '#d69e2e',
          borderWidth: 1,
          padding: 10,
          displayColors: false
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          min: Math.min(...props.datosEvolucion) - 20,
          grid: { color: '#e2e8f0' },
          ticks: { color: '#718096', font: { size: 12 } }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#718096', font: { size: 12 } }
        }
      }
    }
  })
}
</script>

<style scoped>
/* Estilos del contenido principal */
.metric-value {
  font-size: 2.5rem;
  font-weight: bold;
  color: #2d3748;
  margin-bottom: 10px;
}

.metric-label {
  color: #718096;
  font-size: 0.9rem;
  margin-bottom: 15px;
}

.improvement-indicator {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.positive { 
  background: #c6f6d5; 
  color: #22543d; 
}

/* Estilos del contenido expandido */
.expanded-metrics {
  animation: fadeInUp 0.4s ease-out;
}

.expanded-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 20px;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 16px;
  margin-bottom: 30px;
}

.metric-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.metric-icon {
  background: #e6f3ff;
  color: #3182ce;
  border-radius: 8px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.metric-number {
  font-size: 1.3rem;
  font-weight: bold;
  color: #2d3748;
}

.metric-desc {
  font-size: 0.8rem;
  color: #718096;
}

/* Gráfico de evolución */
.evolution-chart {
  margin-bottom: 30px;
}

.chart-title {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 15px;
}

.chart-container {
  position: relative;
  height: 200px;
  background: #f8fafc;
  border-radius: 12px;
  padding: 15px;
}

/* Sección de objetivos */
.goals-section {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
}

.goals-title {
  font-size: 1rem;
  font-weight: 600;
  color: #2d3748;
  margin-bottom: 15px;
}

.goal-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.goal-item:last-child {
  margin-bottom: 0;
}

.goal-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.goal-name {
  font-size: 0.9rem;
  font-weight: 500;
  color: #2d3748;
}

.goal-target {
  font-size: 0.8rem;
  color: #718096;
}

.goal-progress {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 120px;
}

.progress-bar {
  background: #e2e8f0;
  border-radius: 10px;
  height: 6px;
  flex: 1;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.8rem;
  font-weight: 500;
  color: #4a5568;
  min-width: 35px;
}

/* Acciones rápidas */
.quick-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.primary {
  background: #3182ce;
  color: white;
}

.action-btn.primary:hover {
  background: #2c5aa0;
  transform: translateY(-1px);
}


/* Utilidades */
.mr-1 { margin-right: 0.25rem; }

/* Animaciones */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-actions {
    flex-direction: column;
  }
  
  .action-btn {
    justify-content: center;
  }
}
</style>