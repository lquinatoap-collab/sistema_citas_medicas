# Sistema de citas médicas

Proyecto académico de la asignatura Interacción Humano-Computador de la Universidad Estatal de Milagro (UNEMI).

Se tomó como base el sistema proporcionado por el tutor y se realizaron mejoras en su interfaz con apoyo de la IA. El proyecto utiliza PHP y SQLite y se ejecutó y comprobó localmente mediante XAMPP y Apache en Windows.

## Funciones principales

- Inicio y cierre de sesión.
- Registro de citas con nombre del paciente, especialidad, fecha y hora.
- Consulta de las citas registradas.
- Contador del total de citas.
- Almacenamiento persistente mediante SQLite.

## Mejoras realizadas en la interfaz

- Etiquetas visibles para identificar los campos.
- Instrucciones y ejemplos para completar el formulario.
- Botón con el texto “Registrar cita”.
- Presentación diferenciada de mensajes de confirmación y error.
- Fecha y hora en columnas separadas en el reporte.
- Contador de citas y mensaje cuando no existen registros.
- Ajustes de distribución para diferentes tamaños de pantalla.

## Archivos del proyecto

- index.php: interfaz y procesamiento de las operaciones del sistema.
- db_config.php: conexión con SQLite y creación de la base de datos y sus tablas.
- css/style.css: estilos de la interfaz.

El archivo citas_medicas.db no se incluye en el repositorio. El sistema lo genera automáticamente cuando no existe, junto con las tablas y el usuario inicial definidos en db_config.php.

## Requisitos

- XAMPP con Apache y PHP.
- Extensión PDO SQLite habilitada en el PHP de XAMPP.
- Navegador web.
- Permiso de escritura en la carpeta del proyecto para crear y actualizar la base de datos.

No es necesario iniciar MySQL, porque el sistema utiliza SQLite.

## Instalación y ejecución en XAMPP

1. Descargar el repositorio desde “Code / Código” → “Download ZIP”.
2. Extraer el archivo descargado.
3. Crear la carpeta C:\xampp\htdocs\Reservas_Medicas.
4. Copiar dentro index.php, db_config.php y la carpeta css, conservando su estructura.
5. Abrir el panel de control de XAMPP.
6. Iniciar Apache mediante el botón “Start”.
7. Abrir en el navegador: http://localhost/Reservas_Medicas/
8. Iniciar sesión con las credenciales de demostración.

Estas instrucciones suponen que XAMPP está instalado en C:\xampp y que Apache utiliza el puerto 80.

## Acceso de demostración

- Usuario: admin
- Contraseña: 1234

Son credenciales de prueba para uso académico local.

## Comprobación del funcionamiento

1. Iniciar sesión.
2. Registrar una cita utilizando datos ficticios.
3. Comprobar el mensaje de confirmación, la nueva fila y el incremento del contador.
4. Cerrar sesión y detener Apache.
5. Iniciar Apache nuevamente y acceder al sistema.
6. Comprobar que la cita permanece registrada.

Los registros se conservan en citas_medicas.db. Cerrar sesión o detener Apache no elimina ese archivo.

## Uso de inteligencia artificial

Se utilizó ChatGPT como herramienta de apoyo para proponer cambios en el código y mejorar la presentación de la interfaz. Los cambios fueron revisados y comprobados mediante pruebas de funcionamiento realizadas por los estudiantes.

## Alcance académico

Este repositorio contiene el código para descargarlo y ejecutarlo en un entorno local con PHP. No constituye un sistema médico preparado para producción. Para las demostraciones deben utilizarse datos ficticios.
