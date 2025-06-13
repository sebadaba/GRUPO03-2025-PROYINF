<template>
    <Card title="Ensayos Rendidos" subtitle="Progreso hacia tu meta" :icon="FileText" icon-background="#e6fffa"
        icon-color="#00a693" grid-area="ensayos-rendidos" :expandable="true" @expand="handleExpand"
        class="ensayos-card">
        <!-- Contenido principal -->
        <template #main-content>
            <div class="metric-value">{{ ensayosRendidos }}</div>
            <div class="metric-label">Total completados</div>

            <div class="progress-section">
                <div class="progress-bar">
                    <div class="progress-fill" :style="{
                        width: `${progressPercentage}%`,
                        background: 'linear-gradient(90deg, #00a693, #38d9a9)'
                    }"></div>
                </div>
                <div class="goal-text">
                    <Target :size="14" class="inline mr-1" />
                    Objetivo: {{ metaEnsayos }} ensayos
                </div>
            </div>

            <div class="quick-stats">
                <div class="stat-item">
                    <Clock :size="16" />
                    <span>Último: {{ ultimoEnsayo }}</span>
                </div>
            </div>
        </template>

        <!-- Contenido expandido -->
        <template #expanded-content>
            <div class="expanded-content">
                <h4 class="section-title">Detalles de Rendimiento</h4>

                <!-- Estadísticas detalladas -->
                <div class="detailed-stats">
                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <CheckCircle :size="20" />
                        </div>
                        <div class="stat-info">
                            <div class="stat-number">{{ ensayosRendidos }}</div>
                            <div class="stat-label">Completados</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <Clock :size="20" />
                        </div>
                        <div class="stat-info">
                            <div class="stat-number">{{ ensayosPendientes }}</div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>


                    <div class="stat-card">
                        <div class="stat-icon average">
                            <TrendingUp :size="20" />
                        </div>
                        <div class="stat-info">
                            <div class="stat-number">{{ promedioSemanal }}</div>
                            <div class="stat-label">Por semana</div>
                        </div>
                    </div>
                </div>


                <div class="recent-history">
                    <h5 class="subsection-title">Ensayos Recientes</h5>
                    <div class="history-list">
                        <div v-for="ensayo in historialReciente" :key="ensayo.id" class="history-item">
                            <div class="history-icon">
                                <FileText :size="16" />
                            </div>
                            <div class="history-info">
                                <div class="history-title">{{ ensayo.nombre }}</div>
                                <div class="history-date">{{ ensayo.fecha }}</div>
                            </div>
                            <div class="history-score">
                                <span class="score-value">{{ ensayo.puntaje }}</span>
                                <div class="score-change" :class="ensayo.cambio > 0 ? 'positive' : 'negative'">
                                    <component :is="ensayo.cambio > 0 ? TrendingUp : TrendingDown" :size="12" />
                                    {{ Math.abs(ensayo.cambio) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Footer con acciones -->
        <template #footer-content>
            <div class="action-buttons">
                <button 
                    class="action-btn primary" 
                    @click="verHistorialCompleto"
                    @mouseenter="isHovered = true"
                    @mouseleave="isHovered = false"
                >
                    <History :size="16" />
                    <span>Ver historial completo</span>
                </button>
            </div>
        </template>
    </Card>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import Card from '../Card.vue'
import {
    FileText, Target, Calendar, Clock, CheckCircle, Zap,
    TrendingUp, TrendingDown, Lightbulb, Plus, History
} from 'lucide-vue-next'

// Props
const props = defineProps({
    ensayosRendidos: {
        type: Number,
        default: 12
    },
    metaEnsayos: {
        type: Number,
        default: 15
    },
    ultimoEnsayo: {
        type: String,
        default: 'hace 3 días'
    }
})

// Datos computados y reactivos
const ensayosPendientes = computed(() => props.metaEnsayos - props.ensayosRendidos)
const progressPercentage = computed(() => (props.ensayosRendidos / props.metaEnsayos) * 100)

const diasPromedio = ref(4)
const rachaConsecutiva = ref(3)
const promedioSemanal = ref(2.5)
const proyeccionActual = ref(18)
const ensayosPorSemana = ref(1.5)

const recomendacion = computed(() => {
    if (progressPercentage.value >= 80) {
        return "¡Excelente ritmo! Mantén la constancia."
    } else if (progressPercentage.value >= 60) {
        return "Buen progreso, podrías aumentar ligeramente el ritmo."
    } else {
        return "Considera aumentar la frecuencia para alcanzar tu meta."
    }
})

// Calendario de ensayos (últimos 14 días)
const calendarioEnsayos = ref([
    { fecha: '2024-06-01', dia: 1, tieneEnsayo: true, completado: true, esHoy: false, esFuturo: false },
    { fecha: '2024-06-02', dia: 2, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: false },
    { fecha: '2024-06-03', dia: 3, tieneEnsayo: true, completado: true, esHoy: false, esFuturo: false },
    { fecha: '2024-06-04', dia: 4, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: false },
    { fecha: '2024-06-05', dia: 5, tieneEnsayo: true, completado: true, esHoy: false, esFuturo: false },
    { fecha: '2024-06-06', dia: 6, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: false },
    { fecha: '2024-06-07', dia: 7, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: false },
    { fecha: '2024-06-08', dia: 8, tieneEnsayo: true, completado: true, esHoy: false, esFuturo: false },
    { fecha: '2024-06-09', dia: 9, tieneEnsayo: false, completado: false, esHoy: true, esFuturo: false },
    { fecha: '2024-06-10', dia: 10, tieneEnsayo: true, completado: false, esHoy: false, esFuturo: true },
    { fecha: '2024-06-11', dia: 11, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: true },
    { fecha: '2024-06-12', dia: 12, tieneEnsayo: true, completado: false, esHoy: false, esFuturo: true },
    { fecha: '2024-06-13', dia: 13, tieneEnsayo: false, completado: false, esHoy: false, esFuturo: true },
    { fecha: '2024-06-14', dia: 14, tieneEnsayo: true, completado: false, esHoy: false, esFuturo: true }
])

// Historial reciente
const historialReciente = ref([
    { id: 1, nombre: 'Ensayo Nacional #12', fecha: '9 Jun 2024', puntaje: 695, cambio: 15 },
    { id: 2, nombre: 'Ensayo Nacional #11', fecha: '5 Jun 2024', puntaje: 680, cambio: -5 },
    { id: 3, nombre: 'Ensayo Nacional #10', fecha: '1 Jun 2024', puntaje: 685, cambio: 20 },
    { id: 4, nombre: 'Ensayo Nacional #9', fecha: '28 May 2024', puntaje: 665, cambio: 10 }
])

// Referencias
const projectionChart = ref(null)

// Eventos


const onCollapsed = () => {
    console.log('Tarjeta de ensayos colapsada')
}
const router = useRouter()
const isHovered = ref(false)
const isExpanded = ref(false)

const handleExpand = (expanded) => {
    isExpanded.value = expanded
    console.log('Card expanded:', expanded)
}

const verHistorialCompleto = (event) => {
    event.stopPropagation() // Previene que el click se propague a la tarjeta
    router.push('/historial')
}
</script>

<style scoped>
/* Contenido principal */
.metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 8px;
}

.metric-label {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 16px;
}

.progress-section {
    margin-bottom: 20px;
}

.progress-bar {
    background: #e2e8f0;
    border-radius: 12px;
    height: 10px;
    margin: 12px 0;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-fill {
    height: 100%;
    border-radius: 12px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(100%);
    }
}

.goal-text {
    color: #718096;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.quick-stats {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #718096;
    font-size: 0.85rem;
}

/* Contenido expandido */
.expanded-content {
    animation: fadeInUp 0.4s ease-out;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
}

.detailed-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon.completed {
    background: #c6f6d5;
    color: #22543d;
}

.stat-icon.pending {
    background: #fed7d7;
    color: #742a2a;
}

.stat-icon.streak {
    background: #fef5e7;
    color: #d69e2e;
}

.stat-icon.average {
    background: #e6f3ff;
    color: #3182ce;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: bold;
    color: #2d3748;
}

.stat-label {
    font-size: 0.8rem;
    color: #718096;
}

.subsection-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 15px;
}



/* Historial */
.recent-history {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: 8px;
    transition: transform 0.2s;
}

.history-item:hover {
    transform: translateX(4px);
}

.history-icon {
    background: #e6f3ff;
    color: #3182ce;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.history-info {
    flex: 1;
}

.history-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: #2d3748;
}

.history-date {
    font-size: 0.8rem;
    color: #718096;
}

.history-score {
    text-align: right;
}

.score-value {
    font-size: 1.1rem;
    font-weight: bold;
    color: #2d3748;
}

.score-change {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    margin-top: 2px;
}

.score-change.positive {
    color: #38a169;
}

.score-change.negative {
    color: #e53e3e;
}

/* Acciones del footer */
.action-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn.primary {
    background: #e6fffa;
    color: #00a693;
    border: 1px solid #b2f5ea;
}

.action-btn.primary:hover {
    background: #b2f5ea;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 166, 147, 0.1);
}

/* Efecto de elevación en hover */
.ensayos-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.ensayos-card:hover:not(:has(.action-btn:hover)) {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
              0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Cuando el botón está en hover, la tarjeta vuelve a su estado normal */
.ensayos-card:has(.action-btn:hover) {
  transform: none;
  box-shadow: none;
}

/* Utilidades */
.inline {
    display: inline;
}

.mr-1 {
    margin-right: 0.25rem;
}

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
    .detailed-stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .projection-content {
        grid-template-columns: 1fr;
    }

    .calendar-grid {
        gap: 4px;
    }

    .action-buttons {
        flex-direction: column;
    }
}
</style>