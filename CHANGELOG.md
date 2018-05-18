# Version 2.2.0-beta11

## Bugfixes

* None

## Features

* Switch to appserver-io/description version 11.0.0

# Version 2.2.0-beta10

## Bugfixes

* None

## Features

* Switch to appserver-io/description version 10.0.0

# Version 2.2.0-beta9

## Bugfixes

* None

## Features

* Remove dependency to appserver-io/configuration because of implicity dependency by appserver-io/description library

# Version 2.2.0-beta8

## Bugfixes

* None

## Features

* Switch to appserver-io/description version ~9.0

# Version 2.2.0-beta7

## Bugfixes

* Fixed invalid ResultDescriptor usage

## Features

* None

# Version 2.2.0-beta6

## Bugfixes

* None

## Features

* Refactoring for usage with new DI implementation

# Version 2.2.0-beta5

## Bugfixes

* None

## Features

* Switch to latest appserver-io-psr/epb version 4.0.0

# Version 2.2.0-beta4

## Bugfixes

* Query whether or not a header already exists before overriding it with a default header in RawResult class

## Features

* Add new RedirectResult implementation

# Version 2.2.0-beta3

## Bugfixes

* None

## Features

* Add PATCH request method restriction by @Patch annotation

# Version 2.2.0-beta2

## Bugfixes

* None

## Features

* Refactoring ActionMapping initialization

# Version 2.2.0-beta1

## Bugfixes

* None

## Features

* Closed [#38](https://github.com/appserver-io/routlt/issues/38) - URL Routes according to REST defacto standard

# Version 2.1.1

## Bugfixes

* Fixed encoding and appending of additional NULL content to response body in RawResult
* Fixed composer dependencies to an installable set

## Features

* None

# Version 2.1.0

## Bugfixes

* Removed misleading deprecation of RawResult result class
* Fixed Travis-CI configuration file
* Updating Dev dependencies

## Features

* Add fromConfiguration() method to descriptors

# Version 2.0.0

## Bugfixes

* Fixed invalid access to not existent getServletRequest() method in WorkflowInterceptor

## Features

* None

# Version 2.0.0-beta3

## Bugfixes

* None

## Features

* Remove mandatory return value for actions, use default value "input" if NULL has been returned

# Version 2.0.0-beta2

## Bugfixes

* Fixed [#30](https://github.com/appserver-io/routlt/issues/30) - Unsupported HTTP method results in 404

## Features

* Added support for overloading action names by usage of HTTP method annotations

# Version 2.0.0-beta1

## Bugfixes

* None

## Features

* Add RawResult implementation to optimize JSON request handling

# Version 2.0.0-alpha5

## Bugfixes

* Fixed [#27](https://github.com/appserver-io/routlt/issues/27) - ServletDispatcherResult didn't set correct request URI

## Features

* Moved PhtmlServlet as DhtmlServlet to ServletEngine (to avoid conflicts with simple PHTML templates)

# Version 2.0.0-alpha4

## Bugfixes

* None

## Features

* Add interceptor functionality
* Add PhtmlServlet for processing simple PHTML templates

# Version 2.0.0-alpha3

## Bugfixes

* None

## Features

* Closed [#20](https://github.com/appserver-io/routlt/issues/20) - Allow Action invocation for specified request methods only

# Version 2.0.0-alpha2

## Bugfixes

* None

## Features

* Add BaseAction::setAttribute() and BaseAction::getAttribute() methods

# Version 2.0.0-alpha1

## Bugfixes

* None

## Features

* Initial Release for ~2.0

# Version 1.0.0

## Bugfixes

* None

## Features

* Switched to stable dependencies due to version 1.0.0 release

# Version 0.3.0

## Bugfixes

* None

## Features

* Applied new file name and comment naming conventions

# Version 0.2.1

## Bugfixes

* None

## Features

* Add X-Powered-By header with servlet name that handles the request

# Version 0.2.0

## Bugfixes

* None

## Features

* Switch to new appversion.io beta version

# Version 0.1.2

## Bugfixes

* None

## Features

* Move composer dependencies from require => require-dev

# Version 0.1.1

## Bugfixes

* None

## Features

* Refactoring ANT PHPUnit execution process
* Composer integration by optimizing folder structure (move bootstrap.php + phpunit.xml.dist => phpunit.xml)
* Switch to new appserver-io/build build- and deployment environment
