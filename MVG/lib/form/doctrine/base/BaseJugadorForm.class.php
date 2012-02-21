<?php

/**
 * Jugador form base class.
 *
 * @method Jugador getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseJugadorForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'token'      => new sfWidgetFormInputText(),
      'user_id'    => new sfWidgetFormInputText(),
      'nombre'     => new sfWidgetFormInputText(),
      'eliminado'  => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'      => new sfValidatorString(array('max_length' => 255)),
      'user_id'    => new sfValidatorString(array('max_length' => 255)),
      'nombre'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'eliminado'  => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Jugador', 'column' => array('id'))),
        new sfValidatorDoctrineUnique(array('model' => 'Jugador', 'column' => array('user_id'))),
      ))
    );

    $this->widgetSchema->setNameFormat('jugador[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Jugador';
  }

}
