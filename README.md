# site-error-notifications

## WordPress Plugin

When used as a WordPress plugin the plugin will search for a `config.yml` file in the root directory of the plugin.  The path to this file may be changed by editing `site-error-notifications.php` (a comment indicates where to edit).

## YAML Configuration

A YAML document is accepted as configuration by the `\Fgms\SiteErrorNotifications\YamlFactory::create` static method.  This method produces an instance of `\Fgms\SiteErrorNotifications\ErrorHandlerInterface`.  The YAML document is expected to have the following form:

```YAML
monolog:
    # Only present if Monolog logging is desired
    file: # The file to which the log shall be written
    name: # The name of the log (optional)
email:
    # Only present if email notifications are desired
    from: # The address from which emails shall be sent
    to: # The address (a string) or addresses (an array) to which emails shall be sent
    templates: # Path to the templates directory for Twig
    name: # The name of the site (optional)
```
