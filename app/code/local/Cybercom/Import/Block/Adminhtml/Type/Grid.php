<?php

class Cybercom_Import_Block_Adminhtml_Type_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
{
      parent::__construct();
      $this->setId('importTypeGrid');
      $this->setDefaultSort('type_id');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection=Mage::getResourceModel('import/import_type_collection')->addFieldToFilter('type',array('eq'=>Cybercom_Import_Model_Import_Type::TYPE_IMPORT_TEXT));
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('type_id', array(
          'header'    => Mage::helper('import')->__('Id'),
          'align'     =>'right',
          'type'      => 'number',
          'width'     => '10px',
          'index'     => 'type_id'
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('import')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));
      
      $this->addColumn('record_load_interval', array(
          'header'    => Mage::helper('import')->__('Request Interval ( In Sec )'),
          'align'     =>'right',
          'index'     => 'record_load_interval',
          'width'     => '20px',
          'type'     => 'number',
      ));
      

      $this->addColumn('per_load_item', array(
          'header'    => Mage::helper('import')->__('Per Load Item'),
          'align'     =>'left',
          'width'     => '15px',
          'index'     => 'per_load_item',
          'type'      =>'number',
      ));
      $this->addColumn('is_processing', array(
          'header'   => Mage::helper('import')->__('Is Proccessing'),
          'align'    =>'center',
          'type'     => 'options',
          'width'     => '15px',
          'index'    =>'is_processing',
          'options'  => Mage::getModel('import/import_type')->getProcessingOption(),
          
          
      ));
      
      $this->addColumn('class_name', array(
          'header'    => Mage::helper('import')->__('Class Name'),
          'align'     =>'left',
          'index'     => 'class_name',
      ));
      
      $this->addColumn('import_file_name', array(
          'header'    => Mage::helper('import')->__('Import File Name'),
          'align'     =>'left',
          'index'     => 'import_file_name',
      ));
		
      $this->addColumn('action',
        array(
                  'header'      => Mage::helper('import')->__('Action'),
                  'width'       => '300px',
                  'type'        => 'action',
                  'getter'      => 'getId',
                  'renderer'    => 'Cybercom_Import_Block_Adminhtml_Type_Grid_Action',
                  'filter'      => false,
                  'sortable'    => false,
                  'index'       => 'stores',
                  'is_system'   => true,
        ));
        
      return parent::_prepareColumns();
  }
  
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('type' => $row->getId()));
  }
  
  protected function _prepareMassaction()
  {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        
        
        $this->getMassactionBlock()->addItem('empty', array(
            'label'=> Mage::helper('import')->__('Empty Used Data'),
            'url'  => $this->getUrl('*/*/massEmpty', array('' => '')),
            'confirm' => Mage::helper('import')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('removeProcessReport', array(
            'label'=> Mage::helper('import')->__('Remove Process Report'),
            'url'  => $this->getUrl('*/*/massRemoveProcessReport', array('' => '')),
            'confirm' => Mage::helper('import')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('removeImportReport', array(
            'label'=> Mage::helper('import')->__('Remove Import Report'),
            'url'  => $this->getUrl('*/*/massRemoveImportReport', array('' => '')),
            'confirm' => Mage::helper('import')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('removeImportData', array(
            'label'=> Mage::helper('import')->__('Remove All Data'),
            'url'  => $this->getUrl('*/*/massRemoveImportData', array('' => '')),
            'confirm' => Mage::helper('import')->__('Are you sure?')
        ));
        
        /*$this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('import')->__('Delete'),
            'url'  => $this->getUrl('*//*/massDelete', array('' => '')),
            'confirm' => Mage::helper('import')->__('Are you sure?')
        ));*/
        
        return $this;
  }
}
