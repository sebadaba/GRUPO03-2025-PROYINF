<template>
  <Card
    title="Ponderación Actual"
    :icon="Target"
    icon-background="#e6f3ff"
    icon-color="#3182ce"
    grid-area="ponderacion"
  >
    <template #main-content>
      <div class="metric-value">{{ ponderacion.puntaje }}</div>
      <div class="metric-label">Puntaje ponderado</div>
      <div class="weights-list">
        <div v-for="item in ponderacion.detalle" :key="item.nombre" class="weight-item">
          <Calculator :size="14" class="mr-2" />
          {{ item.nombre }}: {{ item.valor }}
        </div>
      </div>
    </template>
  </Card>
</template>

<script setup>
import Card from '../Card.vue'
import { Target, Calculator } from 'lucide-vue-next'

defineProps({
  ponderacion: {
    type: Object,
    required: true,
    default: () => ({
      puntaje: 0,
      detalle: []
    })
  }
})
</script>

<style scoped>
.metric-value { font-size: 2.5rem; font-weight: bold; color: #2d3748; margin-bottom: 10px; }
.metric-label { color: #718096; font-size: 0.9rem; }
.weights-list { margin-top: 15px; font-size: 0.9rem; color: #718096; }
.weight-item { display: flex; align-items: center; padding: 4px 0; }
.mr-2 { margin-right: 0.5rem; }
</style>