# O2O

He creado 3 endpoints:
    - Obtención de una cerveza pasandole una Id.
    - Obteniendo todas las cervezas disponibles.
    - Obteniendo las cervezas filtrando por un texto.

Como le pregunté a Luis y me dijo que realizar un volcado a la BD desde la API daría puntos, he creado un comando
para ejecutar una funcion en el servicio ApiPunkService que realiza el volcado de la API a nuestra BD y de esta forma no tener que estar realizando llamadas a la API.

Había creado vistas(sin mucho estilo), para poder probarlo todo mediante una interfaz pero me he ajustado al anunciado y devuelvo finalmente un JSON y he quitado todas las referencias a estas pese a que los ficheros continuan ahi.

He creado una clase para gestionar las excepciones propias del sistema.

He creado un listener que maneja todas las excepciones del sistema ya sean controladas o inesperadas devolviendo un json con el mensaje publico, el estado de la petición http y en caso de estar en desarrollo una traza, fichero, etc

Por último, he generado tests para comprobar el correcto funcionamiento de las funciones creadas.
