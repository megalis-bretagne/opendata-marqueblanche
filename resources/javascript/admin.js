var limit = 20,
    siren = configuration.siren,
    filesToUpload = 0,
    filesInError = 0;

$(document).ready(function() {

    // Files uploader configuration.
    $('#drag-and-drop-zone').dmUploader({
        url: 'action/upload',
        extraData: function() {
            return {
                'siren' : siren,
                'category' : $('select#category').val(),
                'description' : $('textarea#description').val()
            };
        },
        maxFileSize: 10000000, // Max 10 Mo
        auto: false,
        queue: true,
        onDragEnter: function(){
            // Happens when dragging something over the DnD area
            this.addClass('active');
        },
        onDragLeave: function(){
            // Happens when dragging something OUT of the DnD area
            this.removeClass('active');
        },
        onNewFile: function(id, file){
            ui_multi_add_file(id, file);
        },
        onBeforeUpload: function(id){
            // about tho start uploading a file
            ui_multi_update_file_status(id, 'uploading', 'Publication...');
            ui_multi_update_file_progress(id, 0, '', true);
        },
        onUploadCanceled: function(id) {
            // Happens when a file is directly canceled by the user.
            ui_multi_update_file_status(id, 'warning', 'Annul&eacute; par l\'utilisateur');
            ui_multi_update_file_progress(id, 0, 'warning', false);
        },
        onUploadProgress: function(id, percent){
            // Updating file progress
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function(id, data){
            // A file was successfully uploaded
            var message = '';
            if (data.indexOf('{') > 0) {
                message = data.substring(0, data.indexOf('{'));
            } else {
                message = data;
            }
            ui_multi_update_file_status(id, 'success', message);
            ui_multi_update_file_progress(id, 100, 'success', false);
        },
        onComplete: function(){
            search(1, 0);
            if (filesInError == 0) {
                displayMessage('success', 'Publication vers les donn&eacute;es citoyennes termin&eacute;e');
                sleep(3000).then(function(){ resetPublicationForm(); });
            } else if (filesInError < filesToUpload) {
                var message = "Publication vers les donn&eacute;es citoyennes termin&eacute;e.<br />";
                if (filesInError > 1) {
                    message = message + filesInError + " documents n'ont pas &eacute;t&eacute; publi&eacute;s suite &agrave; une erreur. Veuillez recommencer pour ces documents, si le prob&egrave;me persiste, contactez l'administrateur de cette plateforme.";
                } else {
                    message +=  "Un document n'a pas &eacute;t&eacute; publi&eacute; suite &agrave; une erreur. Veuillez recommencer pour ce document, si le prob&egrave;me persiste, contactez l'administrateur de cette plateforme.";
                }
            } else if (filesInError == filesToUpload) {
                displayMessage("danger", "Une erreur s'est produite lors de la publication de ces documents.<br />Veuillez recommencer, si le prob&egrave;me persiste, contactez l'administrateur de cette plateforme.");
            }
        },
        onUploadError: function(id, xhr, status, message){
            filesInError++;
            ui_multi_update_file_status(id, 'danger', xhr.responseText);
            ui_multi_update_file_progress(id, 0, 'danger', false);  
        },
        onFallbackMode: function(){
            displayMessage('error', 'Le module de t&eacute;l&eacute;charg&eacute; n\'est pas pris en charge par votre navigateur.<br />Veuillez changer de navigateur ou mettre &agrave; jour celui-ci.');
        },
        onFileSizeError: function(file){
            displayMessage('warning', 'Le fichier ' + file.name + ' est trop grand pour &ecirc;tre t&eacute;l&eacute;charg&eacute;.<br />Taille maximale autoris&eacute; par fichier : 10 Mo.');
        }
    });
    $('#publish').on('click', function(evt){
        evt.preventDefault();
        $('#message-area .alert').hide();
        var files = $('ul#files li.media').length;
        filesInError = 0;
        filesToUpload = files;
        var category = $('select#category').val();
        var description = $('textarea#description').val();
        var error = '';
        if (files === 0) {
            error = 'Veuillez s&eacute;lectionner un ou plusieurs fichiers.';
        }
        if (!category) {
            if (error.length !== 0) { error += '<br />'; }
            error += 'Veuillez s&eacute;lectionner une cat&eacute;gorie.';
        }
        if (!description) {
            if (error.length !== 0) { error += '<br />'; }
            error += 'Veuillez saisir une description suffisament explicite pour votre publication.';
        }

        if (!error) {
            $('#drag-and-drop-zone').dmUploader('start');
        } else {
            displayMessage('warning', error);
        }
    });
    $('#cancel').on('click', function(evt){
        evt.preventDefault();
        $('#message-area .alert').hide();
        resetPublicationForm();
    });

    // Media deletion.
    $('ul#files').on('click', 'i.fas', function(evt) {
        evt.stopPropagation();
        evt.preventDefault();
        var fileId = $(this).parents('li.media').attr('id');
        $('#'+fileId).remove();
        var files = new Array();
        var id = fileId.replace('uploaderFile', '');
        $('#drag-and-drop-zone').data('dmUploader').queue.forEach(function(file){
            if (file.id !== id) {
                files.push(file);
            }
        });
        $('#drag-and-drop-zone').data('dmUploader').queue = files;
    });

    $('input[type="file"]').change(function () {
        var filename = $(this).val().replace(/^.*[\\\/]/, '');
        if (filename != undefined || filename != '') {
          $(this).next('.custom-file-label').text(filename);
        }
    });

    // On page load.
    var current = parseInt($.trim($('#admin-search-form input[name="current"]').val()));
    var offset = parseInt($.trim($('#admin-search-form input[name="offset"]').val()));
    search(current, offset);

    // Control Enter form submitting.
    $(document).keypress(function(event) {
        if (event.which === 13) {
            event.stopPropagation();
            event.preventDefault();
            search(1, 0);
        }
    });

    // Pagination action.
    $('ul.pagination').on('click', 'a.page-link', function(event) {
        event.stopPropagation();
        event.preventDefault();
        if (!$(this).parent().hasClass('active')) {
            var current = parseInt($.trim($(this).data('page')));
            var offset = parseInt($.trim($(this).data('offset')));
            search(current, offset);
        }
    });

	// Validate simple search form.
	$('#admin-search-form .btn-primary').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        search(1, 0);
    });

    // Confirm document deletion.
    $('#admin-results-area').on('click', 'i.fas', function(event) {
        event.stopPropagation();
        event.preventDefault();
        $('.modal-footer').show();
        showhide('.modal-body', '.modal-loading');
        var documentId = $(this).data('documentid');
        var documentUrl = $(this).parent('.document-header').find('a.mime').attr('href');
        var text = '"'+$(this).parent('.document-header').find('a.mime').html()+'"';
        var documentDate = $(this).parent('.document-header').find('span.date').html();
        if (documentDate !== '') {
            text = text.concat(' dat&eacute; du ').concat(documentDate);
        }
        showPopUp('delete-document', documentId, documentUrl, text);
    });

    // Document deletion.
    $('#delete-document-form .btn-primary').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        $('.modal-footer').hide();
        showhide('.modal-loading', '.modal-body');
        // Check if document is stored on the current server.
        var documentUrl = $('#delete-document-form .document-url').val();
        if (documentUrl.includes(window.location.hostname)) {
            $.ajax({
                type : 'post',
                url : 'action/delete-document',
                dataType : 'html',
                data : $('#delete-document-form').serialize() + '&csrf_name=' + $('#csrf_name').val() + '&csrf_value=' + $('#csrf_value').val()
            }).done(function(data) {
                $('#document'+$('input[name="documentId"]').val()).remove();
                displayMessage('success', data)
            }).fail(function(obj, text, error) {
                displayError(obj, text, error);
            }).always(function() {
                closePopUp('delete-document');
            });
        } else {
            closePopUp('delete-document');
            var domain = documentUrl.substring(0, documentUrl.indexOf('/OpenData'));
            var message = 'Le document ne semble pas &ecirc;tre h&eacute;berg&eacute; sur cette plateforme.<br />';
            message = message.concat('Pour supprimer ce document, veuillez effectuer une demande de suppression aupr&egrave;s de l\'administrateur de la plateforme : ');
            message = message.concat('<a target="_blank" href="').concat(domain).concat('">').concat(domain).concat('</a>');
            displayMessage('danger', message);
        }
    });
});

