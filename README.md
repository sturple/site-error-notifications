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
    host: # Optional, if present Swift will be configured to send directly using this host rather than by using PHP's mail function
    port: # Optional, only regarded if "host" is present, the port to use for SMTP
    encryption: # Optional, only regarded if "host" is present, the encryption type to use for SMTP
    username: # Optional, only regarded if "host" is present, the username to use for SMTP
    password: # Optional, only regarded if "host" is present, the password to use for SMTP
html:
    templates: # Path to the templates directory for Twig
    debug: # True for debug messages, false otherwise (optional, defaults to false)
```
