/*
 *
 * FLYTHEME_Options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */
function flytheme_radio_img_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');

	jQuery('.flytheme-radio-img-'+labelclass).removeClass('flytheme-radio-img-selected');	
	
	jQuery('label[for="'+relid+'"]').addClass('flytheme-radio-img-selected');
}//function