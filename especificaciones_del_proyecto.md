# Especificaciones del Sistema de Asistencia de Profesores

## Contexto del Proyecto
Se requiere desarrollar un sistema web para el control de asistencia de profesores en un colegio. 
El sistema cuenta con dos puntos de acceso principales:
1. **El Tótem (Ubicado en la entrada del colegio):** Una pantalla física donde los profesores registran su ingreso, salida y solicitan la justificación de inasistencias mediante un QR. 
2. **Panel Web de Administración:** Accesible desde cualquier lugar y dispositivo por los directivos para gestionar profesores, horarios y monitorear la asistencia diaria.

## Stack Tecnológico
*   **Backend:** PHP (Framework Symfony).
*   **Base de Datos:** MySQL.
*   **Frontend del Tótem:** Interfaz amigable para pantallas táctiles, usando JavaScript vainilla para el manejo de inactividad.

## 1. Módulo del Tótem (Profesores)
**Restricción de acceso:** El login de profesores SOLO debe estar disponible en la interfaz del tótem (se puede manejar mediante una vista/ruta específica que se configurará a pantalla completa en el hardware del colegio, o limitando por IP en el futuro).

*   **Login:** El profesor ingresa mediante su DNI y Contraseña. 
*   **Seguridad y Sesión:** El frontend debe implementar un temporizador en JavaScript que cierre la sesión automáticamente tras 20 segundos de inactividad (sin toques en la pantalla). Además, debe haber un botón visible para cerrar sesión manualmente.
*   **Gestión de Asistencia:**
    *   Al iniciar sesión, el profesor verá un botón gigante de "Marcar Ingreso".
    *   Si el profesor ya marcó su ingreso ese día, el botón debe mutar a "Marcar Salida" para registrar el fin de su jornada.
*   **Justificación de Faltas/Retiros:**
    *   Habrá un botón de "Justificar Inasistencia/Retiro temprano".
    *   Al presionarlo, el sistema genera en la pantalla del tótem un **Código QR Dinámico**.
    *   Este QR contiene un Token único (asociado al ID del profesor) con una validez de 5 minutos. 

## 2. Flujo Móvil (Justificación vía QR)
Este es el único entorno fuera del tótem donde el profesor interactúa.
*   El profesor escanea el QR dinámico del tótem con su celular.
*   La URL lleva a una vista móvil que, por seguridad, le exige ingresar su **contraseña** (para validar que es realmente él).
*   Una vez validado, se despliega un formulario para:
    *   Indicar tipo (Falta del día, Retiro temprano, Falta futura).
    *   Seleccionar la fecha/s.
    *   Cargar archivo adjunto de justificación (Formatos permitidos: JPG, PNG, PDF, DOC, DOCX. Peso máximo: 4MB).
*   Los archivos deben subirse a una carpeta segura en el servidor de Symfony (ej. `public/uploads/justificaciones`) y la base de datos solo guardará la URL/Ruta del archivo.

## 3. Módulo de Administración
Accesible desde cualquier dispositivo mediante una URL de `/admin`. Requiere login de administrador.

*   **ABM / CRUD de Profesores:**
    *   Alta, baja y modificación de profesores (DNI, Nombre, Apellido).
    *   Creación y reseteo de la contraseña inicial de los profesores.
*   **Configuración de Horarios:**
    *   Desde el perfil de cada profesor, el administrador debe poder tildar qué días de la semana (Lunes a Viernes) asiste y en qué rango horario (hora de entrada y hora de salida). Esto es vital para calcular las ausencias.
*   **Dashboard / Calendario Diario:**
    *   Vista principal que muestra un calendario posicionado en el día actual.
    *   Al clickear en el día actual (o pasados), debe mostrar 3 listas:
        1.  **Asistieron:** Profesores que marcaron ingreso ese día.
        2.  **Ausentes:** Sistema cruza la tabla de `horarios_profesores` (quiénes debían venir hoy según el día de la semana) y verifica quiénes NO tienen registro en la tabla de `asistencias`.
        3.  **Justificados:** Profesores que tienen un registro en la tabla de justificaciones para esa fecha, con enlace para ver el archivo adjunto.

## Instrucciones para Cursor (IA)
1. Inicia el proyecto en Symfony configurando Doctrine para que se conecte a la base de datos existente proporcionada.
2. Genera las entidades a partir del esquema de la base de datos.
3. Asegúrate de implementar validaciones estrictas en el servidor para el peso del archivo (4MB) y los tipos MIME.
4. Implementa el temporizador de inactividad de 20s en el layout base del Tótem.