import axios from 'axios'

// Tipos
interface Pregunta {
  id?: number
  enunciado: string
  alternativa_a: string
  alternativa_b: string
  alternativa_c: string
  alternativa_d: string
  correcta: 'A' | 'B' | 'C' | 'D'
  categoria_id: number
}

interface PreguntaResponse extends Pregunta {
  id: number
  created_at: string
  updated_at: string
}

export class GestionPreguntasService {
  private baseURL: string

  constructor() {
    this.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  }

  // Obtener todas las preguntas
  async obtenerPreguntas(): Promise<PreguntaResponse[]> {
    try {
      const response = await axios.get<PreguntaResponse[]>(`${this.baseURL}/preguntas`)
      return response.data
    } catch (error) {
      console.error('Error al obtener preguntas:', error)
      throw error
    }
  }

  // Obtener una pregunta por ID
  async obtenerPregunta(id: number): Promise<PreguntaResponse> {
    try {
      const response = await axios.get<PreguntaResponse>(`${this.baseURL}/preguntas/${id}`)
      return response.data
    } catch (error) {
      console.error(`Error al obtener pregunta ${id}:`, error)
      throw error
    }
  }

  // Crear una nueva pregunta
  async crearPregunta(pregunta: Pregunta): Promise<PreguntaResponse> {
    try {
      const response = await axios.post<PreguntaResponse>(`${this.baseURL}/preguntas`, pregunta)
      return response.data
    } catch (error) {
      console.error('Error al crear pregunta:', error)
      throw error
    }
  }

  // Actualizar una pregunta existente
  async actualizarPregunta(id: number, pregunta: Partial<Pregunta>): Promise<PreguntaResponse> {
    try {
      const response = await axios.put<PreguntaResponse>(`${this.baseURL}/preguntas/${id}`, pregunta)
      return response.data
    } catch (error) {
      console.error(`Error al actualizar pregunta ${id}:`, error)
      throw error
    }
  }

  // Eliminar una pregunta
  async eliminarPregunta(id: number): Promise<void> {
    try {
      await axios.delete(`${this.baseURL}/preguntas/${id}`)
    } catch (error) {
      console.error(`Error al eliminar pregunta ${id}:`, error)
      throw error
    }
  }
}

// Exportar una instancia por defecto
export default new GestionPreguntasService()