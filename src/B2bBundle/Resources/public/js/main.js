var menu;
var lastScrollTop;

var MAX_SMOOTH_SCROLL = 60;

// Calculate width of text from DOM element or string. By Phil Freo <http://philfreo.com>
/*$.fn.textWidth = function(text, font) {
    if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
    $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
    return $.fn.textWidth.fakeEl.width() + 8;
};*/

/*$.fn.textWidth = function(text) {
    var htmlCalc = $('<span style="display:inline-block">' + text + '</span>');
    console.log(htmlCalc.html())
    htmlCalc.css('font', this.css('font'))
        .hide()
        .prependTo('body');
    var width = htmlCalc.outerWidth();
    htmlCalc.remove();
    return width;
};*/


/// ---- the plugin, also on GitHub ----
!(function($) {
    // see discussion at https://coderwall.com/p/ziynxq;
    // inspired by https://coderwall.com/p/kdi8ua

    var N = function(key) { return 'getTextSize.' + key; },
        fontMapping = function($o, font) {
            //return {"font": font.font || $o.css('font')};
            var result = {}; // don't affect original object
            $.each(font, function(prop, val) {
                result[prop] = (val || $o.css(prop));
            });
            return result;
        }
    ;

    $.fn.getTextSize = function(dimension, text, font) {
        /// <summary>
        ///		Get width or height of element(s)
        ///		<example><code>$('.el-one, .el-two, a').getTextSize(/*'height'*/ /*, false or new string*/ /*, font construct*/).join('px, ')+'px'</code></example>
        /// </summary>

        dimension = (dimension || 'width');
        // figure out what font aspects we're concerned with
        if( typeof font === "string" ) {
            font = {"font": font};
        }
        // include other common style properties that affect sizing
        font = $.extend({"font": false, "text-transform": false, "letter-spacing": false}, font);

        // allow multiple calculations
        return $.map($(this), function(o) {
            var $o = $(o), $fake = $o.data(N('fake'));
            if (!$fake) {
                // cloning causes duplication issues on subsequent calls
                // can attach to parent, but may be unnecessary parsing vs. body if updating font each time
                $fake = $('<span>').hide().addClass('placeholder').empty().appendTo(document.body);
                $o.data(N('fake'), $fake);
            }
            return $fake.html(text || $o.val() || $o.attr('placeholder') || $o.text()).css(fontMapping($o, font))[dimension]();
        });
    };
})(jQuery);

/* Réinitialiser les filtres dans une barre de filtre */
function initFilters() {
    // Pour chaque input de type checkbox
    $('.filter-item .input-check input[type=checkbox]').each(function() {
        // On passe la propriété checked à false
        $(this).prop('checked', false);
    });
}

function loadToolTips() {
    $('[data-toggle="tooltip"]').tooltip();
}

/* Cocher / décocher les enfants de la checkbox qui sont dans la même .filter-family */
function checkBoxParentItemChecked() {
    $('.filter-item-parent').click(function(e) {
        var parentItem = $(e.target);
        var checked    = parentItem.prop('checked');
        if (typeof checked === "undefined")
            return;

        parentItem.closest('.filter-family').find('.filter-item .input-check input[type=checkbox]').each(function() {
            if ($(this) != parentItem) {
                $(this).prop('checked', checked);
            }
        });
    });
}

function barFixedWithScroll(selector, fakebar) {
    var bar = $(selector),
        fake = $(fakebar);

    if (!$(bar).length)
        return;

    var pos = bar.offset();

    $(window).scroll(function() {
        if($(this).scrollTop() > pos.top - $(menu).outerHeight() && ! bar.hasClass('fixed')) {
            //bar.fadeOut('fast', function(){
            bar.addClass('fixed').css('top', menu.css('height'));//.fadeIn('fast');
            //});
            fake.css('display', 'block');
        } else if($(this).scrollTop() <= pos.top - $(menu).outerHeight() && bar.hasClass('fixed')) {
            //bar.fadeOut('fast', function(){
            bar.removeClass('fixed');//.fadeIn('fast');
            //});
            fake.css('display', 'none');
        }
    });
}

function lightboxWithScroll() {
    var lightbox = $('.lightbox .lightbox-content');

    if (!$(lightbox).length)
        return;

    var pos    = lightbox.offset();
    var height = lightbox.height();

    $(window).scroll(function() {
        var h = $(this).height();
        if (($(this).scrollTop() > (pos.top - (h - height)/2)) && ! lightbox.hasClass('fixed')) {
            console.log(Math.abs($(this).scrollTop() - lastScrollTop));
            if (Math.abs($(this).scrollTop() - lastScrollTop) > MAX_SMOOTH_SCROLL) {
                lightbox.fadeOut('fast', function () {
                    lightbox.addClass('fixed').css('top', (h - height) / 2);
                    lightbox.fadeIn('fast');
                });
            } else {
                lightbox.addClass('fixed').css('top', (h - height) / 2);
            }
        } else if (($(this).scrollTop() <= (pos.top - (h - height)/2)) && lightbox.hasClass('fixed')) {
            console.log(Math.abs($(this).scrollTop() - lastScrollTop));
            if (Math.abs($(this).scrollTop() - lastScrollTop) > MAX_SMOOTH_SCROLL) {
                lightbox.fadeOut('fast', function () {
                    lightbox.removeClass('fixed');
                    lightbox.fadeIn('fast');
                });
            } else {
                lightbox.removeClass('fixed');
            }
        }
        lastScrollTop = $(this).scrollTop();
    });
}

