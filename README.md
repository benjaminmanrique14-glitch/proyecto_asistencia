# Asistencia docente — E.P.E.T N1

Sistema de asistencia de profesores (Symfony 5.4 + MySQL).

## Requisitos

- PHP 7.4+
- Composer
- MySQL / MariaDB (XAMPP)

## Instalación

```bash
composer install
```

Configurá la base en `.env` (`DATABASE_URL`) y creá la base `asistencia_colegio`.

```bash
php bin/console doctrine:schema:update --force
php -S 0.0.0.0:8000 -t public
```

Abrí http://127.0.0.1:8000

## Accesos

| Apartado | URL | Credenciales |
|---|---|---|
| Ingreso (profesores) | `/totem/login` | DNI del profesor (desde la cuenta admin se pueden ver los DNI) / `demo1234` (demos) |
| Administración | `/admin/login` | `admin` / `Colegio2026` |
