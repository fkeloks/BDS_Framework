<?php

/* globals/default.twig */
class __TwigTemplate_23f86d51f9723b0982921ee6b7e8acc9a74a947989913165bfd30f7c0ec2909b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'css' => array($this, 'block_css'),
            'body' => array($this, 'block_body'),
            'js' => array($this, 'block_js'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"";
        // line 2
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('getLocale')->getCallable(), array()), "html", null, true);
        echo "\">
<head>
    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <meta name=\"description\" content=\"\">
    <meta name=\"author\" content=\"fkeloks\">

    <title>";
        // line 10
        $this->displayBlock('title', $context, $blocks);
        echo "</title>

    <link rel=\"icon\" type=\"image/png\" href=\"";
        // line 12
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('assets')->getCallable(), array("favicon.png")), "html", null, true);
        echo "\" />

    ";
        // line 14
        $this->displayBlock('css', $context, $blocks);
        // line 24
        echo "
</head>

<body>

";
        // line 29
        $this->displayBlock('body', $context, $blocks);
        // line 41
        echo "
";
        // line 42
        $this->displayBlock('js', $context, $blocks);
        // line 43
        echo "
</body>
</html>";
    }

    // line 10
    public function block_title($context, array $blocks = array())
    {
        echo "BDS Framework";
    }

    // line 14
    public function block_css($context, array $blocks = array())
    {
        // line 15
        echo "    <link href=\"https://fonts.googleapis.com/css?family=Raleway:100,600\" rel=\"stylesheet\" type=\"text/css\">
    <link rel=\"stylesheet\" href=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('assets')->getCallable(), array("css/app.css")), "html", null, true);
        echo "\" type=\"text/css\">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>
    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>
    <![endif]-->
    ";
    }

    // line 29
    public function block_body($context, array $blocks = array())
    {
        // line 30
        echo "    <span class=\"header\"></span>

    <div class=\"block-center\">
        <h1>BDS Framework</h1>
        <span>An easy-to-use framework, by Florian B</span>
    </div>

    <footer>
        <span>BDS Version 1.0 | 2017</span>
    </footer>
";
    }

    // line 42
    public function block_js($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "globals/default.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  110 => 42,  96 => 30,  93 => 29,  81 => 16,  78 => 15,  75 => 14,  69 => 10,  63 => 43,  61 => 42,  58 => 41,  56 => 29,  49 => 24,  47 => 14,  42 => 12,  37 => 10,  26 => 2,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "globals/default.twig", "C:\\wamp64\\www\\Projets\\BDS_Framework\\app\\views\\globals\\default.twig");
    }
}
