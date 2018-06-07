<?php
class Ccc_Outlook_Block_Adminhtml_Outlook_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('entity_id');
        $this->setId('ccc_outlook_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setGridHeader('<script type="text/javascript">'
            .'function openMailPreview(r)'
            .'{'
            .'    var url = "";'
            .'    var dialogWindow = Dialog.info(null, {'
            .'        closable:true,'
            .'        resizable:false,'
            .'        draggable:true,'
            .'        className:"magento",'
            .'        windowClassName:"popup-window",'
            .'        title:"Mail Preview",'
            .'        top:50,'
            .'        width:600,'
            .'        height:500,'
            .'        zIndex:1000,'
            .'        recenterAuto:false,'
            .'        hideEffect:Element.hide,'
            .'        showEffect:Element.show,'
            .'        id:"browser_window",'
            .'        url:url,'
            .'        onClose:function (param, el) {'
            .'              orderMailId = jQuery(r).siblings(".cls-ordermail-id").val();'
            .'              var readMail  = new  Furnique.Method();'
            .'              var postData  = {"form_key" : "'.Mage::getSingleton('core/session')->getFormKey().'","orderMailId":orderMailId};'
            .'              var isReadUrl = "'.Mage::helper('adminhtml')->getUrl('adminhtml/index/isread/').'?ajax=true";'
            .'              readMail.setUseType("url").setRequestType("post").setData(postData).setURL(isReadUrl).loadPage();'
            .'        }'
            .'    });'
            .'    content = jQuery(r).siblings(".cls-mailbody").html();'
            .'    dialogWindow.getContent().update(atob(content));'
            .'}'
            .'</script>');
    } 
         
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('outlook/ordermail_collection');

        $collection->getSelect()
                   ->columns('GROUP_CONCAT(DISTINCT attachment) as attachments,cm.mfg as mfr_name')
                   ->joinLeft(array('coma' => 'ccc_order_mail_attachment'), 'coma.entity_id = main_table.entity_id' , array())
                   ->joinLeft(array('cm' => 'ccc_manufacturer'), 'cm.entity_id = main_table.mfr_id' , array())
                   ->group('main_table.entity_id');

        // echo $collection->getSelect();exit;

        $this->setCollection($collection);         
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {

        $this->addColumn('entity_id',
            array(  
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'entity_id'
            )
        );


        $this->addColumn('order_id',
            array(
                'header'=> $this->__('Order ID'),
                'align' => 'left',
                'index' => 'order_id'
            )
        );        
         
        $this->addColumn('received_date',
            array(
                'header'=> $this->__('Date'),
                'index' => 'received_date'
            )
        );

        $this->addColumn('from_email',
            array(
                'header'=> $this->__('From Email'),
                'align' => 'left',
                'index' => 'from_email'
            )
        );    

        $this->addColumn('recipients',
            array(
                'header'    => $this->__('Recipients'),
                'align'     => 'left',
                'index'     => 'recipients'
            )
        ); 

        $this->addColumn('subject',
            array(
                'header'=> $this->__('Subject'),
                'align' => 'left',
                'index' => 'subject'
            )
        );    

        $this->addColumn('mfr_name',
            array(
                'header'=> $this->__('MFR'),
                'align' => 'left',
                'index' => 'mfr_name'
            )
        );           

        $this->addColumn('order_status',
            array(
                'header'=> $this->__('Order Status'),
                'align' => 'left',
                'index' => 'order_status'
            )
        );         

        $this->addColumn('read',
            array(
                'header'=> $this->__('isRead'),
                'align' => 'left',
                'index' => 'read',
                'type' => 'options',
                'options' => array( 1 => 'Yes', 2 => 'No' ),                
                'renderer'  => 'outlook/adminhtml_outlook_grid_renderer_isread',
            )
        );  


        $this->addColumn('view_email',
            array(
                'header'=> $this->__('Action'),
                'align' => 'left',
                'index' => 'view_email',
                'renderer'  => 'outlook/adminhtml_outlook_grid_renderer_viewemail',
            )
        ); 

        $this->addColumn('attachments',
            array(
                'header'=> $this->__('Attachment'),
                'align' => 'left',
                'index' => 'attachments',
                'renderer'  => 'outlook/adminhtml_outlook_grid_renderer_attachments',
            )
        );          

        // $this->addColumn('noack',
        //     array(
        //         'header'=> $this->__('No ACK'),
        //         'align' => 'left',
        //         'index' => 'noack',
        //         'renderer'  => 'outlook/adminhtml_outlook_grid_renderer_noack',
        //     )
        // );            
        return parent::_prepareColumns();
    }
     
    public function getRowUrl($row)
    {
        return false;
    }

}