<template>
  <div class="login-container">
    <h2>Iniciar Sesión</h2>
    <p>Usa tu cuenta institucional para acceder</p>
    
    <div class="login-options">
      <button @click="loginAsStudent" class="login-btn student-btn">
        <div class="user-icon student-icon"></div>
        Acceder como Estudiante
      </button>
      
      <button @click="loginAsTeacher" class="login-btn teacher-btn">
        <div class="user-icon teacher-icon"></div>
        Acceder como Docente
      </button>
    </div>
    
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const error = ref('');
const userRole = ref('student');
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
      subjectId: 1
    };
  } catch (error) {
    console.error("Error al obtener datos del profesor:", error);
    throw new Error("No se pudieron obtener los datos del profesor");
  }
};

// Función para iniciar sesión como estudiante
const loginAsStudent = async () => {
  isLoading.value = true;
  error.value = '';
  
  try {
    // Para estudiantes, guardamos información básica
    localStorage.setItem('user', JSON.stringify({
      name: 'Usuario de Prueba',
      email: 'usuario@institucion.edu',
      isAuthenticated: true,
      role: 'student'
    }));
    
    router.push('/home');
  } catch (err) {
    error.value = 'Error al iniciar sesión. Intenta nuevamente.';
  } finally {
    isLoading.value = false;
  }
};

// Función para iniciar sesión como profesor
const loginAsTeacher = async () => {
  isLoading.value = true;
  error.value = '';
  
  try {
    // Para profesores, obtenemos datos adicionales
    const teacherData = await fetchTeacherData("123");
    
    // Guardamos información completa del profesor en localStorage
    localStorage.setItem('user', JSON.stringify(teacherData));
    
    router.push('/teacher');
  } catch (err) {
    error.value = 'Error al iniciar sesión. Intenta nuevamente.';
  } finally {
    isLoading.value = false;
  }
};

// Función simulada de login (no se usa actualmente)
const loginWithGoogle = async () => {
  isLoading.value = true;
  error.value = '';
  
  try {
    // Simulamos un proceso de autenticación exitoso
    
    if (userRole.value === 'teacher') {
      // Para profesores, obtenemos datos adicionales de la API
      const teacherData = await fetchTeacherData("123");
      
      // Guardamos información completa del profesor en localStorage
      localStorage.setItem('user', JSON.stringify(teacherData));
      
      router.push('/teacher');
    } else {
      // Para estudiantes, guardamos información básica
      localStorage.setItem('user', JSON.stringify({
        name: 'Usuario de Prueba',
        email: 'usuario@institucion.edu',
        isAuthenticated: true,
        role: 'student'
      }));
      
      router.push('/home');
    }
  } catch (err) {
    error.value = 'Error al iniciar sesión. Intenta nuevamente.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
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

.google-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 0.75rem;
  margin: 1.5rem 0;
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.google-btn:hover {
  background-color: #f5f5f5;
}

.google-icon {
  width: 18px;
  height: 18px;
  margin-right: 10px;
  background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0OCA0OCIgd2lkdGg9IjQ4cHgiIGhlaWdodD0iNDhweCI+PHBhdGggZmlsbD0iI0ZGQzEwNyIgZD0iTTQzLjYxMSwyMC4wODNINDJWMjBIMjR2OGgxMS4zMDNjLTEuNjQ5LDQuNjU3LTYuMDgsOC0xMS4zMDMsOGMtNi42MjcsMC0xMi01LjM3My0xMi0xMmMwLTYuNjI3LDUuMzczLTEyLDEyLTEyYzMuMDU5LDAsNS44NDIsMS4xNTQsNy45NjEsMy4wMzlsNS42NTctNS42NTdDMzQuMDQ2LDYuMDUzLDI5LjI2OCw0LDI0LDRDMTIuOTU1LDQsNCwxMi45NTUsNCwyNGMwLDExLjA0NSw4Ljk1NSwyMCwyMCwyMGMxMS4wNDUsMCwyMC04Ljk1NSwyMC0yMEM0NCwyMi42NTksNDMuODYyLDIxLjM1LDQzLjYxMSwyMC4wODN6Ii8+PHBhdGggZmlsbD0iI0ZGM0QwMCIgZD0iTTYuMzA2LDE0LjY5MWw2LjU3MSw0LjgxOUM0LjY1NSwxNS4xMDgsMTguOTYxLDEyLDI0LDEyYzMuMDU5LDAsNS44NDIsMS4xNTQsNy45NjEsMy4wMzlsNS42NTctNS42NTdDMzQuMDQ2LDYuMDUzLDI5LjI2OCw0LDI0LDRDMTYuMzE4LDQsOS42NTYsOC4zMzcsNi4zMDYsMTQuNjkxeiIvPjxwYXRoIGZpbGw9IiM0Q0FGNTAiIGQ9Ik0yNCw0NGM1LjE2NiwwLDkuODYtMS45NzcsMTMuNDA5LTUuMTkybC02LjE5LTUuMjM4QzI5LjIxMSwzNS4wOTEsMjYuNzE1LDM2LDI0LDM2Yy01LjIwMiwwLTkuNjE5LTMuMzE3LTExLjI4My03Ljk0NmwtNi41MjIsNS4wMjVDOS41MDUsMzkuNTU2LDE2LjIyNyw0NCwyNCw0NHoiLz48cGF0aCBmaWxsPSIjMTk3NkQyIiBkPSJNNDMuNjExLDIwLjA4M0g0MlYyMEgyNHY4aDExLjMwM2MtMC43OTIsMi4yMzctMi4yMzEsNC4xNjYtNC4wODcsNS41NzFjMC4wMDEtMC4wMDEsMC4wMDItMC4wMDEsMC4wMDMtMC4wMDJsNi4xOSw1LjIzOEMzNi45NzEsMzkuMjA1LDQ0LDM0LDQ0LDI0QzQ0LDIyLjY1OSw0My44NjIsMjEuMzUsNDMuNjExLDIwLjA4M3oiLz48L3N2Zz4=');
  background-size: cover;
}

.error-message {
  color: #e74c3c;
  margin-top: 1rem;
}
</style>

