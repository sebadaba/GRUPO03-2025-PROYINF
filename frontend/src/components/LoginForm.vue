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
  max-width: 360px;
  margin: 4rem auto;
  padding: 2rem;
  border-radius: 12px;
  background-color: #fafafa;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  text-align: center;
  font-family: 'Segoe UI', sans-serif;
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.subtitle {
  font-size: 0.95rem;
  color: #777;
  margin-bottom: 1.5rem;
}

.login-options {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.login-btn {
  padding: 0.75rem;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  background-color: #2f80ed;
  color: white;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.login-btn:hover {
  background-color: #1c64d1;
}

.error-message {
  margin-top: 1.2rem;
  color: #d9534f;
  font-size: 0.9rem;
}
</style>


