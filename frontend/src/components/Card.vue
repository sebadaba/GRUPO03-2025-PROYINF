<template>
  <div 
    class="card" 
    :class="{ 
      'expanded': isExpanded,
      'expandable': expandable,
      [gridArea]: gridArea 
    }"
    @click="handleCardClick"
  >
    <!-- Header de la tarjeta -->
    <div class="card-header">
      <div class="card-icon" :style="iconStyle">
        <component :is="icon" :size="24" />
      </div>
      <div class="card-content-header">
        <div class="card-title">{{ title }}</div>
        <div v-if="subtitle" class="card-subtitle">{{ subtitle }}</div>
      </div>
      <div v-if="expandable" class="expand-indicator">
        <ChevronDown 
          :size="20" 
          class="expand-icon" 
          :class="{ 'rotated': isExpanded }"
        />
      </div>
    </div>

    <!-- Contenido principal (siempre visible) -->
    <div class="card-main-content">
      <slot name="main-content" />
    </div>

    <!-- Contenido expandido (solo visible cuando está expandido) -->
    <transition name="expand">
      <div v-if="isExpanded && expandable" class="card-expanded-content">
        <div class="expanded-divider"></div>
        <slot name="expanded-content" />
      </div>
    </transition>

    <!-- Contenido del footer (opcional) -->
    <div v-if="$slots['footer-content']" class="card-footer">
      <slot name="footer-content" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ChevronDown } from 'lucide-vue-next'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: [String, Object, Function], 
    required: true
  },
  iconBackground: {
    type: String,
    default: '#f0f4ff'
  },
  iconColor: {
    type: String,
    default: '#3182ce'
  },
  gridArea: {
    type: String,
    default: ''
  },
  expandable: {
    type: Boolean,
    default: false
  },
  defaultExpanded: {
    type: Boolean,
    default: false
  },
  clickToExpand: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['expanded', 'collapsed', 'click'])

// Estado de expansión
const isExpanded = ref(props.defaultExpanded)

// Estilo del icono computado
const iconStyle = computed(() => ({
  background: props.iconBackground,
  color: props.iconColor
}))

// Manejo del click en la tarjeta
const handleCardClick = () => {
  emit('click')
  
  if (props.expandable && props.clickToExpand) {
    toggleExpanded()
  }
}

// Función para alternar expansión
const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value
  
  if (isExpanded.value) {
    emit('expanded')
  } else {
    emit('collapsed')
  }
}

// Funciones públicas para control externo
const expand = () => {
  if (!isExpanded.value) {
    toggleExpanded()
  }
}

const collapse = () => {
  if (isExpanded.value) {
    toggleExpanded()
  }
}

// Exponer funciones para uso del componente padre
defineExpose({
  expand,
  collapse,
  toggleExpanded,
  isExpanded: computed(() => isExpanded.value)
})
</script>

<style scoped>
.card {
  background: linear-gradient(135deg, #fff 80%, #f0f4ff 100%);
  box-shadow: 0 4px 24px rgba(76, 110, 245, 0.08);
  border-radius: 24px;
  padding: 32px 24px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.card.expandable {
  cursor: pointer;
}

.card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 32px rgba(76, 110, 245, 0.15);
}

.card.expanded {
  transform: translateY(-8px) scale(1.03);
  box-shadow: 0 16px 40px rgba(76, 110, 245, 0.2);
  z-index: 10;
}

/* Header de la tarjeta */
.card-header {
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
  gap: 12px;
}

.card-icon {
  border-radius: 50%;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}

.card.expandable:hover .card-icon {
  transform: scale(1.1);
}

.card-content-header {
  flex: 1;
  min-width: 0;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2d3748;
  line-height: 1.3;
}

.card-subtitle {
  font-size: 0.9rem;
  color: #718096;
  margin-top: 4px;
}

.expand-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(76, 110, 245, 0.08);
  transition: all 0.2s ease;
}

.expand-indicator:hover {
  background: rgba(76, 110, 245, 0.15);
}

.expand-icon {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  color: #4c6ef5;
}

.expand-icon.rotated {
  transform: rotate(180deg);
}

/* Contenido principal */
.card-main-content {
  margin-bottom: 16px;
}

/* Contenido expandido */
.card-expanded-content {
  margin-top: 20px;
}

.expanded-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
  margin-bottom: 20px;
}

/* Footer */
.card-footer {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}

/* Transiciones para expansión */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
  opacity: 0;
  max-height: 0;
  margin-top: 0;
}

.expand-enter-to,
.expand-leave-from {
  opacity: 1;
  max-height: 500px;
  margin-top: 20px;
}

/* Clases de área del grid */
.promedio-general { grid-area: promedio; }
.ensayos-rendidos { grid-area: ensayos; }
.tasa-mejora { grid-area: mejora; }
.ponderacion { grid-area: ponderacion; }
.comparacion-companeros { grid-area: comparacion; }
.tipos-errores { grid-area: errores; }
.detalle-asignaturas { grid-area: detalle; }

/* Responsive */
@media (max-width: 768px) {
  .card {
    padding: 24px 20px;
  }
  
  .card-title {
    font-size: 1.1rem;
  }
  
  .card-icon {
    width: 40px;
    height: 40px;
  }
}
</style>