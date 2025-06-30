<template>
  <Card
    title="Tipos de Preguntas Más Erradas"
    :icon="AlertCircle"
    icon-background="#fed7d7"
    icon-color="#e53e3e"
    grid-area="tipos-errores"
  >
    <template #main-content>
      <div class="error-type" v-for="err in errores" :key="err.tipo">
        <span class="flex items-center">
          <XCircle :size="16" class="mr-2 text-red-500" />
          {{ err.tipo }}
        </span>
        <div class="error-percentage">{{ err.porcentaje }}%</div>
      </div>
    </template>
  </Card>
</template>

<script setup>
import Card from '../Card.vue'
import { AlertCircle, XCircle } from 'lucide-vue-next'

defineProps({
  errores: {
    type: Array,
    required: true,
    default: () => []
  }
})
</script>

<style scoped>
.error-type { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
.error-type:last-child { border-bottom: none; }
.error-percentage { background: #fed7d7; color: #742a2a; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500; }

.flex { display: flex; }
.items-center { align-items: center; }
.mr-2 { margin-right: 0.5rem; }
.text-red-500 { color: #ef4444; }
</style>