# lnx-sam

Runtime del agente **Sam** de LikePlatform.

Este paquete contiene el motor del agente: el registro de contribuciones, el
manifiesto de capacidades, las bases de las herramientas de lectura y de
propuesta, el modo efectivo por identidad y los comandos de introspección.
**No contiene la identidad de Sam ni la interfaz**: eso vive en el producto
(`luinuxscl/sam`), y los módulos declaran qué aportan (por ejemplo
`luinuxscl/crm`).

- Licencia: **MIT**. La identidad y los módulos de producto son AGPL.
- Sin superficie propia: el paquete no publica rutas, vistas ni componentes.

## Qué resuelve

1. **Una capacidad se declara una vez.** El módulo publica su manifiesto
   (`SamCapability`) y Sam, MCP, la API y la UI leen el mismo permiso, riesgo,
   modo mínimo y confirmación.
2. **La confirmación solo se endurece.** MCP y la API exigen siempre la frase;
   Sam la relaja únicamente cuando el riesgo es reversible y existe undo real.
3. **Nada se expone por estar instalado.** Un módulo tiene que registrarse.
4. **Toda llamada se audita** con la identidad, el recurso y el resultado; el
   payload nunca se guarda en el rastro.

## Instalación

```bash
composer require luinuxscl/lnx-sam
```

El `LnxSamServiceProvider` se descubre solo. Publica la configuración si
necesitas cambiarla:

```bash
php artisan vendor:publish --provider="Luinuxscl\LnxSam\LnxSamServiceProvider" --tag=lnx-sam-config
```

Interruptores principales (`config/lnx_sam.php`):

| Clave | Variable | Por defecto |
| --- | --- | --- |
| `lnx_sam.enabled` | `SAM_ENABLED` | `false` |
| `lnx_sam.permission` | `SAM_PERMISSION` | `sam.use` |
| `lnx_sam.max_mode` | `SAM_MAX_MODE` | `read_only` |

## Registro de un módulo

```php
use Luinuxscl\LnxSam\SamCapability;
use Luinuxscl\LnxSam\SamCapabilityProvider;
use Luinuxscl\LnxSam\SamContributor;
use Luinuxscl\LnxSam\SamRisk;

class CrmSamContributor implements SamContributor, SamCapabilityProvider
{
    public function source(): string
    {
        return 'crm';
    }

    /** @return list<SamCapability> */
    public function capabilities(): array
    {
        return [
            SamCapability::propose(
                key: 'crm.archive_contact',
                source: 'crm',
                label: 'Archivar contacto',
                risk: SamRisk::Reversible,
                undoable: true,
                permission: 'crm.manage_contact',
                surfaces: ['sam', 'mcp', 'api', 'ui'],
                handlers: [ProposeArchiveContactTool::class, ArchiveContactTool::class],
            ),
        ];
    }

    // knowledge(), readTools(), proposeTools(), context(), signals()...
}
```

Y en el `boot()` del proveedor del módulo:

```php
app(\Luinuxscl\LnxSam\SamRegistry::class)->register('crm', CrmSamContributor::class);
```

`SamCapabilityProvider` es **opcional** durante la migración: un módulo sin
manifiesto sigue funcionando y `sam:doctor` lo marca como pendiente.

## Comandos de introspección

Todos son de solo lectura y devuelven código de salida distinto de cero cuando
encuentran drift, así que sirven en CI antes de desplegar.

| Comando | Qué hace |
| --- | --- |
| `sam:capabilities [--source=] [--json] [--strict]` | Catálogo de capacidades por módulo |
| `sam:describe <clave> [--json]` | Detalle de una capacidad y sus herramientas |
| `sam:run <clave> --actor=<email\|id> --dry-run [--input=<json>]` | Comprueba interruptor, permiso, modo y handler; previsualiza un borrador sin crearlo |
| `sam:doctor [--actor=<email\|id>]` | Capacidades sin herramienta, herramientas sin capacidad, `WritesData` incoherente y módulos sin manifiesto |

`sam:run` exige `--dry-run` a propósito: nunca escribe. Una propuesta solo se
previsualiza si su herramienta implementa
`Luinuxscl\LnxSam\Tools\SamDraftPreview`.

## Contrato con la aplicación anfitriona

El runtime es un paquete Laravel, no un framework propio. Espera que el host
aporte:

- `App\Models\User` como identidad autenticada.
- `App\Ai\AiGateway` y `App\Ai\AiConfig` para el interruptor, la cuota y el
  modelo por defecto.
- `App\Actions\RecordMcpInvocation` y `App\Mcp\McpToolRegistry` para el rastro
  de auditoría y el inventario de herramientas MCP.
- Los modelos de conversación (`App\Models\AiConversation`,
  `App\Models\AiChatMessage`), `App\Models\SamModeCeiling`,
  `App\Ai\Knowledge\HelpSearchService` y `App\Jobs\WarmSamFilterSignalsJob`.
- `laravel/ai` y `laravel/mcp`.

Este acoplamiento es **temporal** y está registrado en el ADR 0002 del kit:
el objetivo es que el host inyecte esas piezas por contrato en una versión
posterior, sin cambiar la API pública del paquete.

## Compatibilidad

El runtime vivía en la aplicación como `App\Ai\Sam` y sus comandos como
`App\Console\Commands\Sam*`. `src/aliases.php` mantiene esos nombres como
alias perezosos, así que una referencia resuelta en runtime sigue funcionando
mientras el host retira su copia.

**Los alias no cubren declaraciones de tipo.** PHP no autoloada la clase del
parámetro al comprobar el tipo: `function f(App\Ai\Sam\SamMode $mode)` rechaza
un `Luinuxscl\LnxSam\SamMode` aunque el alias exista. Lo mismo pasa con las
claves del contenedor, que son cadenas: `app()->instance('App\Ai\Sam\SamInstructions', …)`
no afecta a `Luinuxscl\LnxSam\SamInstructions`. Por eso los consumidores
importan el namespace canónico y los alias se retiran en la próxima versión
mayor.

## Pruebas

```bash
composer install
composer test
composer lint
```

Las pruebas no necesitan base de datos ni el stack de IA: cubren el manifiesto
de capacidades, la validación del registro y la detección de drift.
