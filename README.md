# Rout.Lt 2

[![Latest Stable Version](https://poser.pugx.org/appserver-io/routlt/v/stable.png)](https://packagist.org/packages/appserver-io/routlt)
 [![Total Downloads](https://poser.pugx.org/appserver-io/routlt/downloads.png)](https://packagist.org/packages/appserver-io/routlt)
 [![License](https://poser.pugx.org/appserver-io/routlt/license.png)](https://packagist.org/packages/appserver-io/routlt)
 [![Build Status](https://travis-ci.org/appserver-io/routlt.png)](https://travis-ci.org/appserver-io/routlt)
 [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/appserver-io/routlt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/routlt/?branch=master)
 [![Code Coverage](https://scrutinizer-ci.com/g/appserver-io/routlt/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/appserver-io/routlt/?branch=master)

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

Roul.Lt 2 comes with it's own annotations and configuration filse. 

### Application

Therefore, it is necessary, that these annoatations as well as all other framework specfic configuration files will be initialized on application startup. To ensure this, you have to provide a custom `META-INF/context.xml` file with your application, which needs the additional `<descriptors/>' declaration for the Object Manager

```xml
<managers>
    ...
    <manager
        name="ObjectManagerInterface"
        type="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManager"
        factory="AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory">
        <descriptors>
            <descriptor>AppserverIo\Description\ServletDescriptor</descriptor>
            <descriptor>AppserverIo\Description\MessageDrivenBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\StatefulSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\SingletonSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Description\StatelessSessionBeanDescriptor</descriptor>
            <descriptor>AppserverIo\Routlt\Description\PathDescriptor</descriptor>
        </descriptors>
    </manager>
    ...
</managers>
```

### Servlet Engine

As Rout.Lt 2 is based on a servlet, you also need to provide an `WEB-INF/web.xml` your application.

Let's assume, you've installed appserver.io on Linux/Mac OS X under `/opt/appserver` and your application is named `myapp` you'll save the `web.xml` containing the following content in directory `/opt/appserver/myapp/WEB-INF`

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
        <description>A servlet that handles DHTML files.</description>
        <display-name>The DHTML servlet</display-name>
        <servlet-name>dhtml</servlet-name>
        <servlet-class>AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServlet</servlet-class>
    </servlet>
    
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
            <param-name>action.namespace</param-name>
            <param-value>/MyApp/Actions</param-value>
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
        <servlet-name>dhtml</servlet-name>
        <url-pattern>*.dhtml</url-pattern>
    </servlet-mapping>

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

## Action Mapping

As Rout.Lt 2 provides annotations to configure routes and actions, the `routlt.json` configuration file, needed for version ~1.0, is not longer necessary nor supported.

### Using Annotations

You have two annotations, namely `@Path` and `@Action` to configure the routing of your application. These annotations gives you the possiblity to map the `Path Info` of a request to a method in a action class. This mechanism is adopted by many of the available frameworks. The `Path Info` segments will be separated by a slash. The first segment has to map to the value of the `@Path` annotations `name` attribute, the second to one of the `@Action` annotations of one of methods.

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

After saving the above code to a file named `/opt/appserver/webapps/myapp/WEB-INF/classes/MyApp/Actions/IndexAction.php` and [restarting](http://appserver.io/get-started/documentation/basic-usage.html#start-and-stop-scripts), open the URL `http://127.0.0.1:9080/myapp/index.do/index` with your favorite browser. You should see `Hello World!` there.

> If you don't specify the `name` attributes, depending on the annotation, Rout.Lt uses the class or the method name. As the `Action` suffix has to be cut off, it is important, that the action and the action methods always ends with `Action` and nothing else.

### Wildcards

Up with version 2.2 you can also use wildcards in the `@Action` annotation's name attribute, e. g.

```php
/**
 * Action to fetch an articles relation and return the result, e. g. as JSON API compatible result.
 *
 * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
 * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
 *
 * @return void
 * @Action(name="/articles/:id/:relation")
 */
public function nameDoesntMatterAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
{
    // fetch an articles relation, e. g the author and return's the result
}
```

The values, mapped by the wildcards, are available, as usual request parameters, through the `$servletRequest->getParameter()` method, e. g.

```php
/**
 * Action to fetch an articles relation and return the result, e. g. as JSON API compatible result.
 *
 * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
 * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
 *
 * @return void
 * @Action(name="/articles/:id/:relation")
 */
public function nameDoesntMatterAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
{

    // load ID and relation
    $id = $servletRequest->getParameter('id', FILTER_VALIDATE_INT);
    $relation = $servletRequest->getParameter('relation');
    
    // fetch an articles relation, e. g the author and return's the result
}
```

### Restrictions

Additionally to the wildcards it is possible to define restrictions for each wildcard like

```php
/**
  * @Action(name="/articles/:id/:relation", restrictions={{"id", "\d+"}, {"relation", "\w+"}})
  */
```

If a restriction is defined, the variable is only mapped, if the restriction complies. The functionality behind the restrictions uses regular expressions to make sure, the passed value complies. Therefore, each restriction consist's of the wildcard name and the restriction term itself e. g. `{"id", "\d+"}`. The restriction term must be a valid regular expression term, like `\d+` that makes sure, that the value **MUST** be a digit. If no restriction has been set, Rout.Lt assumes that the passed value is a string and uses the defaul `\w+` term to check the consistency.

### Defaults

Beside the restrictions it is possible to define default values for wildcards, like

```php
/**
  * @Action(
  *     name="/articles/:id/:relation", 
  *     restrictions={{"id", "\d+"}, {"relation", "\w+"}}, 
  *     defaults={{"relation", "author"}}
  * )
  */
```

If the relation has not been given in the path info, like `/articles/3`, the default value `author` will be set and is available as request parameter then.

## Action -> Request Method Mapping

Sometimes it is necessary to allow action invocation only for selected request methods. For example, it has to be possible to configure the `indexAction()` only to be invoked on a `POST` request. To do this, you can add a @Post annotation to the methods doc block, like

```php

/**
 * Dummy action implementation that writes 'Hello World' to the response.
 *
 * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
 * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
 *
 * @return void
 * @Post
 */
public function indexAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
{
    $servletResponse->appendBodyStream('Hello World!');
}
```

Annotations are available for all request methods `CONNECT`, `DELETE`, `GET`, `HEAD`, `OPTIONS`, `POST`, `PUT` and `TRACE`. If you don't add one of the above annotations, an action will be invoked on **ALL** of the request methods.

> If you add one of them, the action will be invoked on that annotation only. On all other request methods, a `AppserverIo\Psr\Servlet\ServletException` with a `404` status code will be thrown, which results in a `404` error page.

## Results

By specifying results with the @Results annotation, a developer is able to specify a post processor, to process action results, e. g. by a template engine. By default, Rout.Lt 2 provides a servlet that uses simple DHTML files as templates and processes them in the scope of the servlet's `process()` method. This allows access to servlet request/response instances as well as servlet configutation parameters.

The following example uses a @Results annotation, which contains a nested @Result annotation, to process the `/path/to/my_template.dhtml` after invoking the `indexAction()` method.

```php

namespace MyApp\Actions;

use AppserverIo\Routlt\DispatchAction;
use AppserverIo\Routlt\ActionInterface;
use TechDivision\Servlet\Http\HttpServletRequestInterface;
use TechDivision\Servlet\Http\HttpServletResponseInterface;

/**
 * Example action that shows the usage of the @Results annotation.
 *
 * @Path
 * @Results({
 *     @Result(name="input", type="AppserverIo\Routlt\Results\ServletDispatcherResult", result="/path/to/my_template.dhtml")
 * })
 */
class IndexAction extends DispatchAction
{

    /**
     * Dummy action implementation that writes 'Hello World' to the response.
     *
     * @param \TechDivision\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \TechDivision\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return string The result identifier
     */
    public function indexAction(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {
        try {
            // add the Hello World! text to the request attributes
            $servletRequest->setAttribute('text', 'Hello World!');
        } catch (\Exception $e) {
            // add the exception to the error messages
            $this->addFieldError('fatal', $e->getMessage());
        }
        // return the result identifier for our template
        return ActionInterface::INPUT;
    }
}
```

It is possible to specify as many @Result annotations as necessary to support allow result processing in different use cases. Which result has to be used, depends on the string value, returned by your action.

The DHTML file, that has to be stored under `<PATH-TO-WEBAPP>/path/to/my_template.dhtml` specified above will load the string, added as request attribute in the `indexAction()` method and renders it as a HTML document.

```php
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Simple DHTML Servlet</title>
    </head>
    <body>
        <p><?php echo $servletRequest->getAttribute('text') ?></p>
    </body>
</html>
```

## Limitations

Rout.Lt 2 starts with annotations as only configuration option. There is **NO** possiblity to configure the actions with a deployment descriptor. Issue [#21](appserver-io/routlt#21) has already been created to find solution therefore.

# External Links

* All about appserver.io at [appserver.io](http://www.appserver.io)