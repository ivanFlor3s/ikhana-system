#!/bin/bash

echo "🚀 Configurando Ikhana Backend..."

# Instalar dependencias
echo "📦 Instalando dependencias de Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
php artisan key:generate --ansi

# Esperar conexión a la base de datos
echo "⏳ Esperando conexión a la base de datos..."
until php artisan db:show 2>/dev/null; do
    echo "Esperando base de datos..."
    sleep 2
done

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Generar documentación de la API
echo "📚 Generando documentación de la API..."
php artisan scribe:generate

# Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo ""
echo "✅ ¡Configuración completada exitosamente!"
echo ""
echo "🌐 API disponible en: http://localhost:8000/api"
echo "📖 Documentación API: http://localhost:8000/docs"
echo ""

