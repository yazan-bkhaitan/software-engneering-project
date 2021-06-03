<?php

// Heading
$_['heading_title_list']          = 'Purchase Order Email List';
$_['heading_title_add']           = 'Add Email Template';
$_['heading_title_edit']          = 'Edit Email Template';
$_['heading_title_delete']        = 'Delete Email Template';


// Text
$_['text_list']                   = 'Email Template List';
$_['text_success_add']       	    = 'Success: Purchase Order Email template saved successfully!';
$_['text_success_update']        	= 'Success: Purchase Order Email template updated successfully';
$_['text_success_delete']        	= 'Success: Purchase Order Email template deleted successfully';
$_['text_edit']            	      = 'Edit Email Template';

// Entry
$_['entry_id']        		        = 'Email Tamplate Id';
$_['entry_name']      		        = 'Template Name';
$_['entry_subject']      	        = 'Template Subject';
$_['entry_message']               = 'Message';
$_['entry_name_info']      	      = 'Name which will display at time of selection email template (in module configuration).';
$_['entry_message_info']          = 'Add Mail Message';
$_['entry_subject_info']          = 'Add Mail Subject';
$_['entry_no_records']            = 'No Records Found !';
$_['entry_action']     		        = 'Action';


// Button
$_['button_save']			            = 'Save Template';
$_['button_back']			            = 'Back';
$_['button_cancel']			          = 'Cancel';
$_['button_insert']			          = 'Create New Template';
$_['button_delete']			          = 'Delete Template';
$_['button_filter']			          = 'Filter Template';


// Info
$_['info_mail']			              = ' You can check the allowed placeholders(keywords) for the purchase order email template under the <b>Keyword Info</b> tab.';

$_['info_mail_add']			          = ' You can used these below listed placeholders (keywords) in your email template message fields. You can use these created email template for the default conditions of Purchase Order module. You can add email template here and choose these created template under the module setting <b>(Menus -> Extensions -> Extensions -> Choose Modules -> Opencart Purchase Order Management(Edit) -> Under Email Setting Tab)</b>.

<br/> Purchase Order Management module send emails to Admin / Supplier in these conditions :-
<br/>
<br/> Quotation (Creation/Modification) Email to Admin.
<br/> Quotation (Creation/Modification) Email to Supplier.
<br/> Purchase Order Email to Admin.
<br/> Purchase Order Email to Supplier.

<br/><br/> So By the help of this Email Template section you can add Email Messages with Subject. Some Emails will work normally but some with Message + Quotation data combination. So to do this you can add these placeholders with your message.
<br/>
.';

$_['tab_info']      	 	         = 'Info';
$_['tab_general']         	     = 'Add Message';
$_['entry_for']         	       = 'For';
$_['entry_code']      		       = 'Keyword';


//user
$_['text_hello']    		         = 'Hello ';
$_['text_to_seller']    	       = 'Your request has been approved. Please login and manage your store .';
$_['text_auto']    	             = 'Your request has been approved. Please login and manage your store .';
$_['text_ur_pre']    	           = 'Admin commission will be ';
$_['text_sellersubject']         = 'Your Request has been approved. ';
$_['text_thanksadmin']    	     = 'Thank you, ';

//for product mail
$_['entry_pname']      	         = 'Product Name - ';
$_['ptext_auto']    	           = 'Your product has been approved. Please login and manage your store .';
$_['ptext_sellersubject']        = 'Your Product has been approved';


// Error
$_['error_permission']           = 'Warning: You do not have permission to modify email template!';
$_['error_name']                 = 'Warning - Please add Name for this email template message !!';
$_['error_subject']    		       = 'Warning - Please add Subject for email template, character must between 5 to 1000 !!';
$_['error_message']    		       = 'Warning - Please add Message for email template, character must between 25 to 5000 !!';
?>
