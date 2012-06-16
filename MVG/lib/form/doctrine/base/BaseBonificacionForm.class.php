<?php

/**
 * Bonificacion form base class.
 *
 * @method Bonificacion getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBonificacionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'token'       => new sfWidgetFormInputText(),
      'mesa_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mesa'), 'add_empty' => true)),
      'jugador_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'add_empty' => true)),
      'descripcion' => new sfWidgetFormInputText(),
      'fecha'       => new sfWidgetFormDateTime(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'       => new sfValidatorString(array('max_length' => 255)),
      'mesa_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mesa'), 'required' => false)),
      'jugador_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'required' => false)),
      'descripcion' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'fecha'       => new sfValidatorDateTime(),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Bonificacion', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('bonificacion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Bonificacion';
  }

}
