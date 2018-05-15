<?php

class Ccc_Import_Block_Adminhtml_Upload_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('uploadGrid');
      $this->setDefaultSort('no');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $uploadProcesses = array(
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_PRODUCT_SIMPLE_INSERT,
                            'type'=> Ccc_Import_Model_Process::CATALOG_PRODUCT_SIMPLE_INSERT_TEXT
                            ),
                            
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_INSERT,
                            'type'=> Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_INSERT_TEXT
                            ),
                            
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE,
                            'type'=> Ccc_Import_Model_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_TEXT
                            ),
                            
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE,
                            'type'=> Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_TEXT
                            ),
                            
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE,
                            'type'=> Ccc_Import_Model_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE_TEXT
                            ),
                            
                            array(
                            'type_id' => Ccc_Import_Model_Process::CATALOG_PRODUCT_IMAGE_INSERT,
                            'type'=> Ccc_Import_Model_Process::CATALOG_PRODUCT_IMAGE_INSERT_TEXT
                            )
                        );
      
      $collection = new Varien_Data_Collection();
      foreach ($uploadProcesses as $item) {
            $data = new Varien_Object($item);
            $collection->addItem($data);
      }
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('type_id', array(
          'header'    => Mage::helper('import')->__('Id'),
          'align'     =>'right',
          'filter'    => false,
          'sortable'  => false,
          'type'     => 'number',
          'index'   => 'type_id'
      ));

      $this->addColumn('type', array(
          'header'    => Mage::helper('import')->__('Type'),
          'align'     =>'left',
          //'filter'    => false,
          'filter_condition_callback' => array($this, '_typeFilter'),
          'sortable'  => false,
          'index'     => 'type',
      ));
      
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('import')->__('Action'),
                'width'     => '400px',
                'type'      => 'action',
                'getter'    => 'getTypeId',
                'renderer'  => 'Cybercom_Import_Block_Adminhtml_Upload_Grid_Action',
                //'actions'   => array(
//                    array(
//                        'caption'   => Mage::helper('import')->__('Read Csv'),
//                        'url'       => array('base'=> '*/*/save'),
//                        'field'     => 'type'
//                    ),
//                    array(
//                        'caption'   => Mage::helper('import')->__('Import Csv'),
//                        'url'       => array('base'=> '*/adminhtml_process/index'),
//                        'field'     => 'type'
//                    )
//                ),
                'filter'    => false,
                'sortable'  => false,
                //'index'     => 'stores',
                'is_system' => true,
        ));
		
      return parent::_prepareColumns();
  }
  
  public function getRowUrl($row)
  {
      return null;
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  protected function _typeFilter($collection, $column)
  {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
        $collection = $this->getCollection()->toArray();
        $resultantCollecton = $this->recursive_array_search($value, 'type' ,$collection['items']);
        
        $collection = new Varien_Data_Collection();
        foreach ($resultantCollecton as $item) {
            $data = new Varien_Object($item);
            $collection->addItem($data);
        }
        $this->setCollection($collection);
        
        return $this;
  }
  
  function recursive_array_search($needle , $column ,$haystack) {
        $result = array();
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if(is_array($value))
            {
                if(isset($value[$column]))
                {
                    if($this->recursive_array_search($needle, $column ,$value))
                    {
                        $result[$current_key] = $value;
                    }
                }
            }
            else
            {
                if(strpos($value , $needle))
                {
                    $result[$current_key] = $value;
                }
            }
        }
        return $result;
  }

}