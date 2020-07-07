# Schema.org Adapter

A fluent builder for all Schema.org types and their properties and JSON-LD generator.

This adapter wraps the [Schema.org package](https://github.com/spatie/schema-org) by Spatie.

## Usage

A global `schema` helper is created. You can use it anywhere in your code: page or site templates, etc.

```php
$localBusiness = schema('localBusiness')
    ->name('Spatie')
    ->email('info@spatie.be')
    ->contactPoint(schema('contactPoint')->areaServed('Worldwide'));

echo $localBusiness->toScript();
```

```html
<script type="application/ld+json">
{
    "@context": "http:\/\/schema.org",
    "@type": "LocalBusiness",
    "name": "Spatie",
    "email": "info@spatie.be",
    "contactPoint": {
        "@type": "ContactPoint",
        "areaServed": "Worldwide"
    }
}
</script>
```
