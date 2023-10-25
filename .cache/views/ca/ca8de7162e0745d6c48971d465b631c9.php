<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* index.twig */
class __TwigTemplate_d31d1b278ac22ec0cc90dc65815396a8 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 6
        echo twig_escape_filter($this->env, (isset($context["title"]) || array_key_exists("title", $context) ? $context["title"] : (function () { throw new RuntimeError('Variable "title" does not exist.', 6, $this->source); })()), "html", null, true);
        echo "</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: antiquewhite;
        }
        #container {
            text-align: center;
            line-height: 35px;
            display: flex;
            height: 250px;
            align-items: center;
            justify-content: center;
        }
        h1 {
            color: tomato;
        }
        a {
            color: #0505b5;
        }
    </style>
</head>
<body>
    <div id=\"container\">
        <div class=\"content\">
            <h1>Welcome to framework Ilustro</h1>
            <p>
                Build your website quickly and extend your application by adding libraries to the framework
            </p>
            <br>
            <em>visit the repository on <a target=\"_blank\" href=\"https://github.com/everskyblue/Ilustro\">github</a></em>
        </div>
    </div>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{ title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: antiquewhite;
        }
        #container {
            text-align: center;
            line-height: 35px;
            display: flex;
            height: 250px;
            align-items: center;
            justify-content: center;
        }
        h1 {
            color: tomato;
        }
        a {
            color: #0505b5;
        }
    </style>
</head>
<body>
    <div id=\"container\">
        <div class=\"content\">
            <h1>Welcome to framework Ilustro</h1>
            <p>
                Build your website quickly and extend your application by adding libraries to the framework
            </p>
            <br>
            <em>visit the repository on <a target=\"_blank\" href=\"https://github.com/everskyblue/Ilustro\">github</a></em>
        </div>
    </div>
</body>
</html>
", "index.twig", "/storage/emulated/0/.app/Ilustro/static/views/index.twig");
    }
}
