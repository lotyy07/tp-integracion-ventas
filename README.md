# TP Integración de ventas - Parte 2

Jesus Inciarte - Thiago Golubovich 7mo 4ta

Bueno, esta es la segunda parte de la integración entre el sistema de ventas y el ERP de stock (sistema B). Lo más importante que cambiamos es que ahora tenemos una base de datos, porque antes no guardábamos nada, solo recibíamos la venta, la transformábamos y la mandábamos. Con la base ya podemos hacer que una venta no se procese dos veces, que si B se cae la venta no se pierda y se pueda mandar de nuevo, y recibir ventas del sistema A2. También le mandamos a un sistema C los datos de cada venta para sus estadísticas.

## Qué se necesita

PHP 8 y MySQL prendido en localhost con el usuario root sin contraseña, si tiene contraseña se cambia en BaseDatos.php. La base y la tabla no hace falta crearlas porque se crean solas la primera vez.

## Cómo probarlo

Hay que levantar tres servidores, cada uno en una terminal distinta. Los dos primeros son un sistema B y un sistema C falsos que hicimos para poder probar (b-falso.php y c-falso.php), lo único que hacen es responder que salió todo bien y anotar lo que les llega en movimientos-b.txt y estadisticas-c.txt, así vemos qué le llegó a cada uno. El tercero es nuestra integración.

    php -S localhost:3000 b-falso.php
    php -S localhost:3001 c-falso.php
    php -S localhost:8090

Para mandar una venta del sistema A o del A2:

    curl -X POST localhost:8090/index.php --data-binary @InfoVenta.json
    curl -X POST localhost:8090/index.php --data-binary @InfoVentaA2.json

Si mandamos la misma venta otra vez nos responde que ya estaba registrada y a B y a C no les llega de nuevo.

Para ver que B y C son independientes cortamos uno de los dos con Ctrl+C y mandamos una venta nueva: al otro le llega igual.

Para ver qué pasa si el ERP no responde lo que hacemos es cortar el servidor del puerto 3000 con Ctrl+C y mandar una venta, la venta queda guardada como pendiente y después la volvemos a mandar con:

    php reintentar.php

Cada vez que se intenta mandar una venta le sumamos un intento, y si falla 3 veces pasa a fallida para mirarla nosotros.

Para que el área de stock vea qué ventas se procesaron y cuáles están pendientes:

    curl localhost:8090/index.php
    curl "localhost:8090/index.php?estado=pendiente"

Además dejamos una consulta con los datos de las estadísticas de todas las ventas guardadas:

    curl "localhost:8090/index.php?estadisticas"

## Cómo está armado

El index.php es el que arma todo y según lo que le llega llama a un controlador o al otro.

El VentaControlador recibe la venta y se fija qué traductor la entiende. Como el sistema A y el A2 mandan la venta con formatos distintos hicimos un traductor para cada uno (TraductorVentaA y TraductorVentaA2), y los dos la pasan al mismo DTO, así después el proceso es uno solo para los dos. Si mañana se agrega otro sistema solo hay que hacer otro traductor.

El VentaService es el que hace todo el proceso, guarda la venta, la transforma con el VentaMapper al formato de B y la manda con el ConectorStockHttp. Después arma los datos para C con el TransformadorEstadistica y se los manda con el ConectorEstadisticaHttp. Antes el que mandaba a B era el controlador, y lo pasamos al servicio porque si no el reintentar.php no lo podía usar y teníamos que copiar el código.

Todo lo que es de la base de datos está en VentaRepositorio y BaseDatos.

Para el sistema C hicimos lo mismo que con B: su propio conector (ConectorEstadistica y ConectorEstadisticaHttp) y su propio transformador (TransformadorEstadistica), así lo de B no cambió.

## Decisiones que tomamos

Para que una venta no se procese dos veces usamos el origen y el número de venta como clave primaria. Le pusimos el origen porque el sistema A y el A2 pueden tener una venta con el mismo número y no queríamos que se tomen como si fueran la misma.

La venta la guardamos antes de mandarla a B, así si B falla no se pierde. Al sistema A le respondemos que está todo bien aunque B haya fallado, porque la venta ya la tenemos guardada nosotros, si le respondiéramos con un error A la podría seguir mandando.

A B y a C les mandamos la venta por separado. Si C está caído no se corta nada y la venta se registra igual en B, y si B está caído a C le llega igual. Como son estadísticas, si a C no le llega una venta no la reintentamos, lo que sí o sí tiene que llegar es el stock.

El sistema A2 no manda precios, entonces en sus ventas el importe total va vacío (null) y no en 0, para que no parezca que fue una venta gratis.
