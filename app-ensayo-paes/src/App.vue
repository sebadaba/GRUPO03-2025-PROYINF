<template>
  <h1>Sistema de ensayo PAES</h1>
  <div class="container">
    <MenuRoles v-if="vista === 'menu'" @comenzar="vista = 'ensayo'" />

    <Ensayo
      v-else-if="vista === 'ensayo'"
      :indice="indicePregunta"
      :respuestas="respuestas"
      :total="totalPreguntas"
      @siguiente="siguientePregunta"
      @anterior="anteriorPregunta"
      @terminar="terminarEnsayo"
    />

    <Resumen
      v-else-if="vista === 'resumen'"
      :respuestas="respuestas"
      :puntaje="puntajeFinal"
      :total="totalPreguntas"
      @reiniciar="reiniciarEnsayo"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import MenuRoles from "./components/MenuRoles.vue";
import Ensayo from "./components/Ensayo.vue";
import Resumen from "./components/Resumen.vue";

const vista = ref("menu");
const totalPreguntas = 3;
const indicePregunta = ref(0);
const respuestas = ref(Array(totalPreguntas).fill(null));

const siguientePregunta = () => {
  if (indicePregunta.value < totalPreguntas - 1) {
    indicePregunta.value++;
  }
};

const anteriorPregunta = () => {
  if (indicePregunta.value > 0) {
    indicePregunta.value--;
  }
};

const terminarEnsayo = () => {
  vista.value = "resumen";
};

const puntajeFinal = computed(
  () => respuestas.value.filter((r) => r === 1).length
);

const reiniciarEnsayo = () => {
  respuestas.value = Array(totalPreguntas).fill(null);
  indicePregunta.value = 0;
  vista.value = "menu";
};
</script>

<style scoped>
.container {
  width: 100%;
  max-width: 800px;
  min-height: 500px;
  margin: 2rem auto;
  padding: 1.5rem;
  font-family: sans-serif;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
</style>
