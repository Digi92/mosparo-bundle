 

<p align="center">
    <img src="https://github.com/mosparo/mosparo/blob/master/assets/images/mosparo-logo.svg?raw=true" alt="mosparo logo contains a bird with the name Mo and the mosparo text"/>
</p>

<h1 align="center">
    Symfony Bundle
</h1>
<p align="center">
    This bundle adds the required functionality to use mosparo in your Symfony form.
</p>

![GitHub](https://img.shields.io/github/license/Digi92/mosparo-bundle)
[![GitHub checks](https://github.com/Digi92/mosparo-bundle/actions/workflows/release.yaml/badge.svg)](https://github.com/Digi92/mosparo-bundle/actions/workflows/release.yaml)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/Digi92/mosparo-bundle)

***

## Description

With this PHP library you can connect to a mosparo installation and verify the submitted data.

## Requirements

To use the plugin, you must meet the following requirements:

* A mosparo project
* Symfony 5.4 or greater
* PHP 8.0 or greater

## Installation

Install this bundle by using composer:

```text
composer require Digi92/mosparo-bundle
```

## Configuration

### 1. Register the bundle

Register bundle into `config/bundles.php`:

```php
return [
    //...
    Mosparo\MosparoBundle\MosparoBundle::class => ['all' => true],
];
```

### 2. Add configuration files

Setup bundle's config into `config/packages/mosparo.yaml`:

```yaml
mosparo:
  instance_url: '%env(MOSPARO_INSTANCE_URL)%'
  uuid: '%env(MOSPARO_UUID)%'
  public_key: '%env(MOSPARO_PUBLIC_KEY)%'
  private_key: '%env(MOSPARO_PRIVATE_KEY)%'
```

Add your variables to your .env file:

```text
###> mosparo/mosparo-bundle ###
MOSPARO_INSTANCE_URL=https://example.com
MOSPARO_UUID=<your-project-uuid>
MOSPARO_PUBLIC_KEY=<your-project-public-key>
MOSPARO_PRIVATE_KEY=<your-project-private-key>
###< mosparo/mosparo-bundle ###
```

### Handle multiples configurations

Into your configuration file. ex: `config/packages/mosparo.yaml`:

```yaml
mosparo:
  default_project: '%env(MOSPARO_DEFAULT)%'
  projects:
    forms:
      instance_url: '%env(MOSPARO_FORMS_INSTANCE_URL)%'
      uuid: '%env(MOSPARO_FORMS_UUID)%'
      public_key: '%env(MOSPARO_FORMS_PUBLIC_KEY)%'
      private_key: '%env(MOSPARO_FORMS_PRIVATE_KEY)%'
    login:
      instance_url: '%env(MOSPARO_LOGIN_INSTANCE_URL)%'
      uuid: '%env(MOSPARO_LOGIN_UUID)%'
      public_key: '%env(MOSPARO_LOGIN_PUBLIC_KEY)%'
      private_key: '%env(MOSPARO_LOGIN_PRIVATE_KEY)%'
```

Inside your `.env` files

```text
###> mosparo/mosparo-bundle ###
MOSPARO_DEFAULT=<your-default-config>

MOSPARO_FORMS_INSTANCE_URL=<your-forms-project-instance>
MOSPARO_FORMS_UUID=<your-forms-project-uuid>
MOSPARO_FORMS_PUBLIC_KEY=<your-forms-project-public-key>
MOSPARO_FORMS_PRIVATE_KEY=<your-forms-project-private-key>

MOSPARO_LOGIN_INSTANCE_URL=<your-login-project-instance>
MOSPARO_LOGIN_UUID=<your-login-project-uuid>
MOSPARO_LOGIN_PUBLIC_KEY=<your-login-project-public-key>
MOSPARO_LOGIN_PRIVATE_KEY=<your-login-project-private-key>
###< mosparo/mosparo-bundle ###
```

## Usage

### How to integrate mosparo in Symfony form:

```php
<?php

use Mosparo\MosparoBundle\Form\Type\MosparoType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('mosparo', MosparoType::class, [
            'project' => 'default',
            'allowBrowserValidation' => false,
            'cssResourceUrl' => '',
            'designMode' => false,
            'inputFieldSelector' => '[name]:not(.mosparo__ignored-field)',
            'loadCssResource' => true,
            'requestSubmitTokenOnInit' => true,
        ]);
    }
}
```

### Additional options

| Parameter                  | Type    | Default value                         | Description                                                                                                                                                                                                                                                                     |
|----------------------------|---------|---------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `project`                  | String  | `default`                             | Defines the mosparo project to use for validation. See [Handle multiples configurations](#handle-multiples-configurations)                                                                                                                                                                                                                         |
| `allowBrowserValidation`   | Boolean | false                                 | Specifies whether browser validation should be active.                                                                                                                                                                                                                          |
| `cssResourceUrl`           | String  | *empty*                               | Defines the address at which the browser can load the CSS resources. You can use it if the correct resource address is cached.                                                                                                                                                  |
| `designMode`               | Boolean | false                                 | Used to display the mosparo box in the different states in the mosparo backend. The mosparo box is not functional if this option is set to `true`.                                                                                                                              |
| `inputFieldSelector`       | String  | `[name]:not(.mosparo__ignored-field)` | Defines the selector with which the fields are searched.                                                                                                                                                                                                                        |
| `loadCssResource`          | Boolean | true                                  | Determines whether the script should also load the CSS resources during initialization.                                                                                                                                                                                         |
| `requestSubmitTokenOnInit` | Boolean | `true`                                | Specifies whether a submit token should be automatically requested during initialization. If, for example, the form is reset directly after initialization (with `reset()`), there is no need for a submit token during initialization, as a new code is requested with the reset. |

## Ignored fields

### Automatically ignored fields

mosparo automatically ignores the following fields:

* All fields which **do not** have a name (attribute `name`)
* HTML field type
  * *password*
  * *file*
  * *hidden*
  * *checkbox*
  * *radio*
  * *submit*
  * *reset*
* HTML button type
  * *submit*
  * *button*
* Fields containing `_mosparo_` in the name

### Manually ignored fields

#### CSS class

If you give a form field the CSS class `mosparo__ignored-field`, the field will not be processed by mosparo.

#### JavaScript initialisation

When initializing the JavaScript functionality, you can define the selector with which the fields are searched (see [Parameters of the mosparo field](#additional-options)).

### Override allowed and verifiable field types

You can also register event listeners (or subscribers) to add or remove field types.

We use here a listener for example.

```php
// src/EventListener/FilterFieldTypesListener.php
namespace App\EventListener;

use Mosparo\MosparoBundle\Event\FilterFieldTypesEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class FilterFieldTypesListener
{
    public function __invoke(FilterFieldTypesEvent $event): FilterFieldTypesEvent
    {
        // Remove PasswordType from the ignored list
        $event->setIgnoredFieldTypes(array_diff(
          $event->getIgnoredFieldTypes(), 
          [
            PasswordType::class
          ]
        ));
        
        // Add PasswordType to the verifiable list
        $event->setVerifiableFieldTypes(array_merge(
          $event->getVerifiableFieldTypes(), 
          [
            PasswordType::class
          ]
        ));

        return $event;
    }
}
```

### How to deal with functional and e2e testing:

Mosparo won't allow you to test your app efficiently unless you disable it for the environment you are testing against.

```yaml
# config/packages/mosparo.yaml
mosparo:
  enabled: '%env(bool:MOSPARO_ENABLED)%'
```

```bash
#.env.test or an environment variable
MOSPARO_ENABLED=0
```

### How to disable SSL verification:

In order to support invalid SSL certificats you will need to disable the SSL check.

```yaml
# config/packages/mosparo.yaml
mosparo:
  ...
  verify_ssl: '%env(bool:MOSPARO_VERIFY_SSL)%'
```

```bash
#.env or an environment variable
MOSPARO_VERIFY_SSL=0
```

#### For multiples configurations:

```yaml
# config/packages/mosparo.yaml
mosparo:
  projects:
    forms:
      ...
      verify_ssl: '%env(bool:MOSPARO_FORMS_VERIFY_SSL)%'
    login:
      ...
      verify_ssl: '%env(bool:MOSPARO_LOGIN_VERIFY_SSL)%'
```

```bash
#.env or an environment variable
MOSPARO_FORMS_VERIFY_SSL=0
MOSPARO_LOGIN_VERIFY_SSL=0
```

## License

mosparo is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
Please see the [LICENSE](LICENSE) file for the full license.

## Contributing

See [CONTRIBUTING](.github/CONTRIBUTING)