/**
 * @description Sleep function to sleep treatment.
 * @param {number} ms Sleep time in millisecond
 */
function sleep(ms) {
    return new Promise(function(resolve){setTimeout(resolve, ms);});
}

/**
 * @description Reset all fields of publication form.
 */
function resetPublicationForm() {
    filesInError = filesToUpload = 0
    // Cancel upload
    var files = $('ul#files li.media').length;
    if (files !== 0) {
        $('#drag-and-drop-zone').data('dmUploader').queue = new Array();
    }
    // Clear upload files list.
    $('ul#files').find('li.media').each(function(){
        $(this ).remove();
    });
    // Clear form.
    $('select#category').val('');
    $('textarea#description').val('');
}

/**
 * @description Display a modal pop-up with parameter and text.
 * @param {String} elementId HTML modal id
 * @param {String} documentId Document id to delete
 * @param {String} documentUrl Document URL to delete
 * @param {String} text Text to pass to the model
 */
function showPopUp(elementId, documentId, documentUrl, text) {
    $('#' + elementId + ' .document-id').val(documentId);
    $('#' + elementId + ' .document-url').val(documentUrl);
	$('#' + elementId + ' .text').html(text);
	$('#' + elementId).modal('show');
}

/**
 * Close a modal pop-up
 * @param {String} elementId HTML modal id
 * @see #showPopUp
 */
