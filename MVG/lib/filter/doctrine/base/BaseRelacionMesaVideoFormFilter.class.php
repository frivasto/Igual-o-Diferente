<?php

/**
 * RelacionMesaVideo filter form base class.
 *
 * @package    MusicVideoGame
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRelacionMesaVideoFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mesa_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mesa'), 'add_empty' => true)),
      'video_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Video'), 'add_empty' => true)),
      'jugador_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Jugador'), 'add_empty' => true)),
      'intervalo_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'add_empty' => true)),
      'respuesta_real' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'eliminado'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'token'          => new sfValidatorPass(array('required' => false)),
      'mesa_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mesa'), 'column' => 'id')),
      'video_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Video'), 'column' => 'id')),
      'jugador_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Jugador'), 'column' => 'id')),
      'intervalo_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Intervalo'), 'column' => 'id')),
      'respuesta_real' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'eliminado'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('relacion_mesa_video_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'RelacionMesaVideo';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'token'          => 'Text',
      'mesa_id'        => 'ForeignKey',
      'video_id'       => 'ForeignKey',
      'jugador_id'     => 'ForeignKey',
      'intervalo_id'   => 'ForeignKey',
      'respuesta_real' => 'Boolean',
      'eliminado'      => 'Boolean',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
