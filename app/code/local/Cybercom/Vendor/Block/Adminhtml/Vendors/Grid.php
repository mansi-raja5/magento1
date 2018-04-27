<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('cybercom_vendor_vendordetail_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
     
    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'cybercom_vendor/vendordetail_collection';
    }
     
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('selectIds');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('checkout')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('checkout')->__('Are you sure?')
        ));

        $statuses = array(
            1=>'Enable Vendors',
            0=>'Disable Vendors'
        );
        //array_unshift($statuses, array('label'=>'', 'value'=>''));        

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> $this->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => $this->__('Status'),
                         'values' => $statuses
                     )
             )
        ));      

        return $this;
    }
         
    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
         
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(  
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );
         
        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'align' => 'left',
                'index' => 'status',
                'type' => 'options',
                'options' => array( 1 => 'Enable', 0 => 'Disable' )
            )
        );        
         
        $this->addExportType('*/*/exportCsv', Mage::helper('cybercom_vendor')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('cybercom_vendor')->__('XML')); 
        $this->addExportType('*/*/exportExcel',Mage::helper('cybercom_vendor')->__('EXCEL'));
        
        return parent::_prepareColumns();
    }
     
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}