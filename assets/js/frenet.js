
jQuery(function($){
    var updateVar = setTimeout(function() {}, 1);
    $(document).on('click', '.shopengine-qty-btn .plus', function () {
        $('#productPageSimulator .qty_simulator').attr('value', $('.quantity .qty').val());
        if(!$('#productPageSimulator #simulator-data').is(':empty')){
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
    
    $("#productPageSimulator label").after($("#zipcode"));
    $("#productPageSimulator label").after($("#productPageSimulator #zipcode"));
});