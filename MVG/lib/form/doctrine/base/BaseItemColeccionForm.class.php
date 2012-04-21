<?php

/**
 * ItemColeccion form base class.
 *
 * @method ItemColeccion getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseItemColeccionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'intervalo1'     => new sfWidgetFormInputText(),
      'intervalo2'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'add_empty' => true)),
      'respuesta_real' => new sfWidgetFormInputCheckbox(),
      'coleccion_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Coleccion'), 'add_empty' => true)),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'intervalo1'     => new sfValidatorInteger(array('required' => false)),
      'intervalo2'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'required' => false)),
      'respuesta_real' => new sfValidatorBoolean(array('required' => false)),
      'coleccion_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Coleccion'), 'required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ItemColeccion', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('item_coleccion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemColeccion';
  }

}