!(function($) {
    // because we love making everything a plugin
    $.fn.selfAdjustingInput = function(breakpointFn) {
        return $(this).on('input', function() {
            var $me = $(this);
            $me.css('width', breakpointFn( $me.getTextSize()[0] ));
        });
    };
})(jQuery);

/* Adapter la largeur des champs de type input à leur placeholder */
function setInputPlaceholderWidth() {
    $('input.auto-size[placeholder]').each(function() {
        var $me = $(this);
        $me.css('width', $me.getTextSize()[0] + 20);
    });
}

function setInputAutoSize() {
    $('input.auto-size').selfAdjustingInput(function(w) {
        return w;
    });
}

$(function () {
    menu = $('.navbar-menu-primary');
    lastScrollTop = $(window).scrollTop();
    loadToolTips();

    checkBoxParentItemChecked();

    barFixedWithScroll('.article-bar-to-fix', '.fake-bar-for-fix');
    lightboxWithScroll();

    setInputPlaceholderWidth();

    $('body').addClass('debug');
    setInputAutoSize();
});



function twInitTableau() {
    // Initialise chaque tableau de classe « avectri »
    [].forEach.call( document.getElementsByClassName("avectri"), function(oTableau) {
        var oEntete = oTableau.getElementsByTagName("tr")[0];
        var nI = 1;
        // Ajoute à chaque entête (th) la capture du clic
        // Un picto flèche, et deux variable data-*
        // - Le sens du tri (0 ou 1)
        // - Le numéro de la colonne
        [].forEach.call( oEntete.querySelectorAll("th"), function(oTh) {
            oTh.addEventListener("click", twTriTableau, false);
            oTh.setAttribute("data-pos", nI);
            if(oTh.getAttribute("data-tri")=="1") {
                oTh.innerHTML += "<span class=\"flecheDesc\"></span>";
            } else {
                oTh.setAttribute("data-tri", "0");
                oTh.innerHTML += "<span class=\"flecheAsc\"></span>";
            }
            // Tri par défaut
            if (oTh.className=="selection") {
                oTh.click();
            }
            nI++;
        });
    });
}

function twInit() {
    twInitTableau();
}
function twPret(maFonction) {
    if (document.readyState != "loading"){
        maFonction();
    } else {
        document.addEventListener("DOMContentLoaded", maFonction);
    }
}
twPret(twInit);

function twTriTableau() {
    // Ajuste le tri
    var nBoolDir = this.getAttribute("data-tri");
    this.setAttribute("data-tri", (nBoolDir=="0") ? "1" : "0");
    // Supprime la classe « selection » de chaque colonne.
    [].forEach.call( this.parentNode.querySelectorAll("th"), function(oTh) {oTh.classList.remove("selection");});
    // Ajoute la classe « selection » à la colonne cliquée.
    this.className = "selection";
    // Ajuste la flèche
    this.querySelector("span").className = (nBoolDir=="0") ? "flecheAsc" : "flecheDesc";

    // Construit la matrice
    // Récupère le tableau (tbody)
    var oTbody = this.parentNode.parentNode.parentNode.getElementsByTagName("tbody")[0];
    var oLigne = oTbody.rows;
    var nNbrLigne = oLigne.length;
    var aColonne = new Array(), i, j, oCel;
    for(i = 0; i < nNbrLigne; i++) {
        oCel = oLigne[i].cells;
        aColonne[i] = new Array();
        for(j = 0; j < oCel.length; j++){
            aColonne[i][j] = oCel[j].innerHTML;
        }
    }

    // Trier la matrice (array)
    // Récupère le numéro de la colonne
    var nIndex = this.getAttribute("data-pos");
    // Récupère le type de tri (numérique ou par défaut « local »)
    var sFonctionTri = (this.getAttribute("data-type")=="num") ? "compareNombres" : "compareLocale";
    // Tri
    aColonne.sort(eval(sFonctionTri));
    // Tri numérique
    function compareNombres(a, b) {return a[nIndex-1] - b[nIndex-1];}
    // Tri local (pour support utf-8)
    function compareLocale(a, b) {return a[nIndex-1].localeCompare(b[nIndex-1]);}
    // Renverse la matrice dans le cas d’un tri descendant
    if (nBoolDir==0) aColonne.reverse();

    // Construit les colonne du nouveau tableau
    for(i = 0; i < nNbrLigne; i++){
        aColonne[i] = "<td>"+aColonne[i].join("</td><td>")+"</td>";
    }

    // assigne les lignes au tableau
    oTbody.innerHTML = "<tr>"+aColonne.join("</tr><tr>")+"</tr>";
}

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + ";"
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function delete_cookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}