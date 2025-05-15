CREATE TABLE alumno (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    rut VARCHAR(12) UNIQUE,
    curso VARCHAR(20),
    colegio VARCHAR(100)
);

CREATE TABLE profesor (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    rut VARCHAR(12) UNIQUE,
    correo VARCHAR(100) UNIQUE,
    especialidad VARCHAR(100)
);

CREATE TABLE materia (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE
);

CREATE TABLE categoria_pregunta (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100),
    materia_id INTEGER REFERENCES materia(id)
);

CREATE TABLE pregunta (
    id SERIAL PRIMARY KEY,
    enunciado TEXT,
    alternativa_a TEXT,
    alternativa_b TEXT,
    alternativa_c TEXT,
    alternativa_d TEXT,
    correcta CHAR(1) CHECK (correcta IN ('A', 'B', 'C', 'D')),
    categoria_id INTEGER REFERENCES categoria_pregunta(id)
);

CREATE TABLE ensayo (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(150),
    descripcion TEXT,
    fecha_creacion DATE DEFAULT CURRENT_DATE,
    profesor_id INTEGER REFERENCES profesor(id)
);

CREATE TABLE ensayo_pregunta (
    id SERIAL PRIMARY KEY,
    ensayo_id INTEGER REFERENCES ensayo(id) ON DELETE CASCADE,
    pregunta_id INTEGER REFERENCES pregunta(id) ON DELETE CASCADE,
    orden INTEGER
);

CREATE TABLE resultado_ensayo (
    id SERIAL PRIMARY KEY,
    alumno_id INTEGER REFERENCES alumno(id),
    ensayo_id INTEGER REFERENCES ensayo(id),
    puntaje_total INTEGER,
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE resultado_pregunta (
    id SERIAL PRIMARY KEY,
    resultado_ensayo_id INTEGER REFERENCES resultado_ensayo(id) ON DELETE CASCADE,
    pregunta_id INTEGER REFERENCES pregunta(id),
    alternativa_respondida CHAR(1) CHECK (alternativa_respondida IN ('A', 'B', 'C', 'D')),
    correcta BOOLEAN
);
