var directory = configuration.directory;
$(document).ready(function() {

    // On load, display root directory.
    if (directory && directory !== '') {
        exploreDirectory(directory,siren);
    }
    
    // Go into folder action.
    $('#directory-area').on('click', 'a.mime.folder', function(event) {
        event.stopPropagation();
        event.preventDefault();
        exploreDirectory($(this).attr('href'),siren);
    });
    $('#directory-area').on('click', '.breadcrumb-item a', function(event) {
        event.stopPropagation();
        event.preventDefault();
        exploreDirectory($(this).attr('href'),siren);
    });

    // Explore citizen data directory.
    $('#results-area .btn-secondary.explore').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        $('#results-area').hide();
        exploreDirectory(directory,siren);
    });

    // Show/hide collapse directory area.
    $('#directory').on('click', 'nav#breadcrumb', function() {
        if ($(this).attr('aria-expanded') === 'true') {
            $(this).find('i.fas').addClass('fa-chevron-up').removeClass('fa-chevron-down');
        } else if ($(this).attr('aria-expanded') === 'false') {
            $(this).find('i.fas').addClass('fa-chevron-down').removeClass('fa-chevron-up');
        }
    });

});

/** @description Explore and display directory content with breadcrumb.
 * @param {string} path Directory relative path
 * @param {string} siren siren
 */
function exploreDirectory(path,siren) {
    $('#directory-area').show();
    $('#loading-area').show();
    $('#message-area .alert').hide()
    $.ajax({
        type : 'post',
        url : 'action/explore-directory',
        dataType : 'html',
        data : {
            'directory': path,
            'siren': siren
        }
    }).done(function(data) {
        $('#directory').html(data);
    }).fail(function(obj, text, error) {
        displayError(obj, text, error);
    }).always(function() {
        $('#loading-area').hide();
    });
}
