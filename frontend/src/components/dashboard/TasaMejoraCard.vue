<template>
  <Card
    title="Tasa de Mejora"
    subtitle="Progreso en últimos ensayos"
    :icon="TrendingUp"
    icon-background="#f0fff4"
    icon-color="#38a169"
    grid-area="tasa-mejora"
    :expandable="false"
  >
    <template #main-content>
      <div class="metric-value">+{{ mejora.porcentaje }}%</div>
      <div class="metric-label">Últimos {{ mejora.datos.length }} ensayos</div>
      
      <div class="mini-chart-container">
        <canvas ref="miniChart"></canvas>
      </div>

      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-icon positive"><TrendingUp :size="14" /></div>
          <div class="stat-text">
            <span class="stat-value">{{ puntosGanados > 0 ? '+' : ''}}{{ puntosGanados }} pts</span>
            <span class="stat-label">Ganados</span>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon" :class="tendenciaClass"><component :is="tendenciaIcon" :size="14" /></div>
           <div class="stat-text">
            <span class="stat-value">{{ tendenciaTexto }}</span>
            <span class="stat-label">Tendencia</span>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon neutral"><Sigma :size="14" /></div>
           <div class="stat-text">
            <span class="stat-value">{{ puntajePromedio }} pts</span>
            <span class="stat-label">Promedio</span>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon positive"><Target :size="14" /></div>
           <div class="stat-text">
            <span class="stat-value">{{ puntajeMaximo }} pts</span>
            <span class="stat-label">Máximo</span>
          </div>
        </div>
      </div>
    </template>
  </Card>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
// NUEVO: Importamos el ícono Sigma para el promedio
import { TrendingUp, TrendingDown, Target, Sigma } from 'lucide-vue-next'
import Chart from 'chart.js/auto'
import Card from '../Card.vue'

const props = defineProps({
  mejora: {
    type: Object,
    required: true
  }
})

const miniChart = ref(null)

// --- COMPUTED PROPERTIES ---

const puntosGanados = computed(() => {
  const datos = props.mejora.datos
  if (!datos || datos.length < 2) return 0
  return datos[datos.length - 1] - datos[0]
})

const tendenciaClass = computed(() => {
  if (props.mejora.porcentaje > 0) return 'positive'
  if (props.mejora.porcentaje < 0) return 'negative'
  return 'neutral'
})

const tendenciaIcon = computed(() => {
  if (props.mejora.porcentaje > 0) return TrendingUp
  if (props.mejora.porcentaje < 0) return TrendingDown
  return Target
})

const tendenciaTexto = computed(() => {
  if (props.mejora.porcentaje > 0) return 'En alza'
  if (props.mejora.porcentaje < 0) return 'En baja'
  return 'Estable'
})

// NUEVO: Computed property para el puntaje promedio
const puntajePromedio = computed(() => {
  const datos = props.mejora.datos
  if (!datos || datos.length === 0) return 0
  const sum = datos.reduce((a, b) => a + b, 0)
  return Math.round(sum / datos.length)
})

// NUEVO: Computed property para el puntaje máximo
const puntajeMaximo = computed(() => {
  const datos = props.mejora.datos
  if (!datos || datos.length === 0) return 0
  return Math.max(...datos)
})


// --- RENDERIZADO DEL GRÁFICO ---

const renderMiniChart = () => {
  if (!miniChart.value) return
  const ctx = miniChart.value.getContext('2d')
  
  const gradient = ctx.createLinearGradient(0, 0, 0, 90)
  gradient.addColorStop(0, 'rgba(56, 161, 105, 0.25)')
  gradient.addColorStop(1, 'rgba(56, 161, 105, 0)')

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: props.mejora.datos.map((_, i) => `Ensayo ${i + 1}`),
      datasets: [
        // NUEVO: Dataset para la línea de promedio
        {
          label: 'Promedio',
          data: Array(props.mejora.datos.length).fill(puntajePromedio.value),
          borderColor: '#a0aec0', // Color gris neutro
          borderWidth: 1.5,
          borderDash: [5, 5], // Línea punteada
          pointRadius: 0,
          fill: false,
          tension: 0.4,
        },
        // Dataset principal del rendimiento
        {
          label: 'Puntaje',
          data: props.mejora.datos,
          borderColor: '#38a169',
          backgroundColor: gradient,
          borderWidth: 2.5,
          // NUEVO: Hacemos visibles el primer y último punto
          pointRadius: (context) => {
             const index = context.dataIndex;
             const count = context.chart.data.labels.length;
             return index === 0 || index === count - 1 ? 4 : 0;
          },
          pointBackgroundColor: '#38a169',
          pointHoverRadius: 6,
          pointHoverBorderWidth: 2,
          pointHoverBorderColor: '#fff',
          tension: 0.4,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          enabled: true,
          mode: 'index',
          intersect: false,
          backgroundColor: '#2d3748',
          titleColor: '#fff',
          bodyColor: '#cbd5e0',
          titleFont: { size: 12, weight: 'bold' },
          bodyFont: { size: 12 },
          padding: 10,
          cornerRadius: 8,
          // Mostramos ambos datasets en el tooltip
          displayColors: true,
          callbacks: {
            labelColor: function(context) {
              if (context.datasetIndex === 0) { // Color para la línea de promedio
                return {
                  borderColor: '#a0aec0',
                  backgroundColor: '#a0aec0',
                  borderDash: [5, 5],
                };
              }
              return { // Color para la línea de puntaje
                borderColor: '#38a169',
                backgroundColor: '#38a169',
              };
            },
          }
        }
      },
      scales: {
        x: { display: false },
        y: { display: false }
      },
      layout: {
        padding: { top: 10, bottom: 5, left: 5, right: 5 }
      }
    }
  })
}

onMounted(() => {
  renderMiniChart()
})
</script>

<style scoped>
.mini-chart-container {
  width: 100%;
  height: 90px;
  margin: 0.5rem 0 1.2rem 0;
  background: transparent;
}
.mini-chart-container canvas {
  width: 100% !important;
  height: 90px !important;
}

.metric-value {
  font-size: 2.1rem;
  font-weight: 700;
  color: #38a169;
  margin-bottom: 0.25rem;
  letter-spacing: -1px;
}

.metric-label {
  color: #4a5568;
  font-size: 1rem;
  margin-bottom: 0.75rem;
}

/* --- NUEVA SECCIÓN DE ESTILOS PARA LA GRILLA DE ESTADÍSTICAS --- */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem 1.5rem;
  margin-bottom: 0.5rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.stat-text {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #2d3748;
  line-height: 1.2;
}

.stat-label {
  font-size: 0.8rem;
  color: #718096;
}

.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  padding: 0.35rem; /* Un poco más de padding */
  flex-shrink: 0;
}
.stat-icon.positive {
  background: #f0fff4;
  color: #38a169;
}
.stat-icon.negative {
  background: #fff5f5;
  color: #e53e3e;
}
.stat-icon.neutral {
  background: #edf2f7;
  color: #718096;
}
</style>