<?php
class Cybercom_Banner_Block_Adminhtml_Banners_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('cybercom_banner_banneerdetail_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
     
    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'cybercom_banner/bannerdetail_collection';
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
            1=>'Enable Banners',
            0=>'Disable Banners'
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
        $this->addColumn('banner_id',
            array(  
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'banner_id'
            )
        );
         
        $this->addColumn('image',
            array(
                'header'    =>  $this->__('Image'),
                'index'     =>  'image',
                'align'     =>  'center',
                'width'     =>  '100px',
                'renderer'  =>  'cybercom_banner/adminhtml_banners_edit_bannerImgRender',
            )
        );  
        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );
        $this->addColumn('sort_order',
            array(
                'header'=> $this->__('Sort Order'),
                'index' => 'sort_order',
                'width' => '50px',
            )
        ); 
        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'align' => 'left',
                'index' => 'status',
                'type' => 'options',
                'options' => array( 1 => 'Active', 0 => 'Inactive' )
            )
        );        
         
        $this->addExportType('*/*/exportCsv', Mage::helper('cybercom_banner')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('cybercom_banner')->__('XML')); 
        $this->addExportType('*/*/exportExcel',Mage::helper('cybercom_banner')->__('EXCEL'));
        
        return parent::_prepareColumns();
    }
     
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('banner_id' => $row->getId()));
    }

}