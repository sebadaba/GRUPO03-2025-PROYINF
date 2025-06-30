<template>
  <Card
    title="Rendimiento por Asignatura"
    :icon="BookOpen"
    icon-background="#e6f3ff"
    icon-color="#3182ce"
    grid-area="detalle-asignaturas"
  >
    <template #main-content>
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
    </template>
  </Card>
</template>

<script setup>
import Card from '../Card.vue'
import { BookOpen, TrendingUp, TrendingDown, Minus, Calculator as MathIcon, BookA, Clock, Atom } from 'lucide-vue-next'

defineProps({
  asignaturas: {
    type: Array,
    required: true,
    default: () => []
  }
})

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
</script>

<style scoped>
.subjects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
.subject-card { background: #f7fafc; border-radius: 12px; padding: 15px; text-align: center; }
.subject-score { font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; }
.improvement-indicator { display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; margin-top: 10px; }
.positive { background: #c6f6d5; color: #22543d; }
.negative { background: #fed7d7; color: #742a2a; }
.neutral { background: #e2e8f0; color: #4a5568; }

.flex { display: flex; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.mr-1 { margin-right: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
</style>