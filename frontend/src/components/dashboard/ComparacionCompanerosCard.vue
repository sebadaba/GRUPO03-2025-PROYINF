<template>
  <Card
    title="Comparación con Compañeros"
    :icon="Users"
    icon-background="#edf2f7"
    icon-color="#4a5568"
    grid-area="comparacion-companeros"
  >
    <template #main-content>
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
          Tu posición
        </span>
        <strong style="color: #38a169;">{{ comparacion.posicion }}</strong>
      </div>
    </template>
  </Card>
</template>

<script setup>
import Card from '../Card.vue'
import { Users, User, BarChart, Crown, Medal } from 'lucide-vue-next'

defineProps({
  comparacion: {
    type: Object,
    required: true,
    default: () => ({
      tuPuntaje: 0,
      promedioCurso: 0,
      mejorCurso: 0,
      posicion: 'N/A'
    })
  },
  icon: {
    type: [String, Object, Function],
    default: Users
  }
})
</script>

<style scoped>

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

.flex { display: flex; }
.items-center { align-items: center; }
.mr-2 { margin-right: 0.5rem; }
</style>