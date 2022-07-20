jQuery(document).ready(function ($) {

    // Events
    // Sorting icons
    jQuery(document).on('click', '.fa-sort-asc', handle_ajax);
    jQuery(document).on('click', '.fa-sort-desc', handle_ajax);

    // Role filter
    jQuery(document).on('click', '.kwp-role-name', handle_ajax);

    // Page selected
    jQuery(document).on('click', '.kwp-page', handle_ajax);

    // Default state
    let currentState = {
        page: 1,
        sorting: 'ASC',
        sortby: 'user_login',
        filter: 'all'
    }

    function handle_ajax(event) {

        // Element
        let elementId = event.target.id;
        let elementClass = event.target.className;

        // Role clicked
        if (elementClass === 'kwp-role-name') {
            currentState.filter = jQuery(this).text();
        }

        // Page clicked
        else if (elementClass === 'kwp-page') {
            currentState.page = jQuery(this).text();
        }

        // Else - sorting clicked
        else {
            let elementIdArr = elementId.split('-');
            currentState.sortby = elementIdArr[0];
            currentState.sorting = elementIdArr[1];
        }

        // Pass to data
        let data = {
            action: 'reload_table',
            kwp_state: currentState,
        };


        // Get tbody element
        let tbody = jQuery(".kwp-userlist-table tbody");

        jQuery.post(
            ajaxdata.url,
            data,
            function( response ) {

                let tbody_html = jQuery.parseJSON( response ).table_html;

                tbody.hide()
                     .html('')
                     .html( tbody_html )
                     .fadeIn(100);
            }
        );

        // After the data is loaded, change the state of sorting icons
        jQuery('.active-sort').toggleClass('active-sort');
        jQuery(this).addClass('active-sort');
    }



});