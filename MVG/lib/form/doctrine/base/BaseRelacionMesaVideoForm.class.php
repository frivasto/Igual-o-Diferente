<?php

/**
 * RelacionMesaVideo form base class.
 *
 * @method RelacionMesaVideo getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRelacionMesaVideoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'token'          => new sfWidgetFormInputText(),
      'mesa_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mesa'), 'add_empty' => true)),
      'jugador_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'add_empty' => true)),
      'num_round'      => new sfWidgetFormInputText(),
      'intervalo_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'add_empty' => true)),
      'respuesta_real' => new sfWidgetFormInputCheckbox(),
      'eliminado'      => new sfWidgetFormInputCheckbox(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'          => new sfValidatorString(array('max_length' => 255)),
      'mesa_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mesa'), 'required' => false)),
      'jugador_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'required' => false)),
      'num_round'      => new sfValidatorInteger(array('required' => false)),
      'intervalo_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'required' => false)),
      'respuesta_real' => new sfValidatorBoolean(array('required' => false)),
      'eliminado'      => new sfValidatorBoolean(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'RelacionMesaVideo', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('relacion_mesa_video[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RelacionMesaVideo';
  }

}
