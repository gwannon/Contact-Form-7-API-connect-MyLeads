<?php
/**
 * Plugin Name: Contact Form 7 + MyLEads
 * Plugin URI:  https://www.enutt.net/
 * Description: Conexión entre Contact Form 7 y la API de MyLeads 
 * Version:     1.0
 * Author:      Enutt S.L.
 * Author URI:  https://www.enutt.net/
 * License:     GNU General Public License v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cf7-myleads
 *
 * PHP 7.3
 * WordPress 5.5.3
 */

//ini_set("display_errors", 1);

define('MY_LEADS_API_URL', get_option("_my_leads_api_url")); 
define('MY_LEADS_API_USER', get_option("_my_leads_api_user"));
define('MY_LEADS_API_PASS', get_option("_my_leads_api_password"));
define('MY_LEADS_OPORTUNITY_ID', get_option("_my_leads_oportunity_id"));
define('MY_LEADS_FACILITY_BUNO', get_option("_my_leads_facility_buno"));
define('MY_LEADS_CFORMS', json_decode(get_option("_my_leads_cforms")));
define('MY_LEADS_EMAIL', get_option("_my_leads_email"));
define('MY_LEADS_BRAND', get_option("_my_leads_brand"));
define('MY_LEADS_COMMENTS', get_option("_my_leads_comments"));

//Cargamos librerías de conexión a la API
require_once(dirname(__FILE__)."/api.php");

//Cargamos las funciones que crean las páginas en el WP-ADMIN
require_once(dirname(__FILE__)."/admin.php");

//CReamos un directorio al activar el plugin
define( 'MY_LEADS_PLUGIN_FILE', __FILE__ );
register_activation_hook(MY_LEADS_PLUGIN_FILE, 'cf7_myleads_plugin_activation' );
function cf7_myleads_plugin_activation() {
  if ( ! current_user_can( 'activate_plugins' ) ) return;
  if(!is_dir(dirname(__FILE__)."/logs/")){
    mkdir(dirname(__FILE__)."/logs/", 0755);
  }
}

//HOOK de CF/
add_action("wpcf7_before_send_mail", "wpcf7_do_my_leads");

function wpcf7_do_my_leads(&$wpcf7_data) {
  //$wpcf7 = WPCF7_ContactForm::get_current();
  if(!in_array($wpcf7_data->id, MY_LEADS_CFORMS)) return; //Chequeamos que sea uno de los formualrios aceptados

  $submission = WPCF7_Submission::get_instance();
  $formdata = $submission->get_posted_data();
  
  //Formato del JSON -------------------------------------------------------------------------
  $json = '{
    "OpportunityId": "'.MY_LEADS_OPORTUNITY_ID.'",
    "FacilityBuno": "'.MY_LEADS_FACILITY_BUNO.'",
    "Customer": {
      "LastName": "'.transformString ($formdata['myleads-lastname-1']).'",
      '.($formdata['myleads-lastname-2'] != '' ? '"SecondLastName": "'.transformString ($formdata['myleads-lastname-2']).'",' : '').'
      "FirstName": "'.transformString ($formdata['myleads-firstname']).'",
      "MobilePhone": "'.$formdata['myleads-mobile-phone'].'",
      '.($formdata['myleads-business-mobile-phone'] != '' ? '"BusinessMobilePhone": "'.$formdata['myleads-business-mobile-phone'].'",' : '').'
      "Email": "'.$formdata['myleads-business-email'].'",
      "Salutation": "'.$formdata['myleads-salutation'][0].'",
      "Sex": "'.($formdata['myleads-salutation'][0] == 'MR' ? "M" : "F").'",
      "PreferredContactChannel": "'.$formdata['myleads-contact-form'][0].'" 
    },
    "Consents": {
        "ConMKDealer": "'.($formdata['myleads-conmkdealer'] == '1' ? "Y" : "N").'",
        "ConAnaDealer": "'.($formdata['myleads-conanadealer'] == '1' ? "Y" : "N").'",
        "ConMKNSC": "'.($formdata['myleads-conmknsc'] == '1' ? "Y" : "N").'",
        "ConAnaNSC": "'.($formdata['myleads-conanansc'] == '1' ? "Y" : "N").'",
        "ConsentDate": "'.date("Y-m-d H:i:s").'"
    },
    "Activity": {
      "ActivityStartDate": "'.date("Y-m-d H:i:s").'",
      "ActivityDueDate": "'.date("Y-m-d H:i:s").'",
      "ActivityStatusDate": "'.date("Y-m-d H:i:s").'",
      "Comments": "' . MY_LEADS_COMMENTS . '",
      "Product": {
        '.($formdata['myleads-product-series'] != '' ? '"Series": "'.$formdata['myleads-product-series'].'",' : '').'
        '.($formdata['myleads-product-model'] != '' ? '"Model": "'.$formdata['myleads-product-model'].'",' : '').'
        "Brand": "'.($formdata['myleads-product-brand'] != '' ? transformString ($formdata['myleads-product-brand']) : MY_LEADS_BRAND).'"
      }
    }
  }';
  
  //echo $json."\n----------------------------\n";
  //print_r($formdata);
  $response = insertLead($json);
  //print_r($response);
}



?>
