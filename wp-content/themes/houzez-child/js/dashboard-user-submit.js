var conditions = [
    {
        id: 'commercial',
        value: ['business-type']
    },
    {
        id: 'condo-units',
        value: []
    },
    {
        id: 'houses-villas',
        value: ['residence-type']
    },
    {
        id: 'land',
        value: ['land-allocation']
    },
    {
        id: 'buy',
        value: ['legal-ownership']
    }
];

jQuery(document).ready(function(){
    var default_type = jQuery('.dashboard_property_type .property_type input[type="radio"]:checked').data('slug');
    houzezz_child_display_conditional_type_labels(default_type);
    jQuery('.dashboard_property_type .property_type').on('change', 'input[type="radio"], input[type="checkbox"]', function(){
        var val = jQuery(this).data('slug');
        var cat_name = jQuery(this).parents('.dashboard_property_cat');
        houzezz_child_display_conditional_type_labels(val,cat_name);
    });
    jQuery('.dashboard_property_status .property_status').on('change', 'input[type="radio"], input[type="checkbox"]', function(){
        var val = jQuery(this).data('slug');
        var cat_name = jQuery(this).parents('.dashboard_property_cat');
        houzezz_child_display_conditional_type_labels(val,cat_name);
    });
});

function houzezz_child_display_conditional_type_labels(term_slug,cat_div){
    var conditional_term_parent = ['commercial', 'condo-units', 'houses-villas', 'land', 'buy'];
    if( jQuery.inArray( term_slug, conditional_term_parent ) > -1 ){
        conditions.filter(function(condition){
            if(condition.id === term_slug){
                if( condition.value.length > 0 ){
                    for( var i = 0; i < condition.value.length; i++ ){
                        var condition_name = condition.value[i];
                        jQuery(cat_div).next('.dashboard_property_label').find('label[for="prop_label"]').siblings('.property_label').addClass('d-none');
                        jQuery(cat_div).next('.dashboard_property_label').find('label[for="prop_label"]').siblings('.property_label.'+condition_name).children('label').addClass('d-none');
                        var label_title = jQuery(cat_div).next('.dashboard_property_label').find('label[for="prop_label"]').siblings('.property_label.'+condition_name).children('label').text();
                        jQuery(cat_div).next('.dashboard_property_label').find('label[for="prop_label"]').text(label_title);
                        jQuery(cat_div).next('.dashboard_property_label').find('label[for="prop_label"]').siblings('.property_label.'+condition_name).removeClass('d-none');
                    }
                    jQuery(cat_div).next('.dashboard_property_label').removeClass('d-none');
                }else{
                    jQuery(cat_div).next('.dashboard_property_label').addClass('d-none');
                }
            }
        });
    }else{
        jQuery(cat_div).next('.dashboard_property_label').addClass('d-none');
    }
}