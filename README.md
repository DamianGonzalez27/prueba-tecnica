# Prueba técnica
Muchas gracias por la oportunidad de participar en el proceso de selección, les comparto el 
repositorio del backend, el cual es un API construida con Symfony y Api Platform

Los endpoints son generados mediante las entidades del framework

## Instalación

```
$ composer install
```

## Configurar la base de datos

Colocar en el archivo ```.env``` las configuraciones necesarias tal como aparece en el ejemplo

```
DATABASE_URL=mysql://user:password@127.0.0.1:3306/database
```

## Crear la base de datos

Para crear la base de datos es necesario ejecutar los siguientes comandos: 

```
php bin/console doctrine:database:create 
```

```
php bin/console doctrine:migrations:execute --up |Migraciones respectivas en src/Migrations|
```

## Ejecutando el servidor

Mediante bin/console

```
php bin/console server:run
```

Mediante el servidor de Symfony

```
symfony serve
```
