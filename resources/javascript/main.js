const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
if (urlParams.has('siren')) {
    // cas megalis
    if (urlParams.get('siren') == '253514491') {
        configuration = {
            limit: 10,
            siren: '253514491',
            title: 'Mégalis Bretagne',
            directory: 'OpenData',
            backgroundcolorstart: '#074263',
            backgroundcolorend: '#e3e6e7',
            gradient: true,
            color: '#e6eaea',
            image: 'logoMegalis.png'
        }
    }else if (urlParams.get('siren') == '222800013') {
    configuration = {
        limit: 10,
        siren: '222800013',
        title: 'Eure-et-Loir département',
        directory: 'OpenData',
        backgroundcolorstart: '#000000',
        backgroundcolorend: '#ffffff',
        gradient: true,
        color: '#e6eaea',
        image: '222800013.png'
    }
    } else if (urlParams.get('siren') == '200044394') {
        configuration = {
            limit: 10,
            siren: '200044394',
            title: 'CC Réolais',
            directory: 'OpenData',
            backgroundcolorstart: '#a9a013',
            backgroundcolorend: '#e3e6e7',
            gradient: true,
            color: '#e6eaea',
            image: '200044394.png'
        }
        //visualisation par defaut
    } else {
            configuration = {
                limit: 10,
                siren: urlParams.get('siren'),
                title: 'siren: '+urlParams.get('siren'),
                directory: 'OpenData',
                backgroundcolorstart: '#a42844',
                backgroundcolorend: '#e3e6e7',
                gradient: true,
                color: '#e6eaea',
                //image: '200044394.png'
            }
        }
}else {
    configuration = {
        limit: 10,
        siren: '',
        //title: "",
        directory: 'OpenData',
        backgroundcolorstart: '#34ab8c',
        backgroundcolorend: '#e3e6e7',
        gradient: true,
        color: '#e6eaea',
        //image: '200044394.png'
    }
}

var title                   = configuration.title;
var backgroundcolorstart    = configuration.backgroundcolorstart;
var backgroundcolorend      = configuration.backgroundcolorend;
var gradient                = configuration.gradient;
var color                   = configuration.color;
var image                   = configuration.image;
var siren                   = configuration.siren;

