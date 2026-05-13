# 📦 Guía para Subir el Proyecto a GitHub

## 🚀 Paso a Paso

### 1. Verificar que estés en la carpeta del proyecto

```bash
cd /Users/lucasgabrielfloresmancilla/Documents/Development/Lucas/ikhana_backend
```

### 2. Inicializar Git (si no está inicializado)

```bash
git init
```

### 3. Agregar todos los archivos

```bash
git add .
```

### 4. Verificar qué archivos se van a subir

```bash
git status
```

**Asegúrate de que NO se suban:**
- ❌ `.env` (credenciales)
- ❌ `/vendor` (dependencias de composer)
- ❌ `/node_modules` (dependencias de npm)
- ❌ `composer.lock` (opcional, pero algunos lo suben)

**Deben aparecer en verde (para commit):**
- ✅ `docker-compose.yml`
- ✅ `Dockerfile`
- ✅ `README.md`
- ✅ `.env.example`
- ✅ `app/`
- ✅ `routes/`
- ✅ `database/migrations/`
- ✅ etc.

### 5. Hacer el primer commit

```bash
git commit -m "Initial commit: Laravel API with Docker, Provider CRUD, and API documentation"
```

### 6. Crear el repositorio en GitHub

1. Ve a https://github.com
2. Click en el botón **"+"** (arriba derecha) → **"New repository"**
3. Nombre del repositorio: `ikhana_backend` (o el que prefieras)
4. Descripción: `Backend API para gestión de proveedores con Laravel 11 y Docker`
5. **NO** marques "Initialize this repository with a README" (ya lo tienes)
6. Click en **"Create repository"**

### 7. Conectar tu proyecto local con GitHub

GitHub te mostrará comandos similares a estos:

```bash
git remote add origin https://github.com/TU_USUARIO/ikhana_backend.git
git branch -M main
git push -u origin main
```

**Reemplaza `TU_USUARIO`** con tu nombre de usuario de GitHub.

### 8. Subir el código

```bash
git push -u origin main
```

Si es la primera vez, te pedirá autenticación:
- **Usuario:** tu nombre de usuario de GitHub
- **Contraseña:** usa un **Personal Access Token** (no tu contraseña normal)

#### Crear un Personal Access Token:
1. Ve a GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click en "Generate new token"
3. Selecciona el scope `repo`
4. Copia el token y úsalo como contraseña

---

## 📝 Commits Sugeridos para el Futuro

Usa mensajes descriptivos:

```bash
# Al agregar nuevas funcionalidades
git add .
git commit -m "feat: add customer module with CRUD endpoints"
git push

# Al corregir bugs
git commit -m "fix: validation error in provider creation"
git push

# Al actualizar documentación
git commit -m "docs: update README with new endpoints"
git push

# Al agregar configuración
git commit -m "chore: add docker-compose for production"
git push
```

---

## 🌿 Trabajar con Ramas (Recomendado)

### Crear una rama para nuevas features:

```bash
# Crear y cambiar a una nueva rama
git checkout -b feature/nombre-de-la-feature

# Hacer cambios y commit
git add .
git commit -m "feat: add new feature"

# Subir la rama a GitHub
git push -u origin feature/nombre-de-la-feature
```

### Hacer un Pull Request:
1. Ve a GitHub → Tu repositorio
2. Verás un botón "Compare & pull request"
3. Describe los cambios
4. Click en "Create pull request"

### Merge a main:
```bash
# Cambiar a main
git checkout main

# Traer últimos cambios
git pull origin main

# Hacer merge de tu rama
git merge feature/nombre-de-la-feature

# Subir a GitHub
git push
```

---

## 🔒 Proteger Información Sensible

### ¿Qué NO debes subir?

**NUNCA subas:**
- ❌ `.env` (contiene credenciales)
- ❌ Claves de API
- ❌ Contraseñas de base de datos
- ❌ Tokens de acceso

### Qué SÍ debes subir:
- ✅ `.env.example` (sin valores reales)
- ✅ `docker-compose.yml` (con valores por defecto)
- ✅ Todo el código fuente
- ✅ Documentación

---

## 📋 Checklist Antes de Subir

```
✅ .gitignore configurado correctamente
✅ .env NO está en el repositorio
✅ .env.example SÍ está en el repositorio
✅ README.md actualizado con instrucciones
✅ docker-compose.yml funcional
✅ Migrations están en database/migrations/
✅ Documentación de API generada
✅ Sin credenciales hardcodeadas en el código
```

---

## 🎯 Después de Subir

### Actualizar el README con la URL del repo:

En el README.md, reemplaza:
```bash
git clone <URL_DEL_REPOSITORIO>
```

Por:
```bash
git clone https://github.com/TU_USUARIO/ikhana_backend.git
```

Commit y push:
```bash
git add README.md
git commit -m "docs: add repository URL to README"
git push
```

---

## 👥 Para que otros colaboradores usen el proyecto:

Comparte estas instrucciones:

```bash
# 1. Clonar el repositorio
git clone https://github.com/TU_USUARIO/ikhana_backend.git
cd ikhana_backend

# 2. Copiar .env.example a .env
cp .env.example .env

# 3. Levantar Docker
docker-compose up -d --build

# 4. Instalar y configurar
docker-compose exec app bash
composer install
php artisan key:generate
php artisan migrate
php artisan scribe:generate
exit

# 5. Ver la API
# http://localhost:8000
# http://localhost:8000/docs
```

---

## 🚨 Solución de Problemas

### Si subes el .env por error:

```bash
# Eliminar del repositorio (pero mantener local)
git rm --cached .env

# Commit
git commit -m "chore: remove .env from repository"

# Push
git push
```

### Si quieres ver qué está ignorado:

```bash
git status --ignored
```

### Si quieres cambiar el último commit:

```bash
git commit --amend -m "Nuevo mensaje"
git push --force  # ⚠️ Solo si no lo han clonado otros
```

---

## 📚 Recursos Útiles

- **Git Cheat Sheet:** https://education.github.com/git-cheat-sheet-education.pdf
- **Conventional Commits:** https://www.conventionalcommits.org/
- **GitHub Docs:** https://docs.github.com/

---

¡Listo! Tu proyecto está en GitHub 🎉

