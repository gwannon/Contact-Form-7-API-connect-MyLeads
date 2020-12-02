<?php

//ADMIN -----------------------------------------

add_action( 'admin_menu', 'my_leads_plugin_menu' );
function my_leads_plugin_menu() {
	add_options_page( __('Administración MyLeads', 'cf7-myleads'), __('MyLeads', 'cf7-myleads'), 'manage_options', 'my_leads', 'my_leads_page_settings');
}

function my_leads_page_settings() { 
	//echo "<pre>"; print_r($_REQUEST); echo "</pre>";
	if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 
		update_option('_my_leads_api_url', $_POST['_my_leads_api_url']);
		update_option('_my_leads_api_user', $_POST['_my_leads_api_user']);
		if ($_POST['_my_leads_api_password'] != '') update_option('_my_leads_api_password', $_POST['_my_leads_api_password']);
		update_option('_my_leads_oportunity_id', $_POST['_my_leads_oportunity_id']);
		update_option('_my_leads_facility_buno', $_POST['_my_leads_facility_buno']);
		update_option('_my_leads_cforms', json_encode($_POST['_my_leads_cforms']));
		update_option('_my_leads_email', $_POST['_my_leads_email']);
		update_option('_my_leads_brand', $_POST['_my_leads_brand']);
		?><p style="border: 1px solid green; color: green; text-align: center;"><?php _e("Datos guardados correctamente.", 'cf7-myleads'); ?></p><?php
	} ?>
	<form method="post">
		<h1><?php _e("Configuración de la conexión con MyLeads", 'cf7-myleads'); ?></h1>
		<h2><?php _e("URL final de la API", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_api_url" value="<?php echo get_option("_my_leads_api_url"); ?>" style="width: 100%" /><br/><br/>
		<h2><?php _e("Usuario de la API", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_api_user" value="<?php echo get_option("_my_leads_api_user"); ?>" /><br/><br/>
		<h2><?php _e("Contraseña de la API", 'cf7-myleads'); ?>:</h2>
		<input type="password" name="_my_leads_api_password" value="" /><br/><br/>
		<h2><?php _e("Facility Buno de la API", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_facility_buno" value="<?php echo get_option("_my_leads_facility_buno"); ?>" /><br/><br/>
		<h2><?php _e("ID de la campaña actual", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_oportunity_id" value="<?php echo get_option("_my_leads_oportunity_id"); ?>" /><br/><br/>
		<h2><?php _e("Marca de la campaña (BMW|MINI)", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_brand" value="<?php echo get_option("_my_leads_brand"); ?>" /><br/><br/>
		<h2><?php _e("Email de aviso en caso de error", 'cf7-myleads'); ?>:</h2>
		<input type="text" name="_my_leads_email" value="<?php echo get_option("_my_leads_email"); ?>" /><br/><br/>
		<hr/>
		<h1><?php _e("Configuración de Contact Form", 'cf7-myleads'); ?></h1>
		<?php
			$args = array('post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1);
			if( $data = get_posts($args)){ $forms = json_decode(get_option("_my_leads_cforms")); ?>
				<ul>
					<?php foreach($data as $item){ ?>
							<li><input type="checkbox" name="_my_leads_cforms[]" value="<?php echo $item->ID; ?>"<?php if(count($forms) > 0 && in_array($item->ID, $forms)) echo " checked='checked'"; ?> /><?php echo $item->post_title; ?></li>
					<?php } ?>
				</ul>
			<?php } else {?>
							<li><?php _e('Debes crear al menos un formulario de contacto.'); ?></li>
			<?php }	?>
		<input type="submit" name="send" class="button button-primary" value="<?php _e("Guardar"); ?>" />
	</form>
	<h2><?php _e('Campos obligatorios que deben tener esos formularios.', 'cf7-myleads'); ?></h2>
	<ul>
		<li>[select* myleads-salutation "Señor|MR" "Señora|MRS"]</li>
		<li>[text* myleads-firstname]</li>
		<li>[text* myleads-lastname-1]</li>
		<li>[phone* myleads-mobile-phone<]</li>
		<li>[email* myleads-email]</li>
	</ul>
	<h2><?php _e('Campos opcionales que deben tener esos formularios.', 'cf7-myleads'); ?></h2>
	<ul>
		<li>[text myleads-lastname-2]</li>
		<li>[text* myleads-product-brand] [hidden myleads-product-brand "MARCA"] *</li>
		<li>[text myleads-product-model] [hidden myleads-product-model "modelo de coche"]</li>
		<li>[text myleads-product-series] [hidden myleads-product-series "serie de coche"]</li>
	</ul>
	<p><small><?php _e('* También se puede meter en la zona de administración de MyLeads.', 'cf7-myleads'); ?></small></p> 
	<?php
}

?>
