jQuery(document).ready( function($){


    $('#more-data').on('click','.ld-more',function(e){
        e.preventDefault();
        var a = $(this).val() ;
           // var r = ++a;
        $.ajax({
          url: ajax_object.ajaxurl, // object instantiated in wp_localize_script function
          type: 'POST',
          data:{ 
            action: 'resolver', // function triggered
            source:  a
          },
          success: function( data ){
              //alert(data);
            //Do something with the result from server
            var myobj = document.getElementById("btnremove");
            myobj.remove();
            console.log( data );
            $("#more-data").append(data);
          }
        });
    });




});