function closePopUp(elementId) {
	$('#' + elementId).modal('hide')
}

/** @description Execute a research with ajax call.
 * @param {number} current Current page
 * @param {number} offset Offset to start search in solr database
 */
function search(current, offset) {
    showhide('#loading-area', '#admin-results-area');
    $('#message-area .alert').hide();
    $('ul.pagination').empty().hide();
    $('#admin-search-form input[name="current"]').val(current);
    $('#admin-search-form input[name="offset"]').val(offset);
    $.ajax({
        type : 'post',
        url : 'action/admin-search',
        dataType: 'json',
        data: $('#admin-search-form').serialize() + '&siren=' + siren + '&limit=' + limit + '&csrf_name=' + $('#csrf_name').val() + '&csrf_value=' + $('#csrf_value').val()
    }).done(function(data) {
        displayResults(data, current, offset);
    }).fail(function(obj, text, error) {
        displayError(obj, text, error);
    });
}

/** @description Display results area from Ajax JSON data. 
 * @param {object} data JSON results
 * @param {number} current Current page number
 * @param {number} offset Offset to display result
 */
function displayResults(data, current, offset) {
    if (data && data.numFound > 0) {
        $('ul.list-group').empty();
        $('#nb-results span').html(data.numFound);
        data.docs.forEach(function(doc) {
            $('ul.list-group').append(feedResultLine(doc));
        });
        if (data.numFound > limit) {
            $('ul.pagination').html(generatePagination(data.numFound, current, offset));
            $('ul.pagination').show();
        }
        $('#admin-results-area').show();
    } else {
        displayMessage('warning', '<b>0 r&eacute;sultat</b><br />Aucun r&eacute;sultat, veuillez revoir vos crit&egrave;res de recherche');
    }
    $('#loading-area').hide();
}

/** @description Feed an HTML result line from document JSON object.
 * @param {string} doc JSON object from ajax call
 * @return HTML element
 */
function feedResultLine(doc) {
    var line = '';
    if (doc.filepath) {
        var filename = doc.stream_name[0];
        var filepath = doc.filepath[0];
        var filetype= "";
        if(doc.content_type) {
            filetype = doc.content_type[0];
        } else if (doc.stream_content_type){
            filetype = doc.stream_content_type[0];
        }
        if (location.protocol === 'https:') {
            filepath = filepath.replace('http:', 'https:');
        }

        line = '<li class="list-group-item" id="document'.concat(doc.id).concat('"><div class="document-header">');
        line = line.concat('<a class="link mime ').concat(getMimeTypeClass(filetype)).concat('" target="_blank" href="');
        line = line.concat(filepath).concat('" title="').concat(filename);
        line = line.concat('">').concat(filename).concat('</a>');

        if (doc.date) {
            line = line.concat('<span class="date">').concat(toDate(doc.date[0])).concat('</span>');
        }

        line = line.concat('<i class="fas fa-times" data-documentid="').concat(doc.id).concat('"></i>');
        line = line.concat('</div></li>');
    }

    return line;
}

/** @description Creates a new file and add it to our list.
 * @param {string} id File identifier
 * @param {string} status File upload status
 */
function ui_multi_add_file(id, file) {
    var template = $('#files-template').text();
    template = template.replace('%%filename%%', file.name);

    template = $(template);
    template.prop('id', 'uploaderFile' + id);
    template.data('file-id', id);

    $('#files').find('li.empty').fadeOut(); // remove the 'no files yet'
    $('#files').prepend(template);
}

/** @description Changes the status messages on upload files list.
 * @param {string} id File identifier
 * @param {string} status File upload status
 * @param {string} message Message to display
 */
function ui_multi_update_file_status(id, status, message) {
    $('#uploaderFile' + id).find('span').html(message).prop('class', 'status text-' + status);
}

/** @description Updates a file progress, depending on the parameters it may animate it or change the color.
 * @param {string} id File identifier
 * @param {float} percent File upload percentage
 * @param {string} color File upload progress bar color
 * @param {boolean} active File media active flag
 */
function ui_multi_update_file_progress(id, percent, color, active) {
    color = (typeof color === 'undefined' ? false : color);
    active = (typeof active === 'undefined' ? true : active);
    var bar = $('#uploaderFile' + id).find('div.progress-bar');
    bar.width(percent + '%').attr('aria-valuenow', percent);
    bar.toggleClass('progress-bar-striped progress-bar-animated', active);

    if (percent === 0){
        bar.html('');
    } else {
        bar.html(percent + '%');
    }

    if (color !== false){
        bar.removeClass('bg-success bg-info bg-warning bg-danger');
        bar.addClass('bg-' + color);
    }
}
