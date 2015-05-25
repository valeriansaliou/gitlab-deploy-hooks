# Install gitlab-deploy-hooks

## Lighttpd configuration

**Note: this configuration is to be used with lighttpd. It can however be adapted to other Web HTTPd backends.**

The lighttpd configuration is something similar:

```
$HTTP["host"] == "hooks.server.tld" {
    $HTTP["scheme"] == "http" {
        url.redirect = ( "^/(.*)" => "https://hooks.server.tld/$1" )
    }

    $HTTP["scheme"] == "https" {
        server.document-root = "/var/www/hooks.server.tld/web"

        url.rewrite-if-not-file = ( "^/([^\?\/]+)(\/+)?(\?(.+))?" => "/index.php?script=$1&$4" )
    }
}
```
