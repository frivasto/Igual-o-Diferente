<?php

/**
 * Intervalo form base class.
 *
 * @method Intervalo getObject() Returns the current form's model object
 *
 * @package    MusicVideoGame
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseIntervaloForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'token'      => new sfWidgetFormInputText(),
      'video_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Video'), 'add_empty' => true)),
      'inicio'     => new sfWidgetFormTime(),
      'fin'        => new sfWidgetFormTime(),
      'eliminado'  => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'      => new sfValidatorString(array('max_length' => 255)),
      'video_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Video'), 'required' => false)),
      'inicio'     => new sfValidatorTime(array('required' => false)),
      'fin'        => new sfValidatorTime(array('required' => false)),
      'eliminado'  => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Intervalo', 'column' => array('id'))),
        new sfValidatorDoctrineUnique(array('model' => 'Intervalo', 'column' => array('token'))),
      ))
    );

    $this->widgetSchema->setNameFormat('intervalo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Intervalo';
  }

}
