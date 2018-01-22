<?php

/* default/template/extension/module/vendor.twig */
class __TwigTemplate_880b15c4304096921753fea353ee6637c8aa5118c4be40d98d26e7b04d35283e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"list-group\">
  ";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["vendors"]) ? $context["vendors"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["vendor"]) {
            // line 3
            echo "  ";
            if (($this->getAttribute($context["vendor"], "vendor_id", array()) == (isset($context["vendor_id"]) ? $context["vendor_id"] : null))) {
                // line 4
                echo "  <a href=\"";
                echo $this->getAttribute($context["vendor"], "href", array());
                echo "\" class=\"list-group-item active\">";
                echo $this->getAttribute($context["vendor"], "name", array());
                echo "</a>
  ";
            } else {
                // line 5
                echo " 
  <a href=\"";
                // line 6
                echo $this->getAttribute($context["vendor"], "href", array());
                echo "\" class=\"list-group-item\">";
                echo $this->getAttribute($context["vendor"], "name", array());
                echo "</a>
  ";
            }
            // line 8
            echo "  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['vendor'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 9
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "default/template/extension/module/vendor.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 9,  47 => 8,  40 => 6,  37 => 5,  29 => 4,  26 => 3,  22 => 2,  19 => 1,);
    }
}
/* <div class="list-group">*/
/*   {% for vendor in vendors %}*/
/*   {% if vendor.vendor_id == vendor_id %}*/
/*   <a href="{{ vendor.href }}" class="list-group-item active">{{ vendor.name }}</a>*/
/*   {% else %} */
/*   <a href="{{ vendor.href }}" class="list-group-item">{{ vendor.name }}</a>*/
/*   {% endif %}*/
/*   {% endfor %}*/
/* </div>*/
/* */
