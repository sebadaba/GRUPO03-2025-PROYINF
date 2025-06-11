<template>
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
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const error = ref("");
const userRole = ref("student");
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

// Función simulada de login (no se usa actualmente)
const loginWithGoogle = async () => {
  isLoading.value = true;
  error.value = "";

  try {
    // Simulamos un proceso de autenticación exitoso

    if (userRole.value === "teacher") {
      // Para profesores, obtenemos datos adicionales de la API
      const teacherData = await fetchTeacherData("123");

      // Guardamos información completa del profesor en localStorage
      localStorage.setItem("user", JSON.stringify(teacherData));

      router.push("/teacher");
    } else {
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
    }
  } catch (err) {
    error.value = "Error al iniciar sesión. Intenta nuevamente.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
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
  margin-bottom: 8px;
  width: 100%;
  justify-content: flex-start;
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

.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.error-message {
  color: #e74c3c;
  margin-top: 1rem;
}
</style>
