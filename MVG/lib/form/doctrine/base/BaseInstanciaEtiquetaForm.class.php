<?php

/**
 * InstanciaEtiqueta form base class.
 *
 * @method InstanciaEtiqueta getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseInstanciaEtiquetaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'token'                => new sfWidgetFormInputText(),
      'relacionmesavideo_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RelacionMesaVideo'), 'add_empty' => true)),
      'texto'                => new sfWidgetFormInputText(),
      'tiempo'               => new sfWidgetFormTime(),
      'eliminado'            => new sfWidgetFormInputCheckbox(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'                => new sfValidatorString(array('max_length' => 255)),
      'relacionmesavideo_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('RelacionMesaVideo'), 'required' => false)),
      'texto'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'tiempo'               => new sfValidatorTime(array('required' => false)),
      'eliminado'            => new sfValidatorBoolean(array('required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'InstanciaEtiqueta', 'column' => array('id'))),
        new sfValidatorDoctrineUnique(array('model' => 'InstanciaEtiqueta', 'column' => array('token'))),
      ))
    );

    $this->widgetSchema->setNameFormat('instancia_etiqueta[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'InstanciaEtiqueta';
  }

}
