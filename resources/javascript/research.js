var limit = configuration.limit;

$(document).ready(function() {

    // Control Enter form submitting.
    $(document).keypress(function(event) {
        if (event.which === 13) {
            event.stopPropagation();
            event.preventDefault();

            if ($('#search-form').is(':visible')) {
                simpleSearch(1, 0);
            } else if ($('#advanced-search-form').is(':visible')) {
                advancedSearch(1, 0);
            }
        }
    });

    // Show/hide collapse div in results area.
    $('#results-area').on('hidden.bs.collapse', '.list-group-item', function(e) {
        $(this).find('i.fas').addClass('fa-chevron-up').removeClass('fa-chevron-down');
    });
    $('#results-area').on('shown.bs.collapse', '.list-group-item', function(e) {
        $(this).find('i.fas').addClass('fa-chevron-down').removeClass('fa-chevron-up');
        // Only if spinner has been already hide, means preview is already loaded
        var pdfArea = $(this).find('.pdf-display');
        if ($(pdfArea).length && !$(pdfArea).find('.spinner-border').hasClass('d-none')) {
            var canvas = $(this).find('canvas');
            if ($(canvas).length) {
                displayPDF($(canvas).data('url'), $(canvas).attr('id'));
            }
        }
    });

    // Pagination action.
    $('ul.pagination').on('click', 'a.page-link', function(event) {
        event.stopPropagation();
        event.preventDefault();
        if (!$(this).parent().hasClass('active')) {
            var current = parseInt($.trim($(this).data('page')));
            var offset = parseInt($.trim($(this).data('offset')));
            if ($('#results-area').hasClass('simple')) {
                simpleSearch(current, offset);
            } else if ($('#results-area').hasClass('advanced')) {
                advancedSearch(current, offset);
            }
        }
    });

	// Validate simple search form.
	$('#search-form .btn-primary').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        simpleSearch(1, 0);
    });
    
    // Validate advanced search form.
	$('#advanced-search-form .btn-primary').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        advancedSearch(1, 0);
    });

    // New search.
    $('#results-area .btn-secondary.new').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
        exploreDirectory(directory,siren);

        // Set first page research
        $('#collapse-area input[name="current"]').val(1);
        $('#collapse-area input[name="offset"]').val(0);
        showhide('#collapse-area', '#results-area');
    });

    // Hide alert message on collapse action
    $('.btn.collapsed').on('click', function() {
        $('#message-area .alert').hide();
        exploreDirectory(directory,siren);
    });
    simpleSearch(1,0);
});

/** @description Execute a simple search with ajax call.
 * @param {number} current Current page
 * @param {number} offset Offset to start search in solr database
 */
function simpleSearch(current, offset) {
    $('#message-area .alert').hide();
    $('ul.pagination').empty().hide();
    $('#search-form input[name="current"]').val(current);
    $('#search-form input[name="offset"]').val(offset);

    searchFieldValue = $('#search' +'-form input[name="search-field"]').val();


    showhide('#loading-area', '#directory-area');
    $.ajax({
        type : 'post',
        url : 'action/search',
        dataType : 'json',
        data : $('#search-form').serialize() + '&siren=' + siren + '&limit=' + limit
    }).done(function(data) {
        displayResults(data, current, offset, 'simple');
    }).fail(function(obj, text, error) {
        displayError(obj, text, error);
    });

}

/** @description Execute an advanced search with ajax call.
 * @param {number} current Current page
 * @param {number} offset Offset to start search in solr database
 */
function advancedSearch(current, offset) {
    $('#message-area .alert').hide();
    $('ul.pagination').empty().hide();
    $('#advanced-search-form input[name="current"]').val(current);
    $('#advanced-search-form input[name="offset"]').val(offset);


    showhide('#loading-area', '#directory-area');
    $.ajax({
        type : 'post',
        url : 'action/advanced-search',
        dataType : 'json',
        data : $('#advanced-search-form').serialize() + '&siren=' + siren + '&limit=' + limit
    }).done(function(data) {
        displayResults(data, current, offset, 'advanced');
    }).fail(function(obj, text, error) {
        displayError(obj, text, error);
    });

}

