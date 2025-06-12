<template>
  <div class="question-display">
    <!-- Pregunta individual -->
    <div v-for="(question, index) in questions" :key="question.id" class="question-item">
      <!-- Número de pregunta y metadatos -->
      <div class="question-header">
        <div class="question-number">
          Pregunta {{ startIndex + index + 1 }}
        </div>
        <div class="question-meta">
          <span class="question-subject">{{ question.subject }}</span>
          <span class="question-difficulty" :class="question.difficulty.toLowerCase()">
            {{ question.difficulty }}
          </span>
        </div>
      </div>
      
      <!-- Contenido de la pregunta -->
      <div class="question-content">
        <p>{{ question.text }}</p>
      </div>
      
      <!-- Opciones de respuesta -->
      <div class="question-options">
        <div 
          v-for="(option, optionIndex) in question.options" 
          :key="optionIndex" 
          class="option" 
          :class="{ 
            correct: showCorrectAnswers && optionIndex === question.correctOption,
            selected: selectedAnswers && selectedAnswers[question.id] === optionIndex,
            disabled: disabled
          }"
          @click="!disabled && handleOptionClick(question.id, optionIndex)"
        >
          <span class="option-letter">{{ ['A', 'B', 'C', 'D'][optionIndex] }}</span>
          <span class="option-text">{{ option }}</span>
          
          <!-- Indicador de selección -->
          <div v-if="selectedAnswers && selectedAnswers[question.id] === optionIndex" 
               class="selection-indicator">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>
      
      <!-- Información adicional (solo si se muestran respuestas correctas) -->
      <div v-if="showCorrectAnswers" class="question-feedback feedback-actions-row">
        <div class="correct-answer">
          <strong>Respuesta correcta:</strong> {{ ['A', 'B', 'C', 'D'][question.correctOption] }} - {{ question.options[question.correctOption] }}
        </div>
        <div class="question-actions inline-actions">
          <slot name="actions" :question="question" :index="index"></slot>
        </div>
      </div>
    </div>
    
    <!-- Estado vacío -->
    <div v-if="questions.length === 0" class="no-questions">
      <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3>No hay preguntas disponibles</h3>
        <p>{{ emptyMessage || 'No se encontraron preguntas para mostrar.' }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// Props del componente
const props = defineProps({
  // Preguntas a mostrar
  questions: {
    type: Array,
    default: () => []
  },
  
  // Respuestas seleccionadas (objeto con questionId: optionIndex)
  selectedAnswers: {
    type: Object,
    default: null
  },
  
  // Mostrar respuestas correctas
  showCorrectAnswers: {
    type: Boolean,
    default: false
  },
  
  // Deshabilitar interacción
  disabled: {
    type: Boolean,
    default: false
  },
  
  // Índice de inicio para numeración
  startIndex: {
    type: Number,
    default: 0
  },
  
  // Mensaje personalizado cuando no hay preguntas
  emptyMessage: {
    type: String,
    default: ''
  },
  
  // Modo de visualización ('exam', 'review', 'preview')
  mode: {
    type: String,
    default: 'preview',
    validator: (value) => ['exam', 'review', 'preview'].includes(value)
  }
});

// Eventos emitidos
const emit = defineEmits(['answer-selected', 'question-viewed']);

// Función para manejar selección de opciones
const handleOptionClick = (questionId, optionIndex) => {
  if (props.disabled) return;
  
  emit('answer-selected', {
    questionId,
    optionIndex,
    question: props.questions.find(q => q.id === questionId)
  });
};

// Función para notificar cuando una pregunta es vista
const handleQuestionViewed = (questionId) => {
  emit('question-viewed', questionId);
};
</script>

<style scoped>
.question-display {
  width: 100%;
  display: grid;
  gap: 2rem;
}

/* Estilos de pregunta individual */
.question-item {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.question-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;

}

.question-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Header de la pregunta */
.question-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.question-number {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1f2937;
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.question-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.question-subject {
  font-weight: 600;
  color: #374151;
  font-size: 0.95rem;
  padding: 0.5rem 1rem;
  background: rgba(14, 165, 233, 0.1);
  border-radius: 20px;
  border: 1px solid rgba(14, 165, 233, 0.2);
}

.question-difficulty {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 600;
  white-space: nowrap;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.fácil {
  background: linear-gradient(45deg, #10b981, #34d399);
  color: white;
}

.media {
  background: linear-gradient(45deg, #f59e0b, #fbbf24);
  color: white;
}

.difícil {
  background: linear-gradient(45deg, #ef4444, #f87171);
  color: white;
}

/* Contenido de la pregunta */
.question-content {
  margin-bottom: 2rem;
}

.question-content p {
  font-size: 1.1rem;
  line-height: 1.6;
  margin: 0;
  color: #374151;
  font-weight: 500;
}

/* Opciones de respuesta */
.question-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.option {
  display: flex;
  align-items: center;
  padding: 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(4px);
  cursor: pointer;
  position: relative;
  user-select: none;
}

.option:hover:not(.disabled) {
  background: rgba(248, 250, 252, 0.8);
  border-color: #0ea5e9;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
}

.option.selected {
  background: rgba(14, 165, 233, 0.1);
  border-color: #0ea5e9;
  box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
}

.option.correct {
  background: rgba(16, 185, 129, 0.1);
  border-color: #10b981;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.option.disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

.option-letter {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: linear-gradient(45deg, #6b7280, #9ca3af);
  color: white;
  border-radius: 50%;
  margin-right: 1rem;
  font-weight: 700;
  font-size: 1rem;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
  transition: all 0.3s ease;
}

.option:hover:not(.disabled) .option-letter,
.option.selected .option-letter {
  background: linear-gradient(45deg, #0ea5e9, #06b6d4);
  box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
}

.option.correct .option-letter {
  background: linear-gradient(45deg, #10b981, #34d399);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.option-text {
  flex: 1;
  line-height: 1.5;
  color: #374151;
  font-size: 0.95rem;
  font-weight: 500;
}

/* Indicador de selección */
.selection-indicator {
  display: flex;
  align-items: center;
  color: #0ea5e9;
  margin-left: 1rem;
  flex-shrink: 0;
}

.option.correct .selection-indicator {
  color: #10b981;
}

/* Feedback de la pregunta */
.question-feedback {
  margin-top: 1.5rem;
  padding: 1.25rem;
  background: rgba(16, 185, 129, 0.05);
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 12px;
}

.correct-answer {
  color: #065f46;
  font-size: 0.95rem;
  line-height: 1.5;
}

.correct-answer strong {
  color: #047857;
}

/* Estado vacío */
.no-questions {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.empty-state {
  text-align: center;
  color: #6b7280;
  max-width: 400px;
  padding: 2rem;
}

.empty-state svg {
  margin: 0 auto 1.5rem;
  color: #9ca3af;
}

.empty-state h3 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
}

.empty-state p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 768px) {
  .question-item {
    padding: 1.5rem;
  }
  
  .question-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.75rem;
  }
  
  .question-meta {
    width: 100%;
    justify-content: space-between;
  }
  
  .question-options {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .option {
    padding: 1rem;
  }
  
  .option-letter {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
  }
}

@media (max-width: 480px) {
  .question-display {
    gap: 1.5rem;
  }
  
  .question-item {
    padding: 1.25rem;
    border-radius: 12px;
  }
  
  .question-content p {
    font-size: 1rem;
  }
  
  .option {
    padding: 0.875rem;
  }
  
  .option-text {
    font-size: 0.875rem;
  }
}

/* Animaciones */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.question-item {
  animation: slideIn 0.3s ease-out;
}

.question-item:nth-child(n) {
  animation-delay: calc(0.1s * var(--index, 0));
}

.feedback-actions-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 1.5rem;
  padding: 1.25rem;
  background: rgba(16, 185, 129, 0.05);
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 12px;
}

.inline-actions {
  display: flex;
  gap: 0.75rem;
  margin: 0;
}

.question-actions.bottom-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}
</style>