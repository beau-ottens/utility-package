SuperBrave Utility package
==========================

### Using the UuidParamConverter

Enable the param converter by adding the following lines to the `services.yaml` file in your project:

```php
SuperBrave\UtilityPackage\Request\ParamConverter\UuidParamConverter:
    tags: ['request.param_converter']
```
