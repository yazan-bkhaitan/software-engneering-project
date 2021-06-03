<?php
// Heading
$_['heading_title']                     = 'Opencart Purchase Order Management';
$_['heading_title_shipping_method']     = 'Manage Shipping Method';
$_['heading_title_payment_method']      = 'Manage Payment Method';
$_['heading_title_payment_method_supplier'] = 'Manage Supplier\'s Payment Method';


// Text
$_['text_extension']                    = 'Extensions';
$_['text_success']                      = 'Success: You have modified purchase order management module!';
$_['text_edit']                         = 'Edit Purchase Order Management Module';
$_['text_po_management']                = 'Purchase Management';
$_['text_po_dashboard']                 = 'Dashboard';
$_['text_po_supplier']                  = 'Supplier';
$_['text_po_quotation']                 = 'Quotation';
$_['text_purchase_order']               = 'Purchase Order';
$_['text_po_email']                     = 'Mail Template List';
$_['text_po_supplier_ledger']           = "Supplier Ledger";
$_['text_purchase_config']              = 'Purchase Configuration';

// Entry
$_['entry_status']                      = 'Module Status';
$_['entry_oc_store']                    = 'Opencart Stores';
$_['entry_select']                      = '--Select Email Notification--';
$_['entry_order_prefix']                = 'Default Order Prefix';
$_['entry_precurement_method']          = 'Procurement Methods';
$_['entry_make_to_stock']               = 'Make To Stock';
$_['entry_make_to_order']               = 'Make To Order';
$_['entry_default_quotation_quantity']  = 'Default Quotation Quantity';
$_['entry_quotation_email_admin']       = 'Quotation Email To Admin/Store';
$_['entry_quotation_email_supplier']    = 'Quotation Email To Supplier';
$_['entry_addproduct_email_admin']      = 'Add Product To Quotation Email To Admin/Store';
$_['entry_addproduct_email_supplier']   = 'Add Product To Quotation Email To Supplier';
$_['entry_updateproduct_email_admin']   = 'Update Details To Quotation Email To Admin/Store';
$_['entry_updateproduct_email_supplier']= 'Update Details To Quotation Email To Supplier';
$_['entry_purchase_email_admin']        = 'Purchase Order Email To Admin/Store';
$_['entry_purchase_email_supplier']     = 'Purchase Order Email To Supplier';
$_['entry_shipping_method_name']        = 'Shipping Method Name';
$_['entry_payment_method_name']         = 'Payment Method Name';
$_['entry_method_status']               = 'Status';
$_['entry_action']                      = 'Action';
$_['entry_shipping_cost']               = 'Default Shipping Cost';
$_['entry_tax_cost']                    = 'Default Tax Cost';
$_['entry_order_to_po']                 = 'Generate PO on Order Placed';
$_['entry_order_status_po']             = 'Order Status for Purchase Order';
$_['entry_po_email_id']                 = 'Set the Email Id for PO';
$_['entry_po_low_stock']                = 'Low stock quantity for automatic PO Generation';

//tab
$_['tab_general']                       = 'General Settings';
$_['tab_email']                         = 'Email Settings';
$_['tab_shipping']                      = 'Shipping Settings';
$_['tab_payment']                       = 'Payment Settings';
$_['tab_payment_seller']                = 'Supplier\'s Payment Method';

//button
$_['button_add_shipping']               = 'Add Shipping Method';
$_['button_remove_shipping']            = 'Remove Shipping Method';
$_['button_add_payment']                = 'Add Payment Method';
$_['button_remove_payment']             = 'Remove Payment Method';

//placeholder
$_['placeholder_order_prefix']          = 'Enter Order Prefix For Purchase Order Management....';
$_['placeholder_default_quantity']      = 'Enter default Quotation Order quantity....';
$_['placeholder_method_name']           = 'Enter method name....';
$_['placeholder_shipping_cost']         = 'Enter default shipping cost....';
$_['placeholder_tax_cost']              = 'Enter default tax cost....';


//help
$_['help_order_prefix']                 = 'Info: This prefix will be used for order those will created under Purchase Order Management Module.';
$_['help_precurement_method']           = 'Info: If you choose <b>Make To Order option</b> and product quantity become to zero after placing order, then a quotation order will create automatically otherwise you have to create quotation manually.';
$_['help_default_quantity']             = 'Info: If you didn\'t provide the quotation order quantity, then this quantity will use for quotation order.';
$_['help_quotation_email_admin']        = 'Info: An email notification will send to store after placing quotation order.';
$_['help_quotation_email_supplier']     = 'Info: An email notification will send to the supplier after placing quotation order.';
$_['help_addproduct_email_admin']       = 'Info: An email notification will send to store after adding any new product to quotation.';
$_['help_addproduct_email_supplier']    = 'Info: An email notification will send to supplier after adding any new product to quotation.';
$_['help_update_email_admin']           = 'Info: An email notification will send to store after updating quotation\'s information.';
$_['help_update_email_supplier']        = 'Info: An email notification will send to supplier after updating quotation\'s information.';
$_['help_purchase_email_admin']         = 'Info: An email notification will send to store after placing purchase order.';
$_['help_purchase_email_supplier']      = 'Info: An email notification will send to supplier after placing purchase order.';
$_['help_shipping_cost']                = 'Info: This will be the default shipping cost for purchase order.';
$_['help_tax_cost']                     = 'Info: Provide default tax cost in percentage i.e. will use for purchase order.';
$_['help_order_to_po']                  = 'Info: If enabled, a purchase order will generated automatically on order place.';
$_['help_order_status_po']              = 'Info: Choose the Order Status for which the Purchase Order will generate from front end.';
$_['help_po_email_id']                  = 'Info: Provide the email address for the Purchase order notification.';
$_['help_po_low_stock']                 = 'Info: Provide the number of the quantity on which purchase order will be created automatically.';


// Error
$_['error_permission']                  = 'Warning: You do not have permission to modify purchase order management module!';
$_['error_warning']                     = 'Warning: Check all form fields carefully!';
$_['error_oc_pom_order_prefix']         = 'Warning: Order Prefix must be between 2 and 5 characters!';
$_['error_oc_pom_order_prefix_invalid'] = 'Warning: Please provide only alphanumeric value for Order Prefix!';
$_['error_shipping_name']               = 'Warning: Shipping Method Name must be between 3 and 70 characters!';
$_['error_payment_name']                = 'Warning: Payment Method Name must be between 3 and 70 characters!';
$_['error_invalid_value']               = 'Warning: You entered invalid value, please enter only alphanumeric characters!';
$_['error_supplier_qty']                = 'Warning: Quotation default quantity should be greater than 0!';
$_['error_shipping_cost']               = 'Warning: You entered invalid value, Only accept numeric value!';
$_['error_tax_cost']                    = 'Warning: You entered invalid value, Only accept numeric value!';
$_['error_oc_pom_email_invalid']        = 'Warning: Invalid email address, Please provide some valid email Id!';
$_['error_po_low_stock']                = "Warning: Please provide positive integer value for the low stock quantity";
$_['error_installation']                = 'The module is not installed correctly, please reinstall the module.';
