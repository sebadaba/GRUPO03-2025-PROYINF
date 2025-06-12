<template>
  <div class="login-view">
    <div class="header">
      <h1>Ensayo PAES</h1>
      <p class="subtitle">Plataforma de preparación para la Prueba de Acceso a la Educación Superior</p>
    </div>

    <div class="login-container">
      <h2>Iniciar Sesión</h2>
      <p>Usa tu cuenta institucional para acceder</p>

      <div class="login-options">
        <button @click="loginAsStudent" class="cssbuttons-io-button student-btn">
          Acceder como Estudiante
          <div class="icon">
            <svg height="24" width="24" viewBox="0 0 24 24">
              <path d="M0 0h24v24H0z" fill="none"></path>
              <path
                d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"
                fill="currentColor"
              ></path>
            </svg>
          </div>
        </button>

        <button @click="loginAsTeacher" class="cssbuttons-io-button teacher-btn">
          Acceder como Docente
          <div class="icon">
            <svg height="24" width="24" viewBox="0 0 24 24">
              <path d="M0 0h24v24H0z" fill="none"></path>
              <path
                d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z"
                fill="currentColor"
              ></path>
            </svg>
          </div>
        </button>
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>

      <div v-if="isLoading" class="loading-message">
        Iniciando sesión...
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const error = ref("");
const isLoading = ref(false);

// Función para obtener datos del profesor desde la API
const fetchTeacherData = async (teacherId) => {
  try {
    // Por ahora, simulamos la respuesta
    return {
      id: teacherId,
      name: "Usuario de Prueba",
      email: "usuario@institucion.edu",
      role: "teacher",
      subject: "Matemáticas",
      subjectId: 1,
    };
  } catch (error) {
    console.error("Error al obtener datos del profesor:", error);
    throw new Error("No se pudieron obtener los datos del profesor");
  }
};

// Función para iniciar sesión como estudiante
const loginAsStudent = async () => {
  isLoading.value = true;
  error.value = "";

  try {
    // Para estudiantes, guardamos información básica
    localStorage.setItem(
      "user",
      JSON.stringify({
        name: "Usuario de Prueba",
        email: "usuario@institucion.edu",
        isAuthenticated: true,
        role: "student",
      })
    );

    router.push("/home");
  } catch (err) {
    error.value = "Error al iniciar sesión. Intenta nuevamente.";
  } finally {
    isLoading.value = false;
  }
};

// Función para iniciar sesión como profesor
const loginAsTeacher = async () => {
  isLoading.value = true;
  error.value = "";

  try {
    // Para profesores, obtenemos datos adicionales
    const teacherData = await fetchTeacherData("123");

    // Guardamos información completa del profesor en localStorage
    localStorage.setItem("user", JSON.stringify(teacherData));

    router.push("/teacher");
  } catch (err) {
    error.value = "Error al iniciar sesión. Intenta nuevamente.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
.login-view {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
  min-height: 100vh;
  padding-top: 4rem;
}

.header {
  margin-bottom: 3rem;
}

h1 {
  margin-bottom: 0.5rem;
  color: #3498db;
  font-size: 2.5rem;
}

.subtitle {
  margin-bottom: 0;
  color: #666;
  font-size: 1.1rem;
}

.login-container {
  max-width: 400px;
  width: 100%;
  margin: 0 auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 12px;
  background-color: white;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.login-container h2 {
  color: #333;
  margin-bottom: 0.5rem;
}

.login-container p {
  color: #666;
  margin-bottom: 2rem;
}

.login-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.cssbuttons-io-button {
  background: rgb(52, 152, 219);
  color: white;
  font-family: inherit;
  padding: 0.35em;
  padding-left: 1.2em;
  font-size: 17px;
  font-weight: 500;
  border-radius: 0.9em;
  border: none;
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  box-shadow: inset 0 0 1.6em -0.6em rgb(52, 152, 219);
  overflow: hidden;
  position: relative;
  height: 2.8em;
  padding-right: 3.3em;
  cursor: pointer;
  width: 100%;
  justify-content: flex-start;
  transition: all 0.3s ease;
}

.cssbuttons-io-button:hover {
  transform: translateY(-2px);
  box-shadow: inset 0 0 1.6em -0.6em rgb(52, 152, 219), 0 4px 15px rgba(52, 152, 219, 0.3);
}

.cssbuttons-io-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.cssbuttons-io-button .icon {
  background: white;
  margin-left: 1em;
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2.2em;
  width: 2.2em;
  border-radius: 0.7em;
  box-shadow: 0.1em 0.1em 0.6em 0.2em rgb(52, 152, 219);
  right: 0.3em;
  transition: all 0.3s;
}

.cssbuttons-io-button:hover .icon {
  width: calc(100% - 0.6em);
}

.cssbuttons-io-button .icon svg {
  width: 1.1em;
  transition: transform 0.3s;
  color: rgb(52, 152, 219);
}

.cssbuttons-io-button:hover .icon svg {
  transform: translateX(0.1em);
}

.cssbuttons-io-button:active .icon {
  transform: scale(0.95);
}

.teacher-btn {
  background: rgb(46, 204, 113);
  box-shadow: inset 0 0 1.6em -0.6em rgb(46, 204, 113);
}

.teacher-btn:hover {
  box-shadow: inset 0 0 1.6em -0.6em rgb(46, 204, 113), 0 4px 15px rgba(46, 204, 113, 0.3);
}

.teacher-btn .icon {
  box-shadow: 0.1em 0.1em 0.6em 0.2em rgb(46, 204, 113);
}

.teacher-btn .icon svg {
  color: rgb(46, 204, 113);
}

.error-message {
  color: #e74c3c;
  margin-top: 1rem;
  padding: 0.8rem;
  background-color: #fdf2f2;
  border: 1px solid #f5c6c6;
  border-radius: 6px;
  font-size: 0.9rem;
}

.loading-message {
  color: #3498db;
  margin-top: 1rem;
  padding: 0.8rem;
  background-color: #f0f8ff;
  border: 1px solid #b8daff;
  border-radius: 6px;
  font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-view {
    padding: 1rem;
  }
  
  h1 {
    font-size: 2rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .login-container {
    padding: 1.5rem;
  }
}

@media (max-width: 500px) {
  .login-view {
    padding: 0.5rem;
  }
  
  .login-container {
    padding: 1rem;
    border: none;
    box-shadow: none;
    max-width: 100%;
  }
  
  .cssbuttons-io-button {
    font-size: 0.95rem;
    padding-left: 0.8em;
    padding-right: 2.5em;
  }
  
  h1 {
    font-size: 1.8rem;
  }
  
  .login-container h2 {
    font-size: 1.3rem;
  }
}

@media (max-width: 350px) {
  .cssbuttons-io-button {
    font-size: 0.85rem;
    padding-left: 0.5em;
    padding-right: 1.5em;
  }
}
</style>