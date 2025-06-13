<template>
  <div class="filter-group relative">
    <label v-if="label">{{ label }}</label>
    <div class="select-wrapper">
      <select 
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="filter-select"
      >
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option 
          v-for="option in options" 
          :key="option.value" 
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      <ChevronDown class="lucide-chevron-icon" :size="20" />
    </div>
  </div>
</template>

<script setup>
import { ChevronDown } from 'lucide-vue-next'

defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Seleccionar...'
  },
  options: {
    type: Array,
    required: true,
    // Expecting array of: { value: string|number, label: string }
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  min-width: 160px;
}

.filter-group label {
  font-weight: 600;
  font-size: 0.875rem;
  color: #1f2937;
  letter-spacing: 0.025em;
}

.select-wrapper {
  position: relative;
  width: 100%;
}

.filter-select {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid rgba(229, 231, 235, 0.8);
  border-radius: 12px;
  font-size: 0.95rem;
  background: rgba(255, 255, 255, 0.9);
  appearance: none;
  padding-right: 2.5rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.filter-select:focus {
  border-color: #0ea5e9;
  background: white;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
  outline: none;
}

.filter-select:hover {
  border-color: #9ca3af;
  background: white;
  transform: translateY(-1px);
}

.lucide-chevron-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  pointer-events: none;
}
</style>