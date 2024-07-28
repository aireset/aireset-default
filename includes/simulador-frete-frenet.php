<?php


function simulador_produto_frenet(){
	?>
	<div id="productPageSimulator"><?=WC_Frenet_Shipping_Simulator::simulator()?></div>
	<style>
		body #productPageSimulator {}
		body #productPageSimulator #shipping-simulator {
			width: 100%;
			display: inline-block;
		}
		body #productPageSimulator #shipping-simulator form {}
		body #productPageSimulator #shipping-simulator form label {
			width: 100%;
			display: inline-block;
			margin: 0;
			padding: 15px 0;
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 18px;
			font-weight: 600;
			text-transform: capitalize;
			line-height: 18px;
			letter-spacing: 0.3px;
		}
		body #productPageSimulator #shipping-simulator form input#zipcode {
			min-width: 200px;
			display: inline-block;
			margin: 0;
			padding: 15px 15px;
			border-radius: 15px 0 0 15px;
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 16px;
			letter-spacing: 0.3px;
			height: 52px;
			border-color: var( --e-global-color-secondary );
		}
		body #productPageSimulator #shipping-simulator form input#zipcode::-webkit-input-placeholder
		{
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 16px;
			letter-spacing: 0.3px;
		}
		body #productPageSimulator #shipping-simulator form input#zipcode:-ms-input-placeholder
		{
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 16px;
			letter-spacing: 0.3px;
		}
		body #productPageSimulator #shipping-simulator form input#zipcode::placeholder 
		{
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 16px;
			letter-spacing: 0.3px;
		}
		body #productPageSimulator #shipping-simulator form button#idx-calc_shipping {
			font-size: 16px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 16px !important;
			border-radius: 8px 8px 8px 8px;  
			color: #FFFFFF;
			background-color: var( --e-global-color-primary );
			padding: 16px 25px 16px 25px !important;
			margin: 0px 15px 0px 0px !important;
			border-radius: 0 15px 15px 0;
			height: 52px;
		}
		body #productPageSimulator #shipping-simulator form #simulator-data {
		}
		body #productPageSimulator #shipping-simulator form #simulator-data  #shipping-rates {
			border: 1px solid var( --e-global-color-secondary );
			border-radius: 15px;
			padding: 15px 15px;
		}
		body #productPageSimulator #shipping-simulator form #simulator-data li {
			width: 100%;
			display: inline-block;
			margin: 0;
			padding: 15px 0;
			color: var( --e-global-color-secondary );
			font-family: "Bechtlers", Montserrat,sans-serif;
			font-size: 18px;
			font-weight: 600;
			text-transform: capitalize;
			line-height: 18px;
			letter-spacing: 0.3px;
		}
	</style>

	<script>
		jQuery(function($){
			var updateVar = setTimeout(function() {}, 1);
			$(document).on('click', '.shopengine-qty-btn .plus', function () {
				$('#productPageSimulator .qty_simulator').attr('value', $('.quantity .qty').val());
				if(!$('#updateVar #simulator-data').is(':empty')){
					clearTimeout(updateVar);
					updateVar = setTimeout(function() {
						$('#productPageSimulator #simulator-data').html();
						$('#productPageSimulator #idx-calc_shipping').trigger('click');
					}, 1000);
				}
			});
			$(document).on('click', '.shopengine-qty-btn .minus', function () {
				$('#productPageSimulator .qty_simulator').attr('value', $('.quantity .qty').val());
				if(!$('#productPageSimulator #simulator-data').is(':empty')){
					clearTimeout(updateVar);
					updateVar = setTimeout(function() {
						$('#productPageSimulator #simulator-data').html();
						$('#productPageSimulator #idx-calc_shipping').trigger('click');
					}, 1000);
				}
			});
			
			$("#productPageSimulator label").after($("#zipcode"))
			
			$("#productPageSimulator label").after($("#productPageSimulator #zipcode"))
		})
	</script>
	<?php
}

add_shortcode( 'simulador_produto_frenet', 'simulador_produto_frenet' );

function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
    $wp_admin_bar->remove_menu('view-store');        // Remove the view site link
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

