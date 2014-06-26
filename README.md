# Routlt

[![Latest Stable Version](https://poser.pugx.org/appserver-io/routlt/v/stable.png)](https://packagist.org/packages/appserver-io/routlt) [![Total Downloads](https://poser.pugx.org/appserver-io/routlt/downloads.png)](https://packagist.org/packages/appserver-io/routlt) [![Latest Unstable Version](https://poser.pugx.org/appserver-io/routlt/v/unstable.png)](https://packagist.org/packages/appserver-io/routlt) [![License](https://poser.pugx.org/appserver-io/routlt/license.png)](https://packagist.org/packages/appserver-io/routlt) [![Build Status](https://travis-ci.org/appserver-io/routlt.png)](https://travis-ci.org/appserver-io/routlt)

## Introduction

routlt provides a small, but very fast implementation of a routing and controller implementation for usage with appserver.io
based on a servlet.

## Installation

You don't have to install routlt, as it'll be delivered with the latest appserver.io release. If you want to install it with
your application only, you do this by add

```sh
{
    "require": {
        "appserver-io/routlt": "dev-master"
    },
}
```

to your ```composer.json``` and invoke ```composer update``` in your project.

That's all!

## Usage

As routlt is based on a servlet, you first need an ```web.xml``` inside the ```WEB-INF``` folder of your application.

Let's assume, you've installed appserver.io on Linux/Mac OS X under ```/opt/appserver``` and your application is named
```myapp``` you'll save the ```web.xml``` containing the following content in directory ```/opt/appserver/myapp/WEB-INF```:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app version="2.4">

    <display-name>appserver.io example application</display-name>
    <description>This is the example application for the appserver.io servlet engine.</description>

    <servlet>
    
        <description>The routlt controller servlet implementation.</description>
        <display-name>The routlt controller servlet</display-name>
        <servlet-name>routlt</servlet-name>
        <servlet-class>AppserverIo\Routlt\ControllerServlet</servlet-class>
        
        <init-param>
            <param-name>configurationFile</param-name>
            <param-value>WEB-INF/routes.json</param-value>
        </init-param>
        
    </servlet>

    <servlet-mapping>
        <servlet-name>routlt</servlet-name>
        <url-pattern>/</url-pattern>
    </servlet-mapping>

    <servlet-mapping>
        <servlet-name>routlt</servlet-name>
        <url-pattern>/*</url-pattern>
    </servlet-mapping>
</web-app>    
```

Additionally you need a routing configuration. In the actual version, the routing configuration will be parsed from a
JSON file with the following structure:

```json
{
    "routes": [
        {
            "urlMapping": "/index*",
            "actionClass": "\\MyApp\\Actions\\IndexAction"
        }
    ]
}
```

Where you'll save your routing file depends on what you've configured in your ```web.xml```. In our example we've
configured, that our routing file will also be available under ```/opt/appserver/myapp/WEB-INF```, so we'll save
it there.

```php

/**
 * MyApp\Actions\SomeAction
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Application
 * @package    MyApp
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace MyApp\Actions;

use AppserverIo\Routlt\DispatchAction;
use TechDivision\Servlet\Http\HttpServletRequest;
use TechDivision\Servlet\Http\HttpServletResponse;

/**
 * Example action.
 *
 * @category   Application
 * @package    MyApp
 * @subpackage Actions
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class IndexAction extends DispatchAction
{

    /**
     * Dummy action implementation that writes 'Hello World' to the response.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequest  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponse $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {
        $servletResponse->appendBodyStream('Hello World!');
    }
}
```

That's pretty simple. After restarting the appserver.io open the URL ```http://127.0.0.1:9080/myapp/index.do/index```
with your browser. You should see ```Hello World!``` there.