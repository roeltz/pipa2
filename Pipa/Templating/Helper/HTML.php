<?php

namespace Pipa\Templating\Helper;
use Pipa\Templating\Helper;

class HTML extends Helper {

    static function text($value) {
        return htmlentities($value, ENT_HTML5 | ENT_QUOTES, "UTF-8");
    }

    function checkbox($attr, $checked = false) {
        return $this->tag("input")
            ->attr("type", "checkbox")
            ->attrOrName($attr)
            ->attr("checked", $checked)
        ;
    }

    function input($type, $attr, $value = false) {
        return $this->tag("input")
            ->attr("type", $type)
            ->attr("value", $value)
            ->attrOrName($attr)
        ;
    }

    function radio($attr, $checked = false) {
        return $this->tag("input")
            ->attr("type", "radio")
            ->attrOrName($attr)
            ->attr("checked", $checked)
        ;
    }

    function select($attr, $options = [], $selected = null, $null = null) {
        $tag = $this->tag("select")
            ->attrOrName($attr)
        ;

        if ($null)
            $tag->child("option")->text($null);

        foreach ($options as $value=>$text) {
            $tag->child("option", [
                "value"=>$value,
                "selected"=>$value === $selected
            ], $text);
        }

        return $tag;
    }

    function tag($name, $attr = [], $children = []) {
        return new HTMLTag($name, $attr, $children);
    }

    function __call($name, $args) {
        return $this->tag($name, ...$args);
    }

    function __invoke($value) {
        return self::text($value);
    }

}

class HTMLTag {

    const UNPAIRED = ["area", "base", "br", "col", "embed", "hr", "img", "input", "link", "meta", "param", "source", "track", "wbr"];

    private $name;
    private $attr;
    private $children;
    private $parent;

    function __construct($name, $attr = [], $children = [], $parent = null) {
        $this->name = $name;
        $this->attr = $attr;
        $this->children = is_array($children) ? $children : [$children];
        $this->parent = $parent;
    }

    function append($child) {
        $this->children[] = $child;
        if ($child instanceof HTMLTag)
            $child->parent = $this;
        return $this;
    }

    function attr($name, $value = null) {
        if (is_string($name)) {
            $this->attr[$name] = $value;
        } elseif (is_array($name)) {
            $this->attr = array_merge($this->attr, $name);
        }
        return $this;
    }

    function attrOrName($attr) {
        if (is_string($attr)) {
            $this->attr("name", $attr);
        } else {
            $this->attr($attr);
        }
        return $this;
    }

    function child($name, $attr = [], $children = []) {
        $child = new HTMLTag($name, $attr, $children, $this);
        $this->children[] = $child;
        return $child;
    }

    function end() {
        if ($this->parent) {
            $this->parent->end();
        } else {
            echo $this;
        }
    }

    function renderAttribute($name, $value) {
        if ($value === true) {
            return $name;
        } else if ($value === false) {
            return "";
        } else {
            return "$name=\"" . HTML::text($value) . "\"";
        }
    }

    function renderAttributes() {
        $attr = array_filter($this->attr, function($v){ return $v !== false; });
        return ($this->attr ? " " : "") . join(" ", array_map(function($value, $name){
            return $this->renderAttribute($name, $value);
        }, $attr, array_keys($attr)));
    }

    function renderChildren() {
        if (is_array($this->children)) {
            return join("", array_map(function($child){
                return is_string($child) ? HTML::text($child) : $child;
            },$this->children));
        }
    }

    function text($value) {
        $this->children[] = $value;
        return $this;
    }

    function __toString() {
        $attr = $this->renderAttributes();
        $html = "<{$this->name}$attr>";

        if (!in_array(strtolower($this->name), self::UNPAIRED) && $this->children !== false) {
            $children = $this->renderChildren();
            $html = "$html$children</{$this->name}>";
        }

        return $html;
    }

}