$(document).ready(function() {

    // Display header if not in an iFrame.
    if (window.top === window.self) {
        // Set background color.
        if (backgroundcolorstart && backgroundcolorstart !== '') {
            $('header').css('background', backgroundcolorstart);
            if (backgroundcolorend && backgroundcolorend !== '') {
                if (gradient) {
                    $('header').css('background', 'linear-gradient(90deg, '+backgroundcolorstart+' 0%, '+backgroundcolorend+' 100%)');
                } else {
                    $('header .col.title').css('background', backgroundcolorstart);
                    $('header .col.logo').css('background', backgroundcolorend);
                }
            }
        }

        // Set color.
        if (color && color !== '') {
            $('header h1').css('color', color);
        }

        // Set title.
        if (title && title !== '') {
            $('header h1 span').html(title + ' - ');
            $('header img').attr('title', title);
        }

        // Set image.
        if (image && image !== '') {
            $('header img').attr('src', window.location.origin + '/resources/img/logo/' + image).show();
        }

        // Finally show header.
        $('header').show();

        $('header').click(function(event) {
            event.stopPropagation();
            event.preventDefault();
            if (siren && siren !== '') {
                window.location.href = '/?siren='+siren;
            }else{
                window.location.href='/'
            }
        });
    }

    // Get entity SIREN in URL.
    if (siren && siren !== '') {
        // Useless if siren is present
        $('#address-area').hide();
    }

    // Contrôle surfacique des formulaires
	(function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    // Close message area.
	$('#message-area button.close').click(function(event) {
        event.stopPropagation();
        event.preventDefault();
		$('#message-area .alert').hide();
    });

    // Erase datepicker.
    $('.erase-date').click(function() {
        $(this).parents('.input-group').find('input[type="date"]').val('');
    });

    // Copy to clipboard
    $('#results-area').on('click', '.list-group-item .copy-btn', function(e) {
        event.stopPropagation();
        event.preventDefault();
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val($(this).data('url')).select();
        try {
            var success = document.execCommand('copy');
            if (success) {
                $(this).trigger('copied', ['Copié !']);
            } else {
                $(this).trigger('copied', ['Copier avec Ctrl+C']);
            }
        } catch (err) {
            $(this).trigger('copied', ['Copier avec Ctrl+C']);
        }
        $temp.remove();
    });
    $('#results-area').on('hover', '.list-group-item .copy-btn', function(event, message) {
        $(this).tooltip('dispose').attr('title', 'Copier dans le presse-papier').tooltip('show');
    });
    // Handler for updating the tooltip message.
    $('#results-area').on('copied', '.list-group-item .copy-btn', function(event, message) {
        $(this).tooltip('dispose').attr('title', message).tooltip('show');
    });

});

/** @description Override jQuery show function to show an HTML element. 
 * @param {string} element HTML element to show
 */
jQuery.fn.show = function() {
    $(this).removeClass('d-none').addClass('show');
}

/** @description Override jQuery hide function to hide an HTML element.  
 * @param {string} element HTML element to hide
 */
jQuery.fn.hide = function() {
    $(this).removeClass('show').addClass('d-none');
}

/** @description Function to remove class starting with a specific filter.  
 * @param {string} filter String filter class to remove
 */
jQuery.fn.removeClassStartingWith = function (filter) {
    $(this).removeClass(function (index, className) {
        return (className.match(new RegExp("\\S*" + filter + "\\S*", 'g')) || []).join(' ');
    });
    return this;
};

/**
 * @description Display an HTML element and hide another one.
 * @param {String} a HTML bloc id to display
 * @param {String} b HTML bloc id to hide
 */
function showhide(a, b) {
	$(a).show();
	$(b).hide();
}

/** @description Display an information message with a specific level color. 
 * @param {string} level Alert level, possible value : primary, secondary, success, danger, warning, info, light or dark 
 * @param {string} radius The message to display.   
 */
function displayMessage(level, message) {
    $('#message-area .alert').removeClassStartingWith('alert-');
    $('#message-area .alert').addClass('alert-' + level);
    $('#message-area .alert span.message').html(message);
    $('#message-area .alert').show();
}

/** @description Display error or warning message from Ajax call. 
 * @param {object} obj Error object
 * @param {string} text Text error
 * @param {string} error Error message
 */
function displayError(obj, text, error) {
    var level;
    switch(obj.status) {
        case 404:
            level = 'warning';
            break;
        case 500:
            level = 'danger';
            break;
        default:
            level = 'danger';
    }
    $('#loading-area').hide();
    displayMessage(level, '<b>' + error + ' </b><br />' + obj.responseText);
}

/** @description Generate a pagination HTML block to navigate through results. 
 * @param {number} total Total of results
 * @param {number} current Current page number
 * @param {number} offset Offset to display result
 */
function generatePagination(total, current, offset) {
    var adj = 3
    var totalPage = Math.ceil(total / limit);
    var prev = current - 1 ;
    var next = current + 1 ;
    var penultimate = totalPage - 1 ;
    var pagination = '';
    
    if (totalPage > 1) {
        if (current > 1) {
            var offset = (prev * limit) - limit;
            pagination += '<li class="page-item"><a class="page-link" data-page="'+prev+'" data-offset="'+offset+'" href="#">&#9668;</a></li>';
        } else {
            pagination += '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">&#9668;</a></li>';
        }

        if (totalPage < 7 + (adj * 2)) {
            pagination += (current === 1) ? '<li class="page-item active"><a class="page-link" href="#">1</a></li>' : '<li class="page-item"><a class="page-link" data-page="1" data-offset="0" href="#">1</a></li>';
 
            for (var index = 2 ; index <= totalPage ; index++) {
                var offset = (index * limit) - limit;
                var active = (index === current) ? ' active' : '';
                pagination += '<li class="page-item '+active+'"><a class="page-link" data-page="'+index+'" data-offset="'+offset+'" href="#">'+index+'</a></li>';
            }
        } else {
            if (current < 2 + (adj * 2)) {
                pagination += (current === 1) ? '<li class="page-item active"><a class="page-link" href="#">1</a></li>' : '<li class="page-item"><a class="page-link" data-page="1" data-offset="0" href="#">1</a></li>';
 
                for (var index = 2; index < 4 + (adj * 2); index++) {
                    var offset = (index * limit) - limit;
                    var active = (index === current) ? ' active' : '';
                    pagination += '<li class="page-item'+active+'"><a class="page-link" data-page="'+index+'" data-offset="'+offset+'" href="#">'+index+'</a></li>';
                }
 
                pagination += '<li class="page-item disabled"><a class="page-link" href="#">&hellip;</a></li>';
                var beforeLast = (penultimate * limit) - limit;
                pagination += '<li class="page-item"><a class="page-link" data-page="'+penultimate+'" data-offset="'+beforeLast+'" href="#">'+penultimate+'</a></li>';
                var last = (totalPage * limit) - limit;
                pagination += '<li class="page-item"><a class="page-link" data-page="'+totalPage+'" data-offset="'+last+'" href="#">'+totalPage+'</a></li>';
            } else if (((adj * 2) + 1 < current) && (current < totalPage - (adj * 2))) {
                pagination += '<li class="page-item"><a class="page-link" data-page="1" data-offset="0" href="#">1</a></li>';
                pagination += '<li class="page-item"><a class="page-link" data-page="2" data-offset="'+limit+'" href="#">2</a></li>';
                pagination += '<li class="page-item disabled"><a class="page-link" href="#">&hellip;</a></li>';
 
                for (var index = current - adj; index <= current + adj; index++) {
                    var offset = (index * limit) - limit;
                    var active = (index === current) ? ' active' : '';
                    pagination += '<li class="page-item'+active+'"><a class="page-link" data-page="'+index+'" data-offset="'+offset+'" href="#">'+index+'</a></li>';
                }
 
                pagination += '<li class="page-item disabled"><a class="page-link" href="#">&hellip;</a></li>';
                var beforeLast = (penultimate * limit) - limit;
                pagination += '<li class="page-item"><a class="page-link" data-page="'+penultimate+'" data-offset="'+beforeLast+'" href="#">'+penultimate+'</a></li>';
                var last = (totalPage * limit) - limit;
                pagination += '<li class="page-item"><a class="page-link" data-page="'+totalPage+'" data-offset="'+last+'" href="#">'+totalPage+'</a></li>';
            } else {
                pagination += '<li class="page-item"><a class="page-link" data-page="1" data-offset="0" href="#">1</a></li>';
                pagination += '<li class="page-item"><a class="page-link" data-page="2" data-offset="'+limit+'" href="#">2</a></li>';
                pagination += '<li class="page-item disabled"><a class="page-link" href="#">&hellip;</a></li>';
 
                for (var index = totalPage - (2 + (adj * 2)); index <= totalPage; index++) {
                    var offset = (index * limit) - limit;
                    var active = (index === current) ? ' active' : '';
                    pagination += '<li class="page-item'+active+'"><a class="page-link" data-page="'+index+'" data-offset="'+offset+'" href="#">'+index+'</a></li>';
                }
            }
        }
        
        if (current === totalPage) {
            pagination += '<li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">&#9658;</a></li>';
        } else {
            offset = (next * limit) - limit;
            pagination += '<li class="page-item"><a class="page-link" data-page="'+next+'" data-offset="'+offset+'" href="#">&#9658;</a></li>';
        }
    }

    return pagination;
}

/** @description Get mime type class from stream content type string value. 
 * @param {string} streamContentType Content type
 * @return String mime class
 */
function getMimeTypeClass(streamContentType) {
    var mimeClass = '';
    switch(streamContentType) {
        case 'application/pdf':
            mimeClass = 'pdf';
            break;
        case 'application/json':
            mimeClass = 'json';
            break;
        case 'application/vnd.oasis.opendocument.text':
            mimeClass = 'odt';
            break;
        case 'application/vnd.oasis.opendocument.presentation':
            mimeClass = 'odp';
            break;
        case 'application/vnd.oasis.opendocument.spreadsheet':
            mimeClass = 'ods';
            break;
        case 'application/vnd.oasis.opendocument.graphics':
            mimeClass = 'odg';
            break;
        case 'application/vnd.ms-excel':
            mimeClass = 'xls';
            break;
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            mimeClass = 'xlsx';
            break;
        case 'application/msword':
            mimeClass = 'doc';
            break;
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            mimeClass = 'docx';
            break;
        case 'application/vnd.ms-powerpoint':
            mimeClass = 'ppt';
            break;
        case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            mimeClass = 'pptx';
            break;
        case 'application/vnd.visio':
            mimeClass = 'vsd';
            break; 
        case 'application/rtf':
            mimeClass = 'rtf';
            break;
        case 'application/xml':
            mimeClass = 'xml';
            break;
        case 'application/xhtml+xml':
        case 'text/html':
            mimeClass = 'html';
            break;
        case 'text/plain':
            mimeClass = 'txt';
            break;
        case 'image/bmp':
        case 'image/gif':
        case 'image/jpeg':
        case 'image/png':
        case 'image/svg+xml':
        case 'image/tiff':
        case 'image/webp':
            mimeClass = 'image';
            break;
        case 'audio/aac':
        case 'audio/midi':
        case 'audio/xmidi':
        case 'audio/mpeg':
        case 'audio/ogg':
        case 'audio/opus':
        case 'audio/wav':
        case 'audio/webm':
        case 'audio/3gpp':
        case 'audio/3gpp2':
            mimeClass = 'audio';
            break;
        case 'video/x-msvideo':
        case 'video/mpeg':
        case 'video/ogg':
        case 'video/mp2t':
        case 'video/webm':
        case 'video/3gpp':
        case 'video/3gpp2':
            mimeClass = 'video';
            break;
        default:
            mimeClass = 'base';
    }

    return mimeClass;
}

/** @description Format a date in fr-FR format (dd/mm/yyyy) from date object. 
 * @param {string} dateObject Date from JSON result
 * @return String date
 */
function toDate(dateObject) {
    var d = new Date(dateObject);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    var date = '';
    if (day < 10) {
        day = '0'.concat(day);
    }
    if (month < 10) {
        month = '0'.concat(month);
    }

    return date.concat(day).concat('/').concat(month).concat('/').concat(year);
}