/** @description Display results area from Ajax JSON data. 
 * @param {object} data JSON results
 * @param {number} current Current page number
 * @param {number} offset Offset to display result
 * @param {string} type Type of search (advanced or simple)
 */
function displayResults(data, current, offset, type) {
    if (data && data.numFound > 0) {
        $('ul.list-group').empty();
        $('#nb-results span').html(data.numFound);
        var firstLine = true;
        data.docs.forEach(function(doc) {
            $('ul.list-group').append(feedResultLine(doc, firstLine));
            firstLine = false;
        });
        if (data.numFound > limit) {
            $('ul.pagination').html(generatePagination(data.numFound, current, offset));
            $('ul.pagination').show();
        }
        $('#results-area').removeClass('advanced').removeClass('simple').addClass(type);
        showhide('#results-area', '#loading-area');

        // Load only first line PDF preview
        var firstCanvas = $('#results-area ul.list-group li.list-group-item').first().find('canvas');
        if ($(firstCanvas).length) {
            displayPDF($(firstCanvas).data('url'), $(firstCanvas).attr('id'));
        }

        $('[data-toggle="tooltip"]').tooltip({
            container: 'body', 
            trigger: 'hover', 
            placement: 'top'
        });
    } else {
        $('ul.list-group').empty();
        $('#results-area').hide();
        displayMessage('warning', '<b>0 r&eacute;sultat</b><br />Aucun r&eacute;sultat, veuillez revoir vos mots cl&eacute;s de recherche');
    }
}

/** @description Feed an HTML result line from document JSON object.
 * @param {string} doc JSON object from ajax call
 * @param {boolean} collapse Flag to collapse each lines excepted the first one
 * @return HTML element
 */
