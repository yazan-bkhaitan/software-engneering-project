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

/* install/step_4.twig */
class __TwigTemplate_03cf3d9eb20deb809a243022fe0ec8ad6405b6f09323f1afef1e56cefa4798a0 extends \Twig\Template
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
        echo ($context["header"] ?? null);
        echo "
<div class=\"container\">
  <header>
    <div class=\"row\">
      <div class=\"col-sm-6\">
        <h1 class=\"pull-left\">4
          <small>/4</small>
        </h1>
        <h3>";
        // line 9
        echo ($context["heading_title"] ?? null);
        echo "
          <br>
          <small>";
        // line 11
        echo ($context["text_step_4"] ?? null);
        echo "</small>
        </h3>
      </div>
      <div class=\"col-sm-6\">
        <div id=\"logo\" class=\"pull-right hidden-xs\"><img src=\"view/image/logo.png\" alt=\"OpenCart\" title=\"OpenCart\"/></div>
      </div>
    </div>
  </header>
  <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
        // line 19
        echo ($context["error_warning"] ?? null);
        echo " <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>
  <div class=\"visit\">
    <div class=\"row\">
      <div class=\"col-sm-5 col-sm-offset-1 text-center\">
        <p><i class=\"fa fa-shopping-cart fa-5x\"></i></p>
        <a href=\"../\" class=\"btn btn-secondary\">";
        // line 24
        echo ($context["text_catalog"] ?? null);
        echo "</a>
      </div>
      <div class=\"col-sm-5 text-center\">
        <p><i class=\"fa fa-cog fa-5x white\"></i></p>
        <a href=\"../admin/\" class=\"btn btn-secondary\">";
        // line 28
        echo ($context["text_admin"] ?? null);
        echo "</a>
      </div>
    </div>
  </div>
  <div class=\"core-modules\">";
        // line 32
        echo ($context["promotion"] ?? null);
        echo "</div>
  <div class=\"modules\"><a href=\"https://www.opencart.com/index.php?route=marketplace/extension&utm_source=opencart_install&utm_medium=store_link&utm_campaign=opencart_install\" target=\"_BLANK\" class=\"btn btn-default\">";
        // line 33
        echo ($context["text_extension"] ?? null);
        echo "</a></div>
  <div class=\"mailing\">
    <div class=\"row\">
      <div class=\"col-sm-12\"><i class=\"fa fa-envelope-o fa-5x\"></i>
        <h3>&#160;&#160;";
        // line 37
        echo ($context["text_mail"] ?? null);
        echo "<br>
          <small>&#160;&#160;";
        // line 38
        echo ($context["text_mail_description"] ?? null);
        echo "</small></h3>
        <a href=\"https://www.opencartarab.com/lists/dada/mail.cgi/list/newsletter/\" target=\"_BLANK\" class=\"btn btn-secondary\">";
        // line 39
        echo ($context["button_mail"] ?? null);
        echo "</a>
    </div>
    </div>
  </div>
  <div class=\"support text-center\">
    <div class=\"row\">
      <div class=\"col-sm-4\">
        <a href=\"https://web.facebook.com/opencartarab/\" class=\"icon transition\"><i class=\"fa fa-facebook fa-4x\"></i></a>
        <h3>";
        // line 47
        echo ($context["text_facebook"] ?? null);
        echo "</h3>
        <p>";
        // line 48
        echo ($context["text_facebook_description"] ?? null);
        echo "</p>
        <a href=\"https://web.facebook.com/opencartarab/\">";
        // line 49
        echo ($context["text_facebook_visit"] ?? null);
        echo "</a>
      </div>
      <div class=\"col-sm-4\">
        <a href=\"https://forum.opencartarab.com/index.php\" class=\"icon transition\"><i class=\"fa fa-comments fa-4x\"></i></a>
        <h3>";
        // line 53
        echo ($context["text_forum"] ?? null);
        echo "</h3>
        <p>";
        // line 54
        echo ($context["text_forum_description"] ?? null);
        echo "</p>
        <a href=\"https://forum.opencartarab.com/index.php\">";
        // line 55
        echo ($context["text_forum_visit"] ?? null);
        echo "</a>
      </div>
      <div class=\"col-sm-4\">
        <a href=\"https://www.opencart.com/index.php?route=support/partner/info&partner_id=79\" class=\"icon transition\"><i class=\"fa fa-user fa-4x\"></i></a>
        <h3>";
        // line 59
        echo ($context["text_commercial"] ?? null);
        echo "</h3>
        <p>";
        // line 60
        echo ($context["text_commercial_description"] ?? null);
        echo "</p>
        <a href=\"https://www.opencart.com/index.php?route=support/partner/info&partner_id=79\" target=\"_BLANK\">";
        // line 61
        echo ($context["text_commercial_visit"] ?? null);
        echo "</a>
      </div>
    </div>
  </div>
</div>
";
        // line 66
        echo ($context["footer"] ?? null);
        echo "
";
    }

    public function getTemplateName()
    {
        return "install/step_4.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  162 => 66,  154 => 61,  150 => 60,  146 => 59,  139 => 55,  135 => 54,  131 => 53,  124 => 49,  120 => 48,  116 => 47,  105 => 39,  101 => 38,  97 => 37,  90 => 33,  86 => 32,  79 => 28,  72 => 24,  64 => 19,  53 => 11,  48 => 9,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "install/step_4.twig", "");
    }
}
