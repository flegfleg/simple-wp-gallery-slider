jQuery( document ).ready( function( $ ) {

  // available options: http://bxslider.com/options
  
  // console.log (options);

  // create slider for each gallery
  $('.swpgs-slider').each( function(  ) {

    var slider_name = $( this ).attr('id');
    var slider_options_name = slider_name + '_args';
    var options = window[ slider_options_name ][0];
    console.log (options);
    $(this).bxSlider( options);

    });
} );