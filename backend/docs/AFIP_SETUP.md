# Configuración AFIP

## 📋 Descripción

Este proyecto utiliza el SDK de AFIP (`afipsdk/afip.php`) para validar CUIT/CUIL y consultar el padrón de contribuyentes.

## 🔑 Opciones de Configuración

### Opción 1: Usar AFIPSDK (Recomendado - Más Fácil)

AFIPSDK es un servicio que simplifica la integración con AFIP sin necesidad de gestionar certificados.

**Pasos:**

1. Regístrate en: https://app.afipsdk.com/
2. Obtén tu `access_token` desde el dashboard
3. Configura tu `.env`:

```env
AFIP_CUIT=tu_cuit_aqui
AFIP_PRODUCTION=false  # true para producción
AFIP_ACCESS_TOKEN=tu_access_token_aqui
```

### Opción 2: Usar Certificados Propios de AFIP

Si prefieres usar tus propios certificados de AFIP sin intermediarios:

**Pasos:**

1. Genera tus certificados desde AFIP (CSR + Certificado)
2. Coloca los archivos en `src/storage/afip/certs/`:
   - `cert.crt` - Certificado público
   - `privada.key` - Clave privada
3. Configura tu `.env`:

```env
AFIP_CUIT=tu_cuit_aqui
AFIP_PRODUCTION=false  # true para producción
AFIP_PASSPHRASE=tu_passphrase  # si tu clave privada tiene passphrase
# NO incluyas AFIP_ACCESS_TOKEN
```

## 🧪 Probar la Integración

Una vez configurado, prueba el endpoint:

```bash
curl -X POST http://localhost:8000/api/tax-id/validate \
  -H "Content-Type: application/json" \
  -d '{"tax_id": "20-41173228-3"}'
```

**Respuesta esperada:**

```json
{
  "tax_id": "20-41173228-3",
  "exists": true
}
```

## 🔍 Debug

Si hay problemas, revisa los logs:

```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

## 📚 Documentación

- AFIPSDK: https://www.afipsdk.com/docs
- AFIP Oficial: https://www.afip.gob.ar

