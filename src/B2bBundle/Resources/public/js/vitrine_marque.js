function onStart() {

    var x = document.getElementsByClassName("button-slide-brands");
    for (var i = 0; i < x.length; i++) {
        x[i].onclick = function () {
            var temp = getCookie("target");
            setCookie("target2", temp);
            temp = getCookie("filters");
            setCookie("filters2", temp);
        }
    }

    //On gère les cookies et l'affichage du bouton qui affiche les filtres
    var y = document.getElementById("btn-filter");
    $('.column-filters').on('hide.bs.collapse', function () {
        delete_cookie("filterBar");
        y.innerHTML = "Ajouter des filtres";
    });
    $('.column-filters').on('show.bs.collapse', function () {
        setCookie("filterBar", "yes");
        y.innerHTML = "Masquer les filtres";
    });

    //On regarde si des cookies sont stockés et on affiche en fonction
    var cookiesFiltres = getCookie("filters");
    var cookiesTarget = getCookie("target");
    var cookiesFilterBar = getCookie("filterBar");
    if (cookiesTarget != "") {
        var x = document.getElementById(cookiesTarget);
        var a = "#";
        var b = a.concat(cookiesTarget);
        $(b).tab('show');
    } else {
        //par défaut, on affiche l'onglet Femme
        var x = document.getElementById("femme-tab");
        $('#femme-tab').tab('show');
        setCookie("target", "femme-tab");
    }
    if (cookiesFiltres != "") {
        var cb = $("input:checkbox");
        $.each(cb, function () {
            if (cookiesFiltres.includes(this.value)) {
                this.checked = true;
            }
        });
    }
    if (cookiesFilterBar != "") {
        $('.column-filters').collapse('show');
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        filter(true);
        setCookie("target", e.target.id);

    })

    filter(true);

}

function initSuggestion() {

    var x = document.getElementsByClassName("col tab-pane fade show active")[0].getElementsByClassName("row")[0].getElementsByClassName("afficher");
    var brandsArray = [], filtersArray = [];
    for (var i = 0; i < x.length; i++) {
        var temp = x[i].getElementsByClassName("card-title")[0].innerHTML;
        temp = temp.substring(1);
        temp = temp.substring(0, temp.length - 1);
        brandsArray.push(temp + " - <em>Marque</em>");
    }
    var cb = $("input:checkbox");
    $.each(cb, function () {
        if (this.value.split(":")[0] == "categorie") {
            filtersArray.push(this.value.split(":")[1] + " - <em>Categorie</em>");
        }
    });
    autocomplete(document.getElementById("target"), brandsArray, filtersArray);

}

//C'est Quentin qui l'a fait
function filterTout() {
    var cb = $("input:checkbox");
    var f, temp, temp2;

    $.each(cb, function () {
        temp = this.value.split(":");
        f = temp[0];
        if (temp[1] == "tout" && this.checked == true) {

            $.each(cb, function () {
                temp2 = this.value.split(":");

                if (temp2[0] == f && temp2[1] != "tout") {
                    if (this.checked == true) {
                        this.checked = false;
                    }
                }
            });
        }
    });
    alwaysOneCheck();
    filter();
}

//C'est Quentin qui l'a fait
function alwaysOneCheck() {
    var cb = $("input:checkbox");
    var tab = [], done = [], toCheck = [];
    var k = 0, verif, temp, temp2, inser;

    $.each(cb, function () {
        tab.push(this.value);
        if (this.checked == true) {
            temp = this.value.split(":");
            verif = temp[0];
            if (!(done.includes(verif))) {
                done.push(verif);
            }
        }

        temp2 = this.value.split(":");
        inser = temp2[0];
        if (!(toCheck.includes(inser))) {
            toCheck.push(inser);
        }

    });

    for (var z = 0; z < toCheck.length; z++) {
        if (!(done.includes(toCheck[z]))) {
            var str = toCheck[z].concat(":tout");
            var cBox = document.getElementById(str);
            cBox.checked = true;

        }
    }
}


