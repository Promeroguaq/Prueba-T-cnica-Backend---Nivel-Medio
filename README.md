## Proyecto API: Product Service

Este proyecto proporciona una API RESTful para gestionar productos. La API permite realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) sobre los productos.

## Requisitos

- PHP 7.3 o superior
- Composer
- MySQL
- Laravel ^11.9

## 1. Configuración del Proyecto

### 1.1 Clonar el Repositorio

Clona el repositorio usando git:

```bash
git clone https://github.com/tuusuario/productservice.git
1.2 Configuración del Entorno
En el archivo .env, configura los datos de conexión a la base de datos MySQL:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=productservice
DB_USERNAME=root
DB_PASSWORD=tucontraseña

2. Instalación de Dependencias
Accede al directorio del proyecto:

cd productservice

Instala las dependencias de Laravel con Composer:

composer install

3. Configuración de Base de Datos
Asegúrate de tener MySQL en funcionamiento.
Ejecuta las migraciones para crear las tablas necesarias:

php artisan migrate

4. Dockerización del Proyecto

4.1 Crear un Dockerfile
Crea un archivo llamado Dockerfile en la raíz del proyecto con el siguiente contenido:

Dockerfile

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Establecer el directorio de trabajo
WORKDIR /var/www

# Copiar los archivos del proyecto
COPY . .

# Instalar las dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias con Composer
RUN composer install

# Exponer el puerto de la aplicación
EXPOSE 9000

4.2 Crear un archivo docker-compose.yml
Crea un archivo llamado docker-compose.yml en la raíz del proyecto con el siguiente contenido:

version: '3.8'

services:
  app:
    build:
      context: .
    container_name: productservice-app
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www
    networks:
      - productservice-network
    environment:
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=productservice
      - DB_USERNAME=root
      - DB_PASSWORD=tucontraseña
    depends_on:
      - db
  db:
    image: mysql:5.7
    container_name: productservice-db
    environment:
      MYSQL_ROOT_PASSWORD: tucontraseña
      MYSQL_DATABASE: productservice
    ports:
      - "3306:3306"
    networks:
      - productservice-network
    volumes:
      - db-data:/var/lib/mysql

networks:
  productservice-network:
    driver: bridge

volumes:
  db-data:
    driver: local

4.3 Construir y Ejecutar el Contenedor Docker
En la raíz del proyecto, ejecuta el siguiente comando para construir la imagen Docker y levantar los contenedores:

docker-compose up -d --build

Una vez que los contenedores estén en funcionamiento, la API estará disponible en http://localhost:8000.

5. Manejo de Errores en la API
Este servicio backend implementa un manejo robusto de errores para proporcionar respuestas claras y útiles a los clientes de la API. A continuación, se describe cómo se manejan los errores y se proporcionan ejemplos de los diferentes tipos de errores que pueden ocurrir.

Tipos de Errores Comunes
404 - Recurso no encontrado
422 - Error de validación
400 - Error personalizado

5.1 Recurso No Encontrado (404)
Cuando un recurso (producto u orden) no se encuentra en la base de datos, la API devolverá un error 404 Not Found. Este tipo de error es útil para indicar que el recurso solicitado no existe.

Ejemplo:
Si intentas obtener un producto que no existe en la base de datos, la respuesta sería la siguiente:

Solicitud: GET /api/products/9999

Respuesta:

{
    "error": "Resource not found",
    "message": "The resource you are looking for does not exist."
}
Código HTTP: 404

5.2 Error de Validación (422)
Cuando los datos enviados a la API no pasan las reglas de validación (por ejemplo, si un campo obligatorio falta o un valor no es válido), la API devolverá un error 422 Unprocessable Entity.

Ejemplo:
Si intentas crear un producto sin proporcionar un nombre, se producirá un error de validación.

Solicitud: POST /api/products

{
    "description": "A great product",
    "price": 20.00
}
Respuesta:

{
    "error": "Validation error",
    "message": {
        "name": ["The name field is required."]
    }
}
Código HTTP: 422

5.3 Error Personalizado (400)
Los errores personalizados son aquellos definidos específicamente para situaciones particulares que no están cubiertas por las excepciones estándar de Laravel. Estos errores se pueden lanzar cuando los datos no cumplen con ciertos requisitos de negocio.

Ejemplo:
Si intentas crear un producto con un precio negativo, se lanzará un error personalizado.

Solicitud: POST /api/products

{
    "name": "Invalid Product",
    "description": "This product has an invalid price",
    "price": -10.00
}
Respuesta:

{
    "error": "Custom error",
    "message": "El precio no puede ser negativo."
}
Código HTTP: 400

5.4 Manejo de Excepciones Personalizadas
En la aplicación, hemos implementado excepciones personalizadas para gestionar casos específicos de error de manera más controlada. Un ejemplo de una excepción personalizada es la que gestiona los productos con precios negativos.

Excepción Personalizada en app/Exceptions/Handler.php

use App\Exceptions\CustomException;

public function store(Request $request)
{
    if ($request->has('price') && $request->price < 0) {
        throw new CustomException('El precio no puede ser negativo.', 400);
    }

    // Código para crear el producto...
}
La clase CustomException puede ser definida de la siguiente manera:


namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    protected $message = 'Error personalizado';
    protected $code = 400;

    public function __construct($message = null, $code = 400)
    {
        $this->message = $message ?: $this->message;
        $this->code = $code;
    }
}
5.5 Registro de Errores (Logging)
Laravel incluye un sistema de registro de errores basado en Monolog. Los errores se registran automáticamente en el archivo storage/logs/laravel.log, pero también puedes personalizar los canales de registro.


Log::channel('custom_error_log')->error('Error al crear el producto: ' . $exception->getMessage());

Este servicio backend maneja los errores de manera eficiente utilizando excepciones personalizadas y un sistema de registro robusto. Los tipos de errores más comunes son:

404 - Recurso no encontrado.
422 - Error de validación.
400 - Error personalizado.

Los errores se devuelven con mensajes claros para que los usuarios puedan comprender fácilmente qué salió mal. Además, todos los errores son registrados en los archivos de log para facilitar la depuración y resolución de problemas.

6. Instrucciones de Uso con Docker

6.1 Construir la Imagen Docker
Para construir y ejecutar la aplicación usando Docker, utiliza los siguientes comandos:

docker-compose up -d --build

6.2 Acceder a la API
Una vez que los contenedores estén levantados, accede a la API en http://localhost:8000.

