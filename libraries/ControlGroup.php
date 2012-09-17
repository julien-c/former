<?php
/**
 *
 * ControlGroup
 *
 * Helper class to build Bootstrap control-groups
 */
namespace Former;

use \HTML, \Form;

class ControlGroup
{
  /**
   * The availables states for a control group
   * @var array
   */
  private $states = array('success', 'warning', 'error', 'info');

  /**
   * The control group attributes
   * @var array
   */
  private $attributes = array();

  /**
   * The current state of the control group
   * @var string
   */
  private $state = null;

  /**
   * The control group label
   * @var string
   */
  private $label = null;

  /**
   * The control group help
   * @var string
   */
  private $help = null;

  /**
   * An array of elements to preprend the field
   * @var array
   */
  private $prepend = array();

  /**
   * An array of elements to append the field
   * @var array
   */
  private $append = array();

  public function __construct($label)
  {
    // Add base class
    $this->attributes['class'] = 'control-group';

    // Set control group label
    $this->setLabel($label);

    return $this;
  }

  /**
   * Opens a control group
   *
   * @return string Opening tag
   */
  public function open()
  {
    // If any errors, set state to errors
    $errors = Former::getErrors();
    if($errors) $this->state('error');

    // Retrieve state and append it to classes
    if($this->state) $this->attributes['class'] .= ' '.$this->state;
    if(Former::field()->isRequired()) $this->attributes['class'] .= ' ' .Former::$requiredClass;

    return '<div'.HTML::attributes($this->attributes). '>';
  }

  /**
   * Closes a control group
   *
   * @return string Closing tag
   */
  public function close()
  {
    return '</div>';
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////// PUBLIC INTERFACE //////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Set the state of the control group
   *
   * @param  string $state A Bootstrap state class
   */
  public function state($state)
  {
    if(!in_array($state, $this->states)) return false;

    $this->state = $state;
  }

  /**
   * Add an inline help
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function inlineHelp($help, $attributes = array())
  {
    $this->help['inline'] = Helpers::inlineHelp($help, $attributes);
  }

  /**
   * Add an block help
   *
   * @param  string $help       The help text
   * @param  array  $attributes Facultative attributes
   */
  public function blockHelp($help, $attributes = array())
  {
    $this->help['block'] = Helpers::blockHelp($help, $attributes);
  }

  /**
   * Prepend elements to the field
   */
  public function prepend()
  {
    $prepend = func_get_args();
    foreach($prepend as $p) {
      $this->prepend[] = '<span class="add-on">'.$p.'</span>';
    }
  }

  /**
   * Append elements to the field
   */
  public function append()
  {
    $append = func_get_args();
    foreach($append as $p) {
      if(!($p instanceof \Bootstrapper\Buttons))
        $p = '<span class="add-on">'.$p.'</span>';
      $this->append[] = $p;
    }
  }

  ////////////////////////////////////////////////////////////////////
  //////////////////////////// INTERFACE /////////////////////////////
  ////////////////////////////////////////////////////////////////////

  /**
   * Adds a label
   *
   * @param  string $label A label
   * @return ControlGroup
   */
  public function setLabel($label, $attributes = array())
  {
    // Attempt to translate the label
    $label = Helpers::translate($label);

    // Set control-group label
    $this->label = array(
      'label' => $label,
      'attributes' => $attributes);
  }

  /**
   * Prints out the current label
   *
   * @param  string $name A field to link the label to
   * @return string       A <label> tag
   */
  public function getLabel($name)
  {
    if(!$this->label) return false;

    extract($this->label);
    $attributes = Helpers::addClass($attributes, 'control-label');

    return \Form::label($name, $label, $attributes);
  }

  /**
   * Prints out the current help
   *
   * @return string A .help-block or .help-inline
   */
  public function getHelp()
  {
    $inline = array_get($this->help, 'inline');
    $block  = array_get($this->help, 'block');

    $errors = Former::getErrors();
    if ($errors) $inline = Helpers::inlineHelp($errors);

    return join(null, array($inline, $block));
  }

  /**
   * Format the field with prepended/appended elements
   *
   * @param  string $field The field to format
   * @return string        Field plus supplementary elements
   */
  public function prependAppend($field)
  {
    if(!$this->prepend and !$this->append) return $field;

    // Prepare wrapping div
    $class = null;
    if($this->prepend) $class = 'input-prepend';
    if($this->append) $class .= ' input-append';

    // Build div
    $return = '<div class="' .$class. '">';
      $return .= join(null, $this->prepend);
      $return .= $field;
      $return .= join(null, $this->append);
    $return .= '</div>';

    return $return;
  }

  /**
   * Get the Field instance from Former
   *
   * @return Field
   */
  private function field()
  {
    return Former::$field;
  }
}