function feedResultLine(doc, collapse) {
    var classItem = "list-group-item";
    if(doc.typology && doc.typology[0] === "PJ"){
        classItem = classItem.concat(" pj");
    }
    var line = '<li class="'.concat(classItem).concat('" data-toggle="collapse" data-target="#details-').concat(doc.id).concat('" ');
    line = line.concat('aria-expanded="').concat(collapse).concat('" aria-controls="details-').concat(doc.id).concat('">');
    var chevron = 'up';
    if (collapse) {
        chevron = 'down';
    }
    line = line.concat('<div class="document-header"><i class="fas fa-chevron-').concat(chevron).concat(' mr-2"></i>');
    if (doc.description) {
        line = line.concat('<span class="title">').concat(decodeUtf8(doc.description[0])).concat('</span>');
    }

    if (doc.date) {
        line = line.concat('<span class="date">').concat(toDate(doc.date[0])).concat('</span>');
    }
    line = line.concat('</div>');
    line = line.concat('<div class="collapse');
    if (collapse) {
        line = line.concat(' show');
    }
    line = line.concat('" id="details-').concat(doc.id).concat('">');

    var filetype= "";
    if(doc.content_type) {
        filetype = doc.content_type[0];
    } else if (doc.stream_content_type){
        filetype = doc.stream_content_type[0];
    }

    if (doc.filepath) {
        const tab= doc.filepath[0].split('/')
        var filename = unescape(tab[tab.length-1])

        var filepath = doc.filepath[0];
        if (location.protocol === 'https:') {
            filepath = filepath.replace('http:', 'https:');
        }
        if (filetype === 'application/pdf') {
            line = line.concat('<div class="row"><div class="col col-xl-4 col-lg-5 col-md-6 col-sm-auto col-xs-auto">');
            line = line.concat('<a target="_blank" class="link" href="').concat(filepath).concat('" title="').concat(filename).concat('">');
            line = line.concat('<div class="pdf-display text-center"><div class="spinner-border" role="status"><span class="sr-only">Chargement, veuillez patienter...</span></div>');
            line = line.concat('<div class="message d-none"></div>');
            line = line.concat('<canvas data-url="').concat(filepath).concat('" id="').concat(doc.id).concat('"></canvas></div></a>');
            line = line.concat('</div><div class="col col-xl-8 col-lg-7 col-md-6 col-sm-auto col-xs-auto">');
        }
        line = line.concat('<a class="link mime ').concat(getMimeTypeClass(filetype)).concat('" target="_blank" href="');
        line = line.concat(filepath).concat('" title="').concat(filename);
        line = line.concat('">').concat("Voir le document").concat('</a>');
    }
    line = line.concat('<span class="permalink copy-btn" data-url="').concat(filepath).concat('" data-toggle="tooltip" data-placement="top" title="Copier dans le presse-papier"><i class="fas fa-link"></i>&nbsp;Copier le permalien du document</span>')
    line = line.concat('<ul>');
    if (doc.description) {
        line = line.concat('<li>Objet : <span class="font-italic">').concat(decodeUtf8(doc.description[0])).concat('</span></li>');
    }

    line = line.concat('<li>');
    if (doc.documenttype) {
        if (doc.documenttype[0] === 1 ) {
            line = line.concat('Type de document : <span class="font-italic">').concat("D&eacute;lib&eacute;rations").concat('</span>');
        } else if (doc.documenttype[0] === 2 ) {
            line = line.concat('Type de document : <span class="font-italic">').concat("Actes r&eacuteglementaires").concat('</span>');
        } else if (doc.documenttype[0] === 3 ) {
            line = line.concat('Type de document : <span class="font-italic">').concat("Actes individuels").concat('</span>');
        } else if (doc.documenttype[0] === 4 ) {
            line = line.concat('Type de document : <span class="font-italic">').concat("Contrats,conventions et avenants").concat('</span>');
        } else if (doc.documenttype[0] === 5 ) {
            line = line.concat('Type de document : <span class="font-italic">').concat("Documents budg&eacute;taires et financiers").concat('</span>');
        } else {
            line = line.concat('Type de document : <span class="font-italic">').concat("autre").concat('</span>');
        }

    }
    if (doc.documentidentifier) {
        line = line.concat(' <span class="font-italic">(Acte num&eacute;ro ').concat(decodeUtf8(doc.documentidentifier[0])).concat(')</span>');
    }
    line = line.concat('</li>');
    if (doc.classification) {
        line = line.concat('<li>Classification : <span class="font-italic">').concat(decodeUtf8(doc.classification)).concat('</span></li>');
    }
    if (doc.date_de_publication) {
        line = line.concat('<li>Date de publication : <span class="date">').concat(toDate(doc.date_de_publication[0])).concat('</span></li>');
    }
    if (doc.siren) {
        line = line.concat('<li>SIREN : <span class="font-italic">').concat(decodeUtf8(doc.siren)).concat('</span></li>');
    }
    if (doc.blockchain_enable && doc.blockchain_enable[0]) {
        line = line.concat('<li><a href="').concat(decodeUtf8(doc.blockchain_url[0])).concat('"><i class="fa fa-cubes"></i>Lien vers la transaction.</a></li>');
    }
    line = line.concat('</ul>');


    if (doc.filepath && filetype === 'application/pdf') {
        line = line.concat('</div></div>');
    }
    line = line.concat('</div></li>');

    return line;
}

/** @description Display PDF first page on a canvas. 
 * @param {string} documentUrl Open data PDF URL
 * @param {string} area Canvas HTML area
 */
function displayPDF(documentUrl, area) {
    // Asynchronous download of PDF
    pdfjsLib.getDocument(documentUrl).promise.then(function(pdf) {
        // Fetch the first page
        pdf.getPage(1).then(function(page) {
            var viewport = page.getViewport({ scale: 1 });
            
            // Prepare canvas using PDF page dimensions
            var canvas = document.getElementById(area);
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext);
        });
        $('#' + area).parent('.pdf-display').find('.spinner-border').hide();
    }, function (reason) {
        // PDF loading error, file does not exist
        $('#' + area).parent('.pdf-display').find('.spinner-border').hide();
        $('#details-' + area + ' a.link').removeAttr('href').addClass('disabled');
        $('#' + area).parent('.pdf-display').find('.message').html('Document indisponible').show();
    });
}

/** @description Decode UTF-8 encoded string. 
 * @param {string} string String to decode
 * @return String decoded string
 */
function decodeUtf8(string) {
    try {
        return decodeURIComponent(escape(string));
    } catch (error) {
        // console.error(error)
        return string
    }
}
