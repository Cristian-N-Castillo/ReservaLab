# ESPECIFICACIÓN FUNCIONAL

# Sistema de Reserva de Laboratorios

## 1. Descripción

El Sistema de Reserva de Laboratorios tiene como objetivo administrar la reserva de laboratorios computacionales del establecimiento, permitiendo a docentes y funcionarios solicitar horarios disponibles de manera rápida, evitando conflictos de reservas y manteniendo un registro histórico de todas las actividades realizadas.

El sistema permitirá controlar laboratorios, cursos, horarios, usuarios y reservas, además de generar reportes y enviar notificaciones cuando corresponda.

---

# 2. Objetivos

## Objetivo General

Digitalizar el proceso de reserva de laboratorios del establecimiento, reemplazando el sistema manual utilizado actualmente.

## Objetivos Específicos

- Administrar usuarios.
- Administrar cursos.
- Administrar laboratorios.
- Administrar horarios de clases.
- Administrar reservas.
- Evitar reservas duplicadas.
- Registrar historial de reservas.
- Enviar notificaciones mediante correo electrónico.
- Generar reportes administrativos.

---

# 3. Tipos de Usuario

## Administrador

Tiene acceso completo al sistema.

Puede:

- Crear usuarios.
- Editar usuarios.
- Desactivar usuarios.
- Crear cursos.
- Editar cursos.
- Desactivar cursos.
- Crear laboratorios.
- Editar laboratorios.
- Desactivar laboratorios.
- Administrar horarios de clases.
- Crear reservas.
- Cancelar reservas.
- Registrar observaciones.
- Ver reportes.
- Exportar información.
- Configurar parámetros del sistema.

---

## Docente / Funcionario

Puede:

- Iniciar sesión.
- Ver disponibilidad.
- Crear reservas.
- Cancelar sus reservas.
- Consultar su historial.
- Visualizar calendario.

---

# 4. Reglas del Negocio

- Las cuentas son creadas únicamente por el administrador.
- El inicio de sesión se realiza utilizando RUT y contraseña.
- Todos los usuarios pertenecen al mismo establecimiento.
- Los cursos son creados por el administrador.
- Los laboratorios son administrados por el administrador.
- Los horarios corresponden a clases completas.
- No se pueden realizar reservas sobre horarios ya ocupados.
- No se pueden reservar horarios anteriores a la fecha actual.
- No se pueden realizar reservas con más de 14 días de anticipación.
- Un docente puede cancelar una reserva.
- Una reserva cancelada no puede ser modificada.
- Las reservas no son recurrentes.
- El administrador puede crear reservas para cualquier docente.
- Solo el administrador puede registrar observaciones.
- Solo se enviará correo cuando exista una observación.

---

# 5. Módulos del Sistema

## Seguridad

- Inicio de sesión.
- Cierre de sesión.
- Gestión de usuarios.
- Gestión de roles.

---

## Cursos

- Crear curso.
- Editar curso.
- Desactivar curso.

---

## Laboratorios

- Crear laboratorio.
- Editar laboratorio.
- Desactivar laboratorio.

---

## Horarios

- Crear horario.
- Editar horario.
- Desactivar horario.

---

## Reservas

- Crear reserva.
- Cancelar reserva.
- Consultar disponibilidad.
- Consultar historial.

---

## Observaciones

- Registrar observación.
- Adjuntar PDF.
- Enviar correo.

---

## Reportes

- Reservas por laboratorio.
- Reservas por docente.
- Reservas por curso.
- Laboratorios más utilizados.
- Historial de reservas.

---

# 6. Alcance

El sistema permitirá administrar completamente el proceso de reserva de laboratorios del establecimiento, considerando usuarios, horarios, laboratorios, cursos, reservas, observaciones y reportes.# ESPECIFICACIÓN FUNCIONAL

# Sistema de Reserva de Laboratorios

## 1. Descripción

El Sistema de Reserva de Laboratorios tiene como objetivo administrar la reserva de laboratorios computacionales del establecimiento, permitiendo a docentes y funcionarios solicitar horarios disponibles de manera rápida, evitando conflictos de reservas y manteniendo un registro histórico de todas las actividades realizadas.

El sistema permitirá controlar laboratorios, cursos, horarios, usuarios y reservas, además de generar reportes y enviar notificaciones cuando corresponda.

---

# 2. Objetivos

## Objetivo General

Digitalizar el proceso de reserva de laboratorios del establecimiento, reemplazando el sistema manual utilizado actualmente.

## Objetivos Específicos

- Administrar usuarios.
- Administrar cursos.
- Administrar laboratorios.
- Administrar horarios de clases.
- Administrar reservas.
- Evitar reservas duplicadas.
- Registrar historial de reservas.
- Enviar notificaciones mediante correo electrónico.
- Generar reportes administrativos.

---

# 3. Tipos de Usuario

## Administrador

Tiene acceso completo al sistema.

Puede:

- Crear usuarios.
- Editar usuarios.
- Desactivar usuarios.
- Crear cursos.
- Editar cursos.
- Desactivar cursos.
- Crear laboratorios.
- Editar laboratorios.
- Desactivar laboratorios.
- Administrar horarios de clases.
- Crear reservas.
- Cancelar reservas.
- Registrar observaciones.
- Ver reportes.
- Exportar información.
- Configurar parámetros del sistema.

---

## Docente / Funcionario

Puede:

- Iniciar sesión.
- Ver disponibilidad.
- Crear reservas.
- Cancelar sus reservas.
- Consultar su historial.
- Visualizar calendario.

---

# 4. Reglas del Negocio

- Las cuentas son creadas únicamente por el administrador.
- El inicio de sesión se realiza utilizando RUT y contraseña.
- Todos los usuarios pertenecen al mismo establecimiento.
- Los cursos son creados por el administrador.
- Los laboratorios son administrados por el administrador.
- Los horarios corresponden a clases completas.
- No se pueden realizar reservas sobre horarios ya ocupados.
- No se pueden reservar horarios anteriores a la fecha actual.
- No se pueden realizar reservas con más de 14 días de anticipación.
- Un docente puede cancelar una reserva.
- Una reserva cancelada no puede ser modificada.
- Las reservas no son recurrentes.
- El administrador puede crear reservas para cualquier docente.
- Solo el administrador puede registrar observaciones.
- Solo se enviará correo cuando exista una observación.

---

# 5. Módulos del Sistema

## Seguridad

- Inicio de sesión.
- Cierre de sesión.
- Gestión de usuarios.
- Gestión de roles.

---

## Cursos

- Crear curso.
- Editar curso.
- Desactivar curso.

---

## Laboratorios

- Crear laboratorio.
- Editar laboratorio.
- Desactivar laboratorio.

---

## Horarios

- Crear horario.
- Editar horario.
- Desactivar horario.

---

## Reservas

- Crear reserva.
- Cancelar reserva.
- Consultar disponibilidad.
- Consultar historial.

---

## Observaciones

- Registrar observación.
- Adjuntar PDF.
- Enviar correo.

---

## Reportes

- Reservas por laboratorio.
- Reservas por docente.
- Reservas por curso.
- Laboratorios más utilizados.
- Historial de reservas.

---

# 6. Alcance

El sistema permitirá administrar completamente el proceso de reserva de laboratorios del establecimiento, considerando usuarios, horarios, laboratorios, cursos, reservas, observaciones y reportes.