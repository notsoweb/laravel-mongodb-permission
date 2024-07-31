Laravel MongoDB Permission
===============

![GitHub Tag](https://img.shields.io/github/v/tag/notsoweb/laravel-mongodb-permission)

Este paquete te permite gestionar roles y permisos de forma simple en el sistema usando el motor de base de datos MongoDB.

Recomendamos seguir la **documentación oficial** de [Laravel MongoDB](https://www.mongodb.com/docs/drivers/php/laravel-mongodb/current/quick-start/) para configurar el driver de mongoDB correctamente en tu proyecto, configurar los modelos entre otras cosas.

Este paquete solo permite la asignación de un rol al usuario, y este hereda los permisos de su rol. El propósito de este paquete, es que el usuario sea quien cree todos los roles que requiera, y este simplemente seleccione los permisos que contendrá el usuario al momento de crear el rol.

La forma de trabajar es simple:
1. Se crean **Tipos de permisos**, esto no es otra cosa más que la clasificación que le dará el programador a los permisos, véalo como una forma de agrupar los permisos bajo cierto criterio.
2. Junto con la creación del **Tipo de permiso** deseado, se agregan los **permisos** que corresponden al **tipo de permiso**.
3. Se crean los **roles**, y se vinculan los **permisos** a los **roles** de la forma que más le convenga al usuario.

En resumen:
1. Los **tipos de permisos** agrupan **permisos**, esta agrupación es definida por el desarrollador, una forma de clasificar los permisos.
2. Los **roles** agrupan **permisos**, sin embargo, los roles son creados según requiera el usuario (y por el usuario), a excepción de los **roles iniciales**, que posiblemente sea el del administrador, y el supervisor (esos obviamente los tiene que crear el desarrollador).

Esto hace que el desarrollador se olvide de crear roles, y dejarle la tarea al usuario. Solo se debe de preocupar de clasificar los permisos.

Siguiendo esta lógica, y para mi caso particular, el usuario solo requiere tener un rol. Si requiere permisos personalizados, **se puede crear libremente un rol**, y asignárselo al usuario, de esta manera ya existirá el **rol (combinación de permisos único)** para el usuario.