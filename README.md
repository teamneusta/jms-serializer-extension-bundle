# Teamneusta JMS Serializer Extension Bundle

The JMS Serializer Bundle enables the configuration of various directories to supply serializer configuration files. These directories are configured with a PHP class namespace. If you aim to offer configuration files for your bundle's classes and the application intends to override some of them, namespace conflicts may arise. This bundle facilitates the configuration of directories to supply wildcard configurations without a namespace prefix.

## Installation

1. **Require the bundle**

   ```shell script
   composer require teamneusta/jms-serializer-extension-bundle
   ```

2. **Enable the bundle**

   Add the Bundle to your `config/bundles.php`:

   ```php
   \Neusta\JmsSerializerExtensionBundle\NeustaJmsSerializerExtensionBundle::class => ['all' => true],
   ```

## Configuration

The following configuration snippet illustrates the usage of this bundle. The configuration keys my_bundle and app have their namespace prefixes configured to be ignored. The configured directories can contain any serializer configuration one desires; PHP class namespaces are disregarded. The configuration key another_bundle provides default configuration from the JMS Serializer Bundle for the CompanyThingCoolBundle Bundle.

```yaml
jms_serializer:
  metadata:
    directories:
      my_bundle:
        path: '@MyBundle/../config/serializer'
        namespace_prefix: 'my_bundle'
      app:
        path: '%kernel.project_dir%/config/serializer'
        namespace_prefix: 'app'
      another_bundle:
        namespace_prefix: "Company\\Thing\\CoolBundle"
        path: "@CompanyThingCoolBundle/../config/serializer"

neusta_jms_serializer_extension:
  non_prefixed_namespaces:
    my_bundle: ~
    app: ~
```

### Overriding load priority

It is possible to provide a `priority` per namespace. If no priority is given, `0` is assumed. When multiple configuration files for a class are found, the one with the highest priority is used.

For example, consider the following configuration. If a configuration file for a given class is found in both `my_bundle` and `another_bundle`, the configuration from `my_bundle` is used because it has a higher priority.

```yaml
jms_serializer:
  metadata:
    directories:
      my_bundle:
        path: '@MyBundle/../config/serializer'
        namespace_prefix: 'my_bundle'
      another_bundle:
        namespace_prefix: "Company\\Thing\\CoolBundle"
        path: "@CompanyThingCoolBundle/../config/serializer"

neusta_jms_serializer_extension:
  non_prefixed_namespaces:
    my_bundle:
      priority: 10
    another_bundle: ~
```

## Contribution

Feel free to open issues for any bug, feature request, or other ideas.

Please remember to create an issue before creating large pull requests.

### Local Development

To develop on local machine, the vendor dependencies are required.

```shell
bin/composer install
```

We use composer scripts for our main quality tools. They can be executed via the `bin/composer` file as well.

```shell
bin/composer cs:fix
bin/composer phpstan
```

For the tests there is a different script, that includes a database setup.

```shell
bin/run-tests
```
