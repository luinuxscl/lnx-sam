# Changelog

Todas las versiones notables de `luinuxscl/lnx-sam`.

El formato sigue [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/)
y el versionado es [SemVer](https://semver.org/lang/es/).

## [0.2.1] - 2026-11-08

### Corregido

- `SamRuntime::conversations()` ya no carga el último mensaje en bloque: la
  relación `latestMessage` del anfitrión ordenaba por la clave del mensaje, y
  con UUID en PostgreSQL el agregado `max(uuid)` no existe, así que la lista de
  conversaciones respondía 500. El contexto solo pinta el título, así que el
  último mensaje queda como carga diferida.

## [0.2.0] - 2026-11-08

### Añadido

- `SamToolDenied`: un módulo puede rechazar una lectura o una propuesta antes
  de producir un resultado, con el motivo que corresponde a la auditoría
  (`forbidden`, `out_of_scope`, `validation`, `failed`). El runtime registra
  una denegación con ese motivo en lugar de un éxito sin payload, y devuelve
  al modelo el mensaje que declara el módulo.

## [0.1.1] - 2026-11-08

### Corregido

- `SamContext::toPrompt()` escapa las etiquetas del JSON (`JSON_HEX_TAG`): un
  valor tomado de un registro no puede cerrar el bloque `<screen_context>` y
  hacerse pasar por instrucciones.

## [0.1.0] - 2026-11-08

Primera versión publicada: extracción del runtime de `App\Ai\Sam` del kit.

### Añadido

- `SamRegistry`: registro validado de contribuidores, perfiles, superficies,
  presentadores de preferencias y manifiestos de capacidades.
- `SamCapability`, `SamCapabilityKind` y `SamCapabilityProvider`: una capacidad
  declarada una vez con permiso, riesgo, modo mínimo, confirmación, superficies
  y herramientas que la implementan.
- `SamCapabilityInspector`: detección de drift entre capacidad y herramienta.
- `SamProposeTool` deriva `risk()` y `minimumMode()` de la capacidad declarada;
  `RISK` y `MIN_MODE` quedan como respaldo de un módulo sin migrar.
- `SamDraftPreview`: previsualiza un borrador sin persistirlo.
- Comandos de solo lectura: `sam:capabilities`, `sam:describe`,
  `sam:run --dry-run` y `sam:doctor`.
- `LnxSamServiceProvider` con la configuración, los singletons, los comandos y
  los alias de compatibilidad.

### Notas

- Requiere `laravel/ai` y `laravel/mcp`.
- La aplicación anfitriona aporta identidad, gateway de IA, auditoría y modelos
  de conversación; el acoplamiento es temporal (ADR 0002 del kit).
