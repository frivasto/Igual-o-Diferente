<?php

/**
 * Decision filter form base class.
 *
 * @package    MusicVideoGame
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDecisionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'relacionmesavideo_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RelacionMesaVideo'), 'add_empty' => true)),
      'tiempo'               => new sfWidgetFormFilterInput(),
      'respuesta'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'eliminado'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'token'                => new sfValidatorPass(array('required' => false)),
      'relacionmesavideo_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('RelacionMesaVideo'), 'column' => 'id')),
      'tiempo'               => new sfValidatorPass(array('required' => false)),
      'respuesta'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'eliminado'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('decision_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Decision';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'token'                => 'Text',
      'relacionmesavideo_id' => 'ForeignKey',
      'tiempo'               => 'Text',
      'respuesta'            => 'Boolean',
      'eliminado'            => 'Boolean',
      'created_at'           => 'Date',
      'updated_at'           => 'Date',
    );
  }
}
