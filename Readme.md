# Inlinefluid — TYPO3 Extension

**Package:** `sg3w/inlinefluid` &nbsp;·&nbsp; **Extension key:** `inlinefluid` &nbsp;·&nbsp; **License:** GPL-2.0-or-later

Inlinefluid is a TYPO3 extension that allows editors and integrators to enter and render Fluid templates directly in the TYPO3 backend using a dedicated CType — no sitepackage deployment required for simple rendering tasks.

---

## Features

| Feature | Description |
|---|---|
| Inline Fluid editing | Write Fluid templates directly in the TYPO3 backend |
| Cached & non-cached rendering | Switch between `USER` and `USER_INT` per content element |
| Variable providers | Inject custom variables (input, text, link, RTE) into templates |
| Static data providers | Supply JSON, YAML, XML, CSV, or serialized PHP data inline or from file |
| Dynamic data providers | Execute custom PHP classes to deliver runtime data |
| FAL media integration | Attach images and reference them via `{mediaImage}` |
| HTML injection | Add raw HTML before or after the rendered template |
| Predefined variables | Access `{REQUEST}`, `{CONTENTOBJECT}`, `{PAGE}`, `{TSFE}`, `{TSSETUP}` |
| Fail-safe execution | Provider exceptions are collected, not re-thrown — rendering continues |
| Exception output | Display provider errors to authenticated backend users only |

---

## Requirements

- TYPO3 11.5 LTS, 12.4 LTS, 13.4 LTS or 14
- PHP 8.1 or higher
- Composer

### Version specific notes

| | TYPO3 11.5 | TYPO3 12.4 | TYPO3 13.4 | TYPO3 14 |
|---|---|---|---|---|
| Syntax highlighting of the code fields | EXT:t3editor (install `typo3/cms-t3editor`, otherwise plain textareas are used) | EXT:t3editor (same) | part of the backend | part of the backend |
| `{TSFE}` predefined variable | available | available | available | not available — `TypoScriptFrontendController` was removed from the core. `{PAGE}`, `{CONTENTOBJECT}` and `{TSSETUP}` cover the usual cases, and the content element reports the unavailability to logged in backend users. |
| Image field in the Media tab | TCA `inline` on `sys_file_reference` | TCA `file` | TCA `file` | TCA `file` |
| Link variable | `input` with `inputLink` | TCA `link` | TCA `link` | TCA `link` |

The content elements render raw markup: the rendering definition registered by Extbase
(`tt_content.<ctype> =< lib.contentElement`) is replaced by a plain `USER_INT` / `USER`
object, so EXT:fluid_styled_content is not required and nothing is wrapped around the
template output.

---

## Installation

```bash
composer require sg3w/inlinefluid
```

After installation activate the extension in the TYPO3 Extension Manager or via:

```bash
vendor/bin/typo3 extension:activate inlinefluid
```

---

## Quick start

1. Create a new content element and select the CType **Inlinefluid**.
2. Open the **Fluid** tab and enter a Fluid template, e.g.:

```html
<h2>{PAGE.title}</h2>
<p>Content element UID: {CONTENTOBJECT.uid}</p>
```

3. Save and preview the page.

---

## Static data providers

Supply structured data without PHP. Supported formats: **JSON**, **YAML**, **XML**, **CSV**, **Serialized PHP**.

Data can be entered inline in the backend or loaded from a file (e.g. `EXT:myext/Resources/Private/Data/items.yaml`).

Example — YAML inline data (variable name `items`):

```yaml
- title: "First item"
  active: true
- title: "Second item"
  active: false
```

Fluid template:

```html
<f:for each="{items}" as="item">
    <p class="{f:if(condition: item.active, then: 'active')}">{item.title}</p>
</f:for>
```

---

## Dynamic data providers

Supply runtime data via a custom PHP class. The class must extend `AbstractExternalDataProvider`:

```php
<?php
namespace Vendor\MySitepackage\DataProvider;

use Sg3w\Inlinefluid\DataProvider\External\AbstractExternalDataProvider;

class MyDataProvider extends AbstractExternalDataProvider
{
    public function renderData(string $dataString): mixed
    {
        return [
            ['title' => 'Item A', 'value' => 42],
            ['title' => 'Item B', 'value' => 17],
        ];
    }
}
```

Configure in the backend:

- **Variable name:** `myData`
- **Provider class:** `\Vendor\MySitepackage\DataProvider\MyDataProvider`

---

## Exception handling in templates

Provider exceptions are collected and exposed as `{exceptions}`. Display them only to authenticated backend users:

```html
<f:be.security.ifAuthenticated>
    <f:if condition="{exceptions}">
        <div class="alert alert-danger" role="alert">
            <ul>
            <f:for as="e" each="{exceptions}">
                <li><strong>{e}</strong></li>
            </f:for>
            </ul>
        </div>
    </f:if>
</f:be.security.ifAuthenticated>
```

---

## Demo files

The extension ships example data files in `EXT:inlinefluid/Demo/`:

| File | Format | Demo |
|---|---|---|
| `data.json` | JSON | Generic widget data |
| `data.yaml` | YAML | Generic data |
| `tabs.xml` | XML | Bootstrap Tab Panel |
| `employees.csv` | CSV | HTML table |
| `os-tree.yaml` | YAML | OS family tree (5 levels) |

---

## FlexForm data structures

The backend form of both content elements is defined by two static data structures in
`Configuration/FlexForms/`:

| File | Used by | Shape |
|---|---|---|
| `inlinefluid.xml` | TYPO3 13, 14 | `type=link`, `type=file`, `required=1`, associative select items, `codeEditor` |
| `inlinefluid_v11_v12.xml` | TYPO3 11, 12 | `<TCEforms>` wrapper, `eval=required`, `inputLink`, inline FAL, numeric select items, `t3editor` |

TYPO3 11 reads the legacy shape natively. TYPO3 12 runs the full `TcaMigration` over every
FlexForm data structure and still ships `t3editor`, so it shares the same file. TYPO3 13
dropped both `<TCEforms>` support and `t3editor`, which is why 13 and 14 need their own file.
The legacy file can be deleted together with v11/v12 support.

**Both files are generated — do not edit them by hand.** They come from a single definition
so that they cannot drift apart:

```bash
php Build/GenerateFlexForms.php
```

To verify in CI that the committed files match the generator (exit code 1 when they do not):

```bash
php Build/GenerateFlexForms.php --check
```

The structures are registered as `FILE:EXT:inlinefluid/Configuration/FlexForms/<file>`
references, so an integrator can point the `ds` of a content type at a modified copy.
There is one exception: on TYPO3 11/12 *without* EXT:t3editor the extension loads the legacy
file, removes the `t3editor` render type and registers the structure inline, so the code
fields degrade to plain textareas instead of an "unknown render type" warning.

---

## Documentation

Full documentation is located in the `Documentation/` directory and covers:

- CType configuration
- Backend editing
- Fluid rendering
- Variable providers
- Static & dynamic data providers
- Predefined template variables
- Media integration
- HTML injection
- ViewHelpers
- Examples (Bootstrap Carousel, Accordion, Tab Panel, HTML Table, Tree View)
- Debugging
- Exception handling
- Target audience

---

## Author

**Jörg Hammel (sg3w.com)**

---

## License

This extension is released under the **GNU General Public License v2.0 or later**.
See [LICENSE](LICENSE) for the full license text.
