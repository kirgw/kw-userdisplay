jQuery(document).ready(function ($) {

    // Events
    // Sorting icons
    jQuery(document).on('click', '.fa-sort-asc', handle_ajax);
    jQuery(document).on('click', '.fa-sort-desc', handle_ajax);

    // Role filter
    jQuery(document).on('click', '.kw-role-name', handle_ajax);

    // Remove role filter
    jQuery(document).on('click', '#kw-role-remove', handle_ajax);

    // Page selected
    jQuery(document).on('click', '.kw-page', handle_ajax);

    // Default state
    let currentState = {
        page: 1,
        sorting: 'ASC',
        sortby: 'user_login',
        filter: 'all'
    }

    // Default icons
    jQuery('.kw-filter-icons').hide();

    // Default sorting
    jQuery('#username-ASC').addClass('active-sort');

    function handle_ajax(event) {

        // Rules:
        // sort by/type changed => page=1, filter stays
        // role filter changed => page=1, sort=def
        // page changed => all remain

        // Element
        let elementId = event.target.id;
        let elementClass = event.target.className;
 
        // Role clicked
        if (elementClass === 'kw-role-name' || elementId === 'kw-role-remove') {

            // Do nothing if filter is already activated
            if (elementClass === 'kw-role-name' && currentState.filter != 'all') {
                return;
            }

            // Either set or remove the role filter
            currentState.filter = (elementClass === 'kw-role-name') ? jQuery(this).text() : 'all';

            // Need to reset all other params in this case
            currentState.page = 1;
            currentState.sorting = 'ASC';
            currentState.sortby = 'user_login';

            // Show/hide sorting icons
            jQuery('.kw-filter-icons').toggle();

            // Remove all other sorting
            jQuery('.active-sort').removeClass('active-sort');

            // Add active sorting
            jQuery('#kw-filter-icon').addClass('active-sort');
        }

        // Page clicked
        else if (elementClass === 'kw-page') {
            currentState.page = jQuery(this).text();
        }

        // Else - sorting clicked
        else {
            let elementIdArr = elementId.split('-');
            currentState.sortby = elementIdArr[0];
            currentState.sorting = elementIdArr[1];

            // Need to reset the page in this case
            currentState.page = 1;

            // Remove all other sorting
            jQuery('.kw-sort').removeClass('active-sort');
            
            // Change the current sorting
            jQuery(this).addClass('active-sort');
        }

        // Pass to data
        let data = {
            action: 'reload_table',
            kw_state: currentState,
        };

        // Get tbody element
        let tbody = jQuery(".kw-userdisplay-table tbody");

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

    }



});