<?php

namespace voku\helper;

use BadMethodCallException;
use DOMElement;
use DOMNode;
use RuntimeException;

/**
 * Class SimpleHtmlDom
 *
 * @package voku\helper
 *
 * @property string      outerText Get dom node's outer html (alias for "outerHtml")
 * @property string      outerHtml Get dom node's outer html
 * @property string      innerText Get dom node's inner html (alias for "innerHtml")
 * @property string      innerHtml Get dom node's inner html
 * @property-read string plaintext Get dom node's plain text
 * @property-read string tag       Get dom node name
 * @property-read string attr      Get dom node attributes
 *
 * @method SimpleHtmlDomNode|SimpleHtmlDom|null children() children($idx = -1) Returns children of node
 * @method SimpleHtmlDom|null first_child() Returns the first child of node
 * @method SimpleHtmlDom|null last_child() Returns the last child of node
 * @method SimpleHtmlDom|null next_sibling() Returns the next sibling of node
 * @method SimpleHtmlDom|null prev_sibling() Returns the previous sibling of node
 * @method SimpleHtmlDom|null parent() Returns the parent of node
 * @method string outerText() Get dom node's outer html (alias for "outerHtml()")
 * @method string outerHtml() Get dom node's outer html
 * @method string innerText() Get dom node's inner html (alias for "innerHtml()")
 *
 */
class SimpleHtmlDom implements \IteratorAggregate
{
  /**
   * @var array
   */
  protected static $functionAliases = array(
      'children'     => 'childNodes',
      'first_child'  => 'firstChild',
      'last_child'   => 'lastChild',
      'next_sibling' => 'nextSibling',
      'prev_sibling' => 'previousSibling',
      'parent'       => 'parentNode',
      'outertext'    => 'html',
      'outerhtml'    => 'html',
      'innertext'    => 'innerHtml',
      'innerhtml'    => 'innerHtml',
  );

  /**
   * @var DOMElement
   */
  protected $node;

  /**
   * SimpleHtmlDom constructor.
   *
   * @param DOMNode $node
   */
  public function __construct(DOMNode $node)
  {
    $this->node = $node;
  }

  /**
   * @param $name
   * @param $arguments
   *
   * @return null|string|SimpleHtmlDom
   *
   */
  public function __call($name, $arguments)
  {
    $name = strtolower($name);

    if (isset(self::$functionAliases[$name])) {
      return call_user_func_array(array($this, self::$functionAliases[$name]), $arguments);
    }

    throw new BadMethodCallException('Method does not exist');
  }

  /**
   * @param string $name
   *
   * @return array|null|string
   */
  public function __get($name)
  {
    $name = strtolower($name);

    switch ($name) {
      case 'outerhtml':
      case 'outertext':
        return $this->html();
      case 'innerhtml':
      case 'innertext':
        return $this->innerHtml();
      case 'text':
      case 'plaintext':
        return $this->text();
      case 'tag':
        return $this->node->nodeName;
      case 'attr':
        return $this->getAllAttributes();
      default:
        return $this->getAttribute($name);
    }
  }

  /**
   * @param string $selector
   * @param int    $idx
   *
   * @return SimpleHtmlDom|SimpleHtmlDomNode|null
   */
  public function __invoke($selector, $idx = null)
  {
    return $this->find($selector, $idx);
  }

  /**
   * @param $name
   *
   * @return bool
   */
  public function __isset($name)
  {
    $name = strtolower($name);

    switch ($name) {
      case 'outertext':
      case 'outerhtml':
      case 'innertext':
      case 'innerhtml':
      case 'plaintext':
      case 'text':
      case 'tag':
        return true;
      default:
        return $this->hasAttribute($name);
    }
  }

  /**
   * @param $name
   * @param $value
   *
   * @return SimpleHtmlDom
   */
  public function __set($name, $value)
  {
    $name = strtolower($name);

    switch ($name) {
      case 'outerhtml':
      case 'outertext':
        return $this->replaceNode($value);
      case 'innertext':
      case 'innerhtml':
        return $this->replaceChild($value);
      default:
        return $this->setAttribute($name, $value);
    }
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->html();
  }

  /**
   * @param $name
   *
   * @return SimpleHtmlDom
   */
  public function __unset($name)
  {
    return $this->removeAttribute($name);
  }

  /**
   * Returns children of node
   *
   * @param int $idx
   *
   * @return SimpleHtmlDomNode|SimpleHtmlDom|null
   */
  public function childNodes($idx = -1)
  {
    $nodeList = $this->getIterator();

    if ($idx === -1) {
      return $nodeList;
    }

    if (isset($nodeList[$idx])) {
      return $nodeList[$idx];
    }

    return null;
  }

  /**
   * Find list of nodes with a CSS selector
   *
   * @param string   $selector
   * @param int|null $idx
   *
   * @return SimpleHtmlDomNode|SimpleHtmlDomNode[]|SimpleHtmlDomNodeBlank
   */
  public function find($selector, $idx = null)
  {
    return $this->getHtmlDomParser()->find($selector, $idx);
  }

  /**
   * Returns the first child of node
   *
   * @return SimpleHtmlDom|null
   */
  public function firstChild()
  {
    $node = $this->node->firstChild;

    if ($node === null) {
      return null;
    }

    return new self($node);
  }

  /**
   * Returns array of attributes
   *
   * @return array|null
   */
  public function getAllAttributes()
  {
    if ($this->node->hasAttributes()) {
      $attributes = array();
      foreach ($this->node->attributes as $attr) {
        $attributes[$attr->name] = HtmlDomParser::putReplacedBackToPreserveHtmlEntities($attr->value);
      }

      return $attributes;
    }

    return null;
  }

  /**
   * Return attribute value
   *
   * @param string $name
   *
   * @return string
   */
  public function getAttribute($name)
  {
    $html = $this->node->getAttribute($name);

    return HtmlDomParser::putReplacedBackToPreserveHtmlEntities($html);
  }

  /**
   * Return SimpleHtmlDom by id.
   *
   * @param string $id
   *
   * @return SimpleHtmlDomNode|SimpleHtmlDomNode[]|SimpleHtmlDomNodeBlank
   */
  public function getElementById($id)
  {
    return $this->find("#$id", 0);
  }

  /**
   * Return SimpleHtmlDom by tag name.
   *
   * @param string $name
   *
   * @return SimpleHtmlDom|SimpleHtmlDomNodeBlank
   */
  public function getElementByTagName($name)
  {
    $node = $this->node->getElementsByTagName($name)->item(0);

    if ($node !== null) {
      return new self($node);
    } else {
      return new SimpleHtmlDomNodeBlank();
    }
  }

  /**
   * Returns Elements by id
   *
   * @param string   $id
   * @param null|int $idx
   *
   * @return SimpleHtmlDomNode|SimpleHtmlDomNode[]|SimpleHtmlDomNodeBlank
   */
  public function getElementsById($id, $idx = null)
  {
    return $this->find("#$id", $idx);
  }

  /**
   * Returns Elements by tag name
   *
   * @param string   $name
   * @param null|int $idx
   *
   * @return SimpleHtmlDomNode|SimpleHtmlDomNode[]|SimpleHtmlDomNodeBlank
   */
  public function getElementsByTagName($name, $idx = null)
  {
    $nodesList = $this->node->getElementsByTagName($name);

    $elements = new SimpleHtmlDomNode();

    foreach ($nodesList as $node) {
      $elements[] = new self($node);
    }

    if (null === $idx) {
      return $elements;
    } else {
      if ($idx < 0) {
        $idx = count($elements) + $idx;
      }
    }

    if (isset($elements[$idx])) {
      return $elements[$idx];
    } else {
      return new SimpleHtmlDomNodeBlank();
    }
  }

  /**
   * Create a new "HtmlDomParser"-object from the current context.
   *
   * @return HtmlDomParser
   */
  public function getHtmlDomParser()
  {
    return new HtmlDomParser($this);
  }

  /**
   * Retrieve an external iterator
   *
   * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
   * @return SimpleHtmlDomNode An instance of an object implementing <b>Iterator</b> or
   * <b>Traversable</b>
   */
  public function getIterator()
  {
    $elements = new SimpleHtmlDomNode();
    if ($this->node->hasChildNodes()) {
      foreach ($this->node->childNodes as $node) {
        $elements[] = new self($node);
      }
    }

    return $elements;
  }

  /**
   * @return DOMNode
   */
  public function getNode()
  {
    return $this->node;
  }

  /**
   * Determine if an attribute exists on the element.
   *
   * @param $name
   *
   * @return bool
   */
  public function hasAttribute($name)
  {
    return $this->node->hasAttribute($name);
  }

  /**
   * Get dom node's outer html
   *
   * @return string
   */
  public function html()
  {
    return $this->getHtmlDomParser()->html();
  }

  /**
   * Get dom node's inner html
   *
   * @return string
   */
  public function innerHtml()
  {
    return $this->getHtmlDomParser()->innerHtml();
  }

  /**
   * Returns the last child of node
   *
   * @return SimpleHtmlDom|null
   */
  public function lastChild()
  {
    $node = $this->node->lastChild;

    if ($node === null) {
      return null;
    }

    return new self($node);
  }

  /**
   * Returns the next sibling of node
   *
   * @return SimpleHtmlDom|null
   */
  public function nextSibling()
  {
    $node = $this->node->nextSibling;

    if ($node === null) {
      return null;
    }

    return new self($node);
  }

  /**
   * Returns the parent of node
   *
   * @return SimpleHtmlDom
   */
  public function parentNode()
  {
    return new self($this->node->parentNode);
  }

  /**
   * Returns the previous sibling of node
   *
   * @return SimpleHtmlDom|null
   */
  public function previousSibling()
  {
    $node = $this->node->previousSibling;

    if ($node === null) {
      return null;
    }

    return new self($node);
  }

  /**
   * Replace child node
   *
   * @param $string
   *
   * @return $this
   */
  protected function replaceChild($string)
  {
    if (!empty($string)) {
      $newDocument = new HtmlDomParser($string);

      if ($this->normalizeStringForComparision($newDocument->outerText()) != $this->normalizeStringForComparision($string)) {
        throw new RuntimeException('Not valid HTML fragment');
      }
    }

    /** @noinspection PhpParamsInspection */
    if (count($this->node->childNodes) > 0) {
      foreach ($this->node->childNodes as $node) {
        $this->node->removeChild($node);
      }
    }

    if (!empty($newDocument)) {
      $newDocument = $this->cleanHtmlWrapper($newDocument);
      $newNode = $this->node->ownerDocument->importNode($newDocument->getDocument()->documentElement, true);
      $this->node->appendChild($newNode);
    }

    return $this;
  }

  /**
   * Replace this node
   *
   * @param $string
   *
   * @return $this
   */
  protected function replaceNode($string)
  {
    if (empty($string)) {
      $this->node->parentNode->removeChild($this->node);

      return null;
    }

    $newDocument = new HtmlDomParser($string);

    if ($this->normalizeStringForComparision($newDocument->outerText()) != $this->normalizeStringForComparision($string)) {
      throw new RuntimeException('Not valid HTML fragment');
    }

    $newDocument = $this->cleanHtmlWrapper($newDocument);

    $newNode = $this->node->ownerDocument->importNode($newDocument->getDocument()->documentElement, true);

    $this->node->parentNode->replaceChild($newNode, $this->node);
    $this->node = $newNode;

    return $this;
  }

  /**
   * Normalize the given string for comparision.
   *
   * @param $string
   *
   * @return string
   */
  private function normalizeStringForComparision($string)
  {
    return urlencode(urldecode(trim(str_replace(array(' ', "\n", "\r\n", "\r"), '', strtolower($string)))));
  }

  /**
   * @param HtmlDomParser $newDocument
   *
   * @return HtmlDomParser
   */
  protected function cleanHtmlWrapper(HtmlDomParser $newDocument)
  {
    if ($newDocument->getIsDOMDocumentCreatedWithoutHtml() === true) {

      // Remove doc-type node.
      $newDocument->getDocument()->doctype->parentNode->removeChild($newDocument->getDocument()->doctype);

      // Remove html element, preserving child nodes.
      $html = $newDocument->getDocument()->getElementsByTagName('html')->item(0);
      $fragment = $newDocument->getDocument()->createDocumentFragment();
      while ($html->childNodes->length > 0) {
        $fragment->appendChild($html->childNodes->item(0));
      }
      $html->parentNode->replaceChild($fragment, $html);

      // Remove body element, preserving child nodes.
      $body = $newDocument->getDocument()->getElementsByTagName('body')->item(0);
      $fragment = $newDocument->getDocument()->createDocumentFragment();
      while ($body->childNodes->length > 0) {
        $fragment->appendChild($body->childNodes->item(0));
      }
      $body->parentNode->replaceChild($fragment, $body);

      // At this point DOMDocument still added a "<p>"-wrapper around our string,
      // so we replace it with "<simpleHtmlDomP>" and delete this at the ending ...
      $this->changeElementName($newDocument->getDocument()->getElementsByTagName('p')->item(0), 'simpleHtmlDomP');
    }

    return $newDocument;
  }

  /**
   * change the name of a tag in a "DOMNode"
   *
   * @param DOMNode $node
   * @param string  $name
   *
   * @return DOMElement
   */
  protected function changeElementName(\DOMNode $node, $name)
  {
    $newnode = $node->ownerDocument->createElement($name);
    foreach ($node->childNodes as $child) {
      $child = $node->ownerDocument->importNode($child, true);
      $newnode->appendChild($child);
    }
    foreach ($node->attributes as $attrName => $attrNode) {
      $newnode->setAttribute($attrName, $attrNode);
    }
    $newnode->ownerDocument->replaceChild($newnode, $node);

    return $newnode;
  }

  /**
   * Set attribute value
   *
   * @param string      $name
   * @param string|null $value      Set to NULL or empty string, to remove the attribute.
   * @param bool        $strict     $value must be NULL, to remove the attribute,
   *                                so that you can set an empty string as attribute-value e.g. autofocus=""
   *
   * @return $this
   */
  public function setAttribute($name, $value = null, $strict = false)
  {
    if (
        ($strict === true && null === $value)
        ||
        ($strict === false && empty($value))
    ) {
      $this->node->removeAttribute($name);
    } else {
      $this->node->setAttribute($name, $value);
    }

    return $this;
  }

  /**
   * Remove attribute
   *
   * @param $name
   *
   * @return mixed
   */
  public function removeAttribute($name)
  {
    $this->node->removeAttribute($name);

    return $this;
  }

  /**
   * Get dom node's plain text
   *
   * @return string
   */
  public function text()
  {
    return $this->node->textContent;
  }
}
