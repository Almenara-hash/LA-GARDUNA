# La Garduña

Repositorio / Sitio web de prueba para "La Garduña" (barbería). Este README contiene instrucciones básicas para preparar el entorno local, estructura de carpetas propuesta y comprobaciones rápidas de PHP y VS Code.

## Estructura propuesta
Recomiendo la siguiente estructura para mantener el proyecto ordenado:

- public/            ← document root (ficheros accesibles públicamente)
  - index.php        ← entrada pública (mover aquí desde la raíz)
  - css/
  - js/
  - img/
- src/               ← lógica PHP, clases, controladores
- views/             ← plantillas HTML/PHP
- includes/          ← ficheros de configuración / conexión (actualmente tienes `includes/conexion.php`)
- config/            ← configuración (BD, env)
- tests/             ← pruebas (si aplica)

> Nota: Si mantienes `index.php` en la raíz, funciona; si lo mueves a `public/` tendrás que actualizar el DocumentRoot de Apache o dejar un pequeño bootstrap en la raíz que redirija a `public/index.php`.

## Qué contiene actualmente
- Archivo principal: `index.php` (en la raíz)
- Carpeta `includes/` (presente según `index.php`) con `conexion.php` — asegúrate de que `conexion.php` existe y es require_once desde el script.

## Preparar entorno local (XAMPP / LAMP)
Si usas XAMPP (instalado en `/opt/lampp`), los pasos básicos:

1. Inicia XAMPP (Apache, MySQL):

```bash
sudo /opt/lampp/lampp start
```

2. Sitio web accesible desde el navegador:

- URL local: http://localhost/LA_GARDUÑA/
- Si mueves `index.php` a `public/` y quieres que sea la raíz del virtual host, puedes configurar DocumentRoot a `/opt/lampp/htdocs/LA_GARDUÑA/public` en la configuración de Apache.

3. Ruta del binario PHP en XAMPP:

- PHP CLI de XAMPP suele estar en `/opt/lampp/bin/php`.
- Sistema (si instalaste php desde paquetes) estará en `/usr/bin/php`.

Comprobar PHP CLI:

```bash
# comprobar php del sistema
which php
php -v
# comprobar php de XAMPP
/opt/lampp/bin/php -v
/opt/lampp/bin/php -r 'echo "OK\n";'
```

Si `which php` no devuelve nada pero `/opt/lampp/bin/php -v` funciona, usa la ruta de XAMPP para herramientas que necesiten PHP CLI.

## Configurar VS Code para validar PHP
En VS Code (usuario), la configuración `php.validate.executablePath` debe apuntar al ejecutable PHP que realmente vas a usar:

- Si usas PHP del sistema:
  - `/usr/bin/php`
- Si usas XAMPP:
  - `/opt/lampp/bin/php`

Ejemplo (en `settings.json` de usuario):

```json
"php.validate.executablePath": "/opt/lampp/bin/php"
```

Después de cambiarlo, recarga la ventana de VS Code (Developer: Reload Window).

## Ajustes recomendados en `index.php`
- Incluir la conexión con ruta absoluta: `require_once __DIR__ . '/includes/conexion.php';`
- Escapar salida de usuario: `<?= htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8') ?>`
- Siempre comprobar `isset()` antes de comparar índices de `$_SESSION`.

Si quieres, puedo aplicar estos cambios automáticamente.

## Comprobaciones rápidas (para desarrolladores)
- ¿PHP CLI funciona?
  - `php -v` o `/opt/lampp/bin/php -v`
- ¿El servidor Apache sirve el sitio?
  - Abre http://localhost/LA_GARDUÑA/
- ¿VS Code muestra errores PHP?
  - Revisa `php.validate.executablePath` y recarga VS Code.

## Cómo mover `index.php` a `public/` (opcional)
1. Crear carpeta `public/` en la raíz del proyecto.
2. Mover `index.php` a `public/index.php`.
3. Ajustar rutas de assets (ej. `href="/LA_GARDUÑA/public/css/style.css"` o mejor usar ruta desde la raíz `/LA_GARDUÑA/css/style.css` según cómo configures DocumentRoot).
4. Actualizar Apache DocumentRoot o dejar un `index.php` en la raíz que haga `header('Location: public/');`.

## Contribuir
- Si quieres que organice los ficheros y aplique los cambios recomendados, responde con "Aplica cambios" y lo hago (moveré `index.php` a `public/` si confirmas, actualizaré rutas y corregiré el código seguro).

## Contacto / notas
- Este README es una guía inicial. Puedo actualizarlo con instrucciones específicas (BD, variables de entorno, composer, linters) si me das más detalles.

---
Archivo generado automáticamente por la herramienta de ayuda; ajústalo según tus preferencias.
# LA-GARDU-A
