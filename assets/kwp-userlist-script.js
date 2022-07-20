jQuery(document).ready(function ($) {


    jQuery(document).on('click', '#addmore', handle_ajax);

    function handle_ajax(event) {
        
        console.log('clicked on: ');
        console.log(event.target.id); 
        console.log(event.target.className); 

        let data = {
            action: 'reload_table',
            some: 'data'
        };

        let tbody = jQuery(".kwp-userlist-table tbody");

        jQuery.post(
            ajaxdata.url,
            data,
            function(response) {

                let tbody_html = jQuery.parseJSON( response ).table_html;

                tbody.hide()
                     .html('')
                     .html(tbody_html)
                     .fadeIn(100);

            }
        );
    }


});