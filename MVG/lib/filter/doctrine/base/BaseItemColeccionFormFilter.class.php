<?php

/**
 * ItemColeccion filter form base class.
 *
 * @package    MusicVideoGame
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseItemColeccionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'intervalo1'     => new sfWidgetFormFilterInput(),
      'intervalo2'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Intervalo'), 'add_empty' => true)),
      'respuesta_real' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'coleccion_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Coleccion'), 'add_empty' => true)),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'intervalo1'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'intervalo2'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Intervalo'), 'column' => 'id')),
      'respuesta_real' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'coleccion_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Coleccion'), 'column' => 'id')),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('item_coleccion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemColeccion';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'intervalo1'     => 'Number',
      'intervalo2'     => 'ForeignKey',
      'respuesta_real' => 'Boolean',
      'coleccion_id'   => 'ForeignKey',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