function filter(bool) {

    var cb = $("input:checkbox");
    var tab = [];
    var x, i;

    filterReset();

    $.each(cb, function () {
        if (this.checked == true && this.value.split(":")[1] != "tout") {
            tab.push(this.value);
        }
    });

    if (tab.length != 0) {

        var categories = [], filtres = [];
        var temp, cookie = "";
        for (i = 0; i < tab.length; i++) {
            cookie = cookie + "|" + tab[i];
            temp = tab[i].split(":");
            categories.push(temp[0]);
            filtres.push(temp[1]);
        }
        setCookie("filters", cookie);
        x = document.getElementsByClassName("filter-card");
        for (i = 0; i < x.length; i++) {
            var boolean = [];
            temp = false;
            var currentCategorie = categories[0];
            for (j = 0; j < tab.length; j++) {
                if (categories[j] != currentCategorie) {
                    currentCategorie = categories[j];
                    boolean.push(temp);
                    temp = false;
                }
                if (x[i].getElementsByClassName(categories[j])[0].innerHTML.indexOf(filtres[j]) != -1) {
                    temp = true;
                }

            }
            boolean.push(temp);
            temp = boolean[0];
            for (var k = 1; k < boolean.length; k++) {
                temp = temp && boolean[k];
            }
            if (temp) {
                addClass(x[i], "afficher");
            }

        }
    }
    else {
        filterAll();
    }
    if (bool) {
        setTimeout(function () {
            initSuggestion();
        }, 500);
    }

    alwaysOneCheck();
    decocheTout();
    affichageVide();
}

//C'est Quentin qui l'a fait
function decocheTout() {
    var cb = $("input:checkbox");
    var f, temp, temp2;

    $.each(cb, function () {
        temp = this.value.split(":");
        f = temp[0];
        if (temp[1] != "tout" && this.checked == true) {
            $.each(cb, function () {
                temp2 = this.value.split(":");

                if (temp2[0] == f && temp2[1] == "tout") {
                    if (this.checked == true) {
                        this.checked = false;
                    }
                }

            });
        }

    });
}

function filterSearchBar(str) {

    filter(false);
    if (str.length != 0) {
        var x, texte;
        x = document.getElementsByClassName("afficher");
        texte = str.toUpperCase();
        for (var i = x.length - 1; i >= 0; i--) {
            var aGarder = false;
            var categories = x[i].getElementsByClassName("categorie")[0].innerHTML.toUpperCase().split(",");
            for (var j = 0; j < categories.length; j++) {
                if (x[i].getElementsByClassName("card-title")[0].innerHTML.toUpperCase().trim().substring(0, texte.length) == texte
                    || categories[j].trim().substring(0, texte.length) == texte) {
                    aGarder = true;
                    break;
                }
            }
            if (!aGarder) {
                removeClass(x[i], "afficher");
            }
        }
        affichageVide();
    }
}

function filterAll() {
    var x;
    x = document.getElementsByClassName("filter-card");
    for (var i = 0; i < x.length; i++) {
        addClass(x[i], "afficher");
    }
}

function filterReset() {
    var x;
    x = document.getElementsByClassName("filter-card");
    for (var i = 0; i < x.length; i++) {
        removeClass(x[i], "afficher");
    }
}


function addClass(element, name) {
    element.classList.add(name);
}


function removeClass(element, name) {
    element.classList.remove(name);
}

function reinit() {
    /* Call when click on "Reiniatiliser" button only */
    delete_cookie("filters");
    delete_cookie("filters2");
    var cb = $("input:checkbox");

    $.each(cb, function () {
        this.checked = false;
    });

    $('#target').val("");

    filterAll();
    alwaysOneCheck();
    affichageVide();
}

function affichageVide() {
    var x = document.getElementsByClassName("col tab-pane fade show active")[0].getElementsByClassName("row")[0].getElementsByClassName("afficher");

    if (x.length == 0) {
        document.getElementsByClassName("affichageVide")[0].innerHTML = "<p class='text-search-null'>Nous sommes désolés, votre recherche n'a donné aucun résultat.</p>";
    }
    else {
        document.getElementsByClassName("affichageVide")[0].innerHTML = "";
    }
}

function autocomplete(inp, arrBrands, arrFilters) {

    inp.removeEventListener("input", eventInput);
    inp.removeEventListener("keydown", eventKeydown);
    document.removeEventListener("click", eventCloseLists);


    var currentFocus;
    inp.addEventListener("input", eventInput);
    inp.addEventListener("keydown", eventKeydown);

    document.addEventListener("click", eventCloseLists);

    function eventCloseLists(e) {
        closeAllLists(e.target);
    }

    function eventInput(e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arrBrands.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arrBrands[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                b.innerHTML = "<strong>" + arrBrands[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arrBrands[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arrBrands[i] + "'>";
                b.addEventListener("click", function (e) {
                    inp.value = this.getElementsByTagName("input")[0].value.split(" -")[0];
                    filterSearchBar(inp.value);
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }

        for (i = 0; i < arrFilters.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arrFilters[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                b.innerHTML = "<strong>" + arrFilters[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arrFilters[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arrFilters[i] + "'>";
                b.addEventListener("click", function (e) {
                    inp.value = this.getElementsByTagName("input")[0].value.split(" -")[0];
                    filterSearchBar(inp.value);
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }

    }

    function eventKeydown(e) {

        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        } else if (e.keyCode == 8) {

        }
    }

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

} // FIN AUTOCOMPLETE
