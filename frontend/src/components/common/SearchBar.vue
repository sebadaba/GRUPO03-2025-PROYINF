<template>
  <div class="search-group relative">
    <label v-if="label">{{ label }}</label>
    <div class="search-wrapper">
      <Search class="lucide-search-icon" :size="20" />
      <input
        type="text"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        class="search-input"
      >
    </div>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'

defineProps({
  modelValue: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Buscar...'
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
.search-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  flex: 1;
  min-width: 250px;
}

.search-group label {
  font-weight: 600;
  font-size: 0.875rem;
  color: #1f2937;
  letter-spacing: 0.025em;
}

.search-wrapper {
  position: relative;
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  border: 2px solid rgba(229, 231, 235, 0.8);
  border-radius: 12px;
  font-size: 0.95rem;
  background: rgba(255, 255, 255, 0.9);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-input:focus {
  border-color: #0ea5e9;
  background: white;
  box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
  outline: none;
}

.search-input:hover {
  border-color: #9ca3af;
  background: white;
  transform: translateY(-1px);
}

.search-input::placeholder {
  color: #9ca3af;
  font-style: italic;
}

.lucide-search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
  pointer-events: none;
}
</style>