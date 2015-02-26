# Rout.Lt

[![Latest Stable Version](https://poser.pugx.org/appserver-io/routlt/v/stable.png)](https://packagist.org/packages/appserver-io/routlt) [![Total Downloads](https://poser.pugx.org/appserver-io/routlt/downloads.png)](https://packagist.org/packages/appserver-io/routlt) [![License](https://poser.pugx.org/appserver-io/routlt/license.png)](https://packagist.org/packages/appserver-io/routlt) [![Build Status](https://travis-ci.org/appserver-io/routlt.png)](https://travis-ci.org/appserver-io/routlt)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appserver-io/routlt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/routlt/?branch=master)[![Code Coverage](https://scrutinizer-ci.com/g/appserver-io/routlt/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/routlt/?branch=master)

## Introduction

Rout.Lt 2 provides a small, but very fast, implementation of a routing and controller implementation for usage with appserver.io based on a [servlet](http://appserver.io/get-started/documentation/servlet-engine.html).

## Installation

If you want to write an application that uses Rout.Lt 2, you have to install it using Composer. To do this, simply add it to the dependencies in your `composer.json`

```sh
{
    "require": {
        "appserver-io/routlt": "~2.0"
    }
}
```

## Configuration

As Rout.Lt 2 is based on a servlet, you first need an `web.xml` inside the `WEB-INF` folder of your application.

Let's assume, you've installed appserver.io on Linux/Mac OS X under ```/opt/appserver``` and your application is named `myapp` you'll save the `web.xml` containing the following content in directory `/opt/appserver/myapp/WEB-INF`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<web-app xmlns="http://www.appserver.io/appserver">

    <display-name>appserver.io example application</display-name>
    <description>This is the example application for the appserver.io servlet engine.</description>

    <session-config>
        <session-name>my_login</session-name>
        <session-file-prefix>my_session_</session-file-prefix>
    </session-config>

    <servlet>
        <description>The Rout.Lt 2 controller servlet implementation.</description>
        <display-name>The Rout.Lt 2 controller servlet</display-name>
        <servlet-name>routlt</servlet-name>
        <servlet-class>AppserverIo\Routlt\ControllerServlet</servlet-class>
        <!-- 
         | this is mandatory and specifies the path where Rout.Lt
         | is looking for your action class implementations
         |-->
        <init-param>
            <param-name>action.base.path</param-name>
            <param-value>WEB-INF/classes</param-value>
        </init-param>
        <!-- 
         | this is optional and can be used to store credentials
         | you don't want to add to version control, for example
         |-->
        <init-param>
            <param-name>routlt.configuration.file</param-name>
            <param-value>WEB-INF/routlt.properties</param-value>
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

As Rout.Lt 2 provides annotations to configure routes and actions, the `routlt.json` configuration file, needed for version ~1.0, is not longer necessry nor supported.

You have two annotations, namely `@Path` and `@Action` to configure your actions. These annotations gives you the possiblity to map the `Path Info` of a request to a method in a action class. This mechanism is adopted by many frameworks out there. The `Path Info` segments will be separated by a slash. The first segment has to map to the value of the `@Path` annotations `name` attribute, the second to one of the `@Action` annotations of one of methods.

For example, assuming want to dispatch the URL `http://127.0.0.1:9080/myapp/index.do/index/login`, you need the implementation of an action class that looks like this. 

```php

namespace MyApp\Actions;

use AppserverIo\Routlt\DispatchAction;
use TechDivision\Servlet\Http\HttpServletRequestInterface;
use TechDivision\Servlet\Http\HttpServletResponseInterface;

/**
 * Example action that shows the usage of the @Path annotation.
 *
 * @Path
 */
class IndexAction extends DispatchAction
{

    /**
     * Dummy action implementation that writes 'Hello World' to the response.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     */
    public function indexAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        $servletResponse->appendBodyStream('Hello World!');
    }

    /**
     * Dummy action that shows the usage of the @Action annotation.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @Action(name="/login")
     */
    public function loginToBackendAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        // do some login stuff here
    }
}
```

After saving the above code to a file named `/opt/appserver/webapps/myapp/WEB-INF/classes/MyApp/Actions/IndexAction.php` and restarting, open the URL `http://127.0.0.1:9080/myapp/index.do/index` with your favorite browser. You should see `Hello World!` there.

> If you don't specify the `name` attributes, depending on the annotation, Rout.Lt uses the class or the method name. As the `Action` suffix has to be cut off, it is important, that the action and the action methods always ends with `Action` and nothing else.

## Limitations

As we actually not differenciate between request methods, there is no possibility to allow action invocation for only selected request methods. For example, it is not possible to configure the indexAction() only has to be invoked on a `GET` request. Issue #20 has already been created to find a solution therefore.

Rout.Lt 2 starts with annotations as only configuration option. There is **NO** possiblity to configure the actions with a deployment descriptor. Issue #21 has already been created to find solution therefore.

# External Links

* All about appserver.io at [appserver.io](http://www.appserver.io)