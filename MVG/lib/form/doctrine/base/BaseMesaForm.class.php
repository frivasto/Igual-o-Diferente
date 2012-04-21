<?php

/**
 * Mesa form base class.
 *
 * @method Mesa getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMesaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'token'        => new sfWidgetFormInputText(),
      'jugador1_id'  => new sfWidgetFormInputText(),
      'jugador2_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'add_empty' => true)),
      'tiempo'       => new sfWidgetFormTime(),
      'coleccion_id' => new sfWidgetFormInputText(),
      'estado'       => new sfWidgetFormInputText(),
      'eliminado'    => new sfWidgetFormInputCheckbox(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'        => new sfValidatorString(array('max_length' => 255)),
      'jugador1_id'  => new sfValidatorInteger(array('required' => false)),
      'jugador2_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'required' => false)),
      'tiempo'       => new sfValidatorTime(array('required' => false)),
      'coleccion_id' => new sfValidatorInteger(array('required' => false)),
      'estado'       => new sfValidatorInteger(array('required' => false)),
      'eliminado'    => new sfValidatorBoolean(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Mesa', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('mesa[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Mesa';
  }

}
