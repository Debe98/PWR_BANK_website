/**
 * Funzione di più alto livello da associare al submit del form, colora il bordo della label del campo
 * a seconda se il contenuto è accettabile o meno
 */
function formChecker(form) {
    let lastRadio = "";
    for (let input of document.getElementsByClassName("fieldInput")) {
        if (input.type === "radio") {
            if (input.name !== lastRadio) {
                changeRadio(input, form);
                lastRadio = input.name;
            }
        }
        else {
            changeChecker(input);
            inputChecker(input);
        }
    }
    if (isAlertDivVisible())
        return false;
    return true;
}

/**
 * Funzione di alto livello da associare al comando 'reset', resetta il colore del bordo
 * e la visualizzazione del campo degli alert
 */
function formReset() {
    for(let input of document.getElementsByClassName("fieldInput")) {
        resetBorderLabel(input);
    }
    resetAlert();
}

/**
 * Funzione da associare all'input di un field, colora il bordo della label del campo
 * a seconda se il contenuto è accettabile o meno
 * @param field campo di riferimento
 */
function inputChecker(field) {
    let isCorrect = (generalChecker(field) === "");
    errorBorderLabel(field, isCorrect);
}

/**
 * Funzione di basso livello che colora il bordo della label di un field
 * @param field campo di riferimento
 * @param esatto boolean che indica se il campo è corretto
 */
function errorBorderLabel(field, esatto) {
    //Il primo sarebbe un campo #text vuoto con previousSibling
    let label = field.previousElementSibling;
    if (esatto) {
        if (!label.classList.replace("errato", "esatto"))
            label.classList.add("esatto");
    }
    else {
        if (!label.classList.replace("esatto", "errato"))
            label.classList.add("errato");
    }
}

/**
 * Funzione di basso livello che resetta il bordo della label di un field
 * @param field campo di riferimento
 */
function resetBorderLabel(field) {
    let label = field.previousElementSibling;
    label.classList.remove("errato");
    label.classList.remove("esatto");
}

/**
 * Funzione da associare al change di un field, mostra la sezione dedicata agli
 * errori a seconda se il contenuto è accettabile o meno
 * @param field campo di riferimento
 */
function changeChecker(field) {
    let errori = generalChecker(field);
    let container = document.getElementById(field.name+"Errore");
    if (errori === "") {
        container.style.display = "";
        container.children[1].innerHTML = "<p>...</p>";
        //console.log("container nascosto: "+field.name);
    }
    else {
        container.style.display = "block";
        container.children[1].innerHTML = errori;
        //console.log("container mostrato: "+field.name);
    }
    handlerAlert();
}

/**
 * Funzione da associare al change di un input radio, mostra la sezione dedicata agli
 * errori a seconda se il contenuto è accettabile o meno
 * @param input campo radio di riferimento
 */
function changeRadio(input, form) {
    let padre = document.getElementsByClassName("avvertenzeForm")[0];
    if (document.getElementById(input.name+"Errore") === null) {    //Per evitare di creare doppioni
        let stringa = "<div id=\"" + input.name + "Errore\" class=\"erroreSpecifico\">\n";
        stringa += "<p class=\"erroreCampo\">" + input.name.substring(0, 1).toUpperCase() + input.name.substring(1) + ":</p>\n";
        stringa += "<div class=\"listaErrori\">...</div>\n</div>\n";
        padre.innerHTML += stringa;
    }

    if (form){
        let valore = form[input.name].value
        for (let radio of form[input.name]) {
            if (radio.value == valore) {
                input = radio;
                break;
            }
        }
    }

    let errori = generalChecker(input);

    let isCorrect = (errori === "");
    let table = document.getElementById(input.name+"Table");
    errorBorderLabel(table, isCorrect);

    let container = document.getElementById(input.name+"Errore");
    if (errori === "") {
        container.style.display = "";
        container.children[1].innerHTML = "<p>...</p>";
        //console.log("container nascosto: "+field.name);
    }
    else {
        container.style.display = "block";
        container.children[1].innerHTML = errori;
        //console.log("container mostrato: "+field.name);
    }
    handlerAlert();
}

function checkUsername(field) {
    let error = "";
    let user = field.value;
    if (user.length < 1) {
        error += "<li>Inserisci un username</li>";
    }
    let regex = /[\s]/;
    if (regex.test(user)) {
        error += "<li>Lo spazio non è un carattere valido</li>";
    }
    if (error === "")
        return error;
    else
        return "<ul>"+error+"</ul>";
}

function checkPassword(field) {
    let error = "";
    let pw = field.value;
    if (pw.length < 1) {
        error += "<li>Inserisci una password</li>";
    }
    let regex = /[\s]/;
    if (regex.test(pw)) {
        error += "<li>Lo spazio non è un carattere valido</li>";
    }
    if (error === "")
        return error;
    else
        return "<ul>"+error+"</ul>";
}

function checkAmount(field) {
    let error = "";
    let importo = field.value;
    let regex = /^(([1-9][0-9]+)|([0-9]))(,[0-9]{1,2})?$/;
    if (regex.test(importo)) {
        return error;
    }
    if (importo.length < 1) {
        error += "<li>Inserisci un valore</li>";
    }
    else {
        if (/[^0-9,]/.test(importo)) {
            error += "<li>L'importo non può contenere i seguenti caratteri: [ ";
            let invalidChar = {};
            for (let c of importo.match(/[^0-9,]/g)) {
                invalidChar[c] = 1;
            }
            for (let c in invalidChar)
                error += "<strong>" + c + "</strong> ";
            error += "]</li>";
        }
        if (/\./.test(importo)) {
            error += "<li>Usa '<strong>,</strong>' invece di '<strong>.</strong>'</li>";
        }
        if (/,/.test(importo)) {
            let cntVirgole = 0;
            for (let c of importo.match(/,/g)) {
                cntVirgole++;
            }
            if (cntVirgole > 1)
                error += "<li>E' ammessa al più una virgola, (presenti: "+cntVirgole+")</li>";
        }
        if (/((,)|([0-9]{3,}))$/.test(importo)) {
            error += "<li>Dopo la virgola inserire solo 1 o 2 cifre</li>";
        }
        if (/^0[0-9]/.test(importo)) {
            error += "<li>Carattere '0' iniziale superfluo</li>";
        }
    }
    if (error === "")
        error = "<li>Formati validi: '5', '3,4' o '2,30'</li>";

    return "<ul>"+error+"</ul>";
}

function checkBeneficiari(field) {
    let error = "";
    let beneficiario = field.value;

    if (beneficiario === null || beneficiario === "") {
        error = "<li>Scegli un beneficiario!</li>";
    }
    if (error === "")
        return error;
    return "<ul>"+error+"</ul>";
}

function checkTipologia(field) {
    let error = "";
    let tipo = field.value;

    if (tipo === 'none' || tipo === null || tipo === "") {
        error = "<li>Scegli una tipologia!</li>";
    }
    else if (tipo !== "entrata" && tipo !== "uscita" && tipo !== "tutti") {
        error = "<li>Tipologia inserita non valida!</li>";
    }

    if (error === "")
        return error;
    return "<ul>"+error+"</ul>";
}

function checkPeriodo(field) {
    let error = "";
    let periodo = field.value;

    if (periodo === 'none' || periodo === null || periodo === "") {
        error = "<li>Scegli un intervallo di tempo!</li>";
    }
    else if (periodo !== "1" && periodo !== "3") {
        error = "<li>Periodo inserito non valido!</li>";
    }

    if (error === "")
        return error;
    return "<ul>"+error+"</ul>";
}

/**
 * Funzione di medio livello che verifica la correttezza del campo di input
 * passato come parametro
 * @param field campo di input
 * @returns {string} resoconto degli errori
 */
function generalChecker(field) {
    if (field.name === "username")
        return checkUsername(field);
    if (field.name === "password")
        return checkPassword(field);
    if (field.name === "importo")
        return checkAmount(field);
    if (field.name === "beneficiari")
        return checkBeneficiari(field);
    if (field.name === "tipologia")
        return checkTipologia(field);
    if (field.name === "periodo")
        return checkPeriodo(field);

    //Non dovrei mai arrivare qui!
    return "<ul><li>Errori vari</li><li>Altri brutti brutti ma bruuuuuuti bruuuuuutti bruutttti</li></ul>";
}

/**
 * Funzione di alto livello che rende visibile la zona "avvertenzeForm"
 * se alcuni dei suoi elementi figli devono essere presentati
 */
function handlerAlert() {
    for (let campo of document.getElementsByClassName("erroreSpecifico")) {
        console.log(campo);
        if(campo.style.display === "block") {
            setAlertDivVisible();
            //console.log("campo alert visibile!");
            return;
        }
    }
    setAlertDivInvisible();
    //console.log("campo alert invisibile!");
}

/**
 * Funzione di medio livello che resetta la zona "avvertenzeForm"
 */
function resetAlert() {
    for (let campo of document.getElementsByClassName("erroreSpecifico")) {
        campo.style.display = "";
        //console.log(campo);
    }
    setAlertDivInvisible();
}

/**
 * Funzione di basso livello che rende visibile la zona "avvertenzeForm"
 */
function setAlertDivVisible() {
    if (!isAlertDivVisible()) {
        let div = document.getElementsByClassName("avvertenzeForm")[0];
        div.style.visibility = "visible";
        div.children[0].style.display = "block"; //nascondo h3
    }
}

/**
 * Funzione di basso livello che rende invisibile la zona "avvertenzeForm"
 */
function setAlertDivInvisible() {
    if (isAlertDivVisible()) {
        let div = document.getElementsByClassName("avvertenzeForm")[0];
        div.style.visibility = "";
        div.children[0].style.display = "";
    }
}

/**
 * Funzione di basso livello che indica se la zona "avvertenzeForm" è visibile
 * @returns {boolean} True se zona errori è visibile, false altrimenti
 */
function isAlertDivVisible() {
    if (document.getElementsByClassName("avvertenzeForm")[0].style.visibility === "visible")
        return true;
    return false;
}

/**
 * Da chiamare al caricamento per creare i box dove si visualizzano gli errori
 * relativi ai parametri di input
 */
function createAlertInput() {
    let padre = document.getElementsByClassName("avvertenzeForm")[0];
    for(let input of document.getElementsByClassName("fieldInput")) {
        if (input.type === "radio");
        else {
            let stringa = "<div id=\"" + input.name + "Errore\" class=\"erroreSpecifico\">\n";
            stringa += "<p class=\"erroreCampo\">" + input.name.substring(0, 1).toUpperCase() + input.name.substring(1) + ":</p>\n";
            stringa += "<div class=\"listaErrori\">...</div>\n</div>\n";
            //console.log(stringa);
            padre.innerHTML += stringa;
        }
    }
}

/**
 * Da chiamare al caricamento per dare le funzioni di verifica ai
 * parametri di input
 */
function setPropertiesInput() {
    for(let input of document.getElementsByClassName("fieldInput")) {
        if (input.type === "radio") {
            input.setAttribute('onchange', "changeRadio(this)");
        }
        else {
            input.setAttribute('oninput', "inputChecker(this)");
            input.setAttribute('onchange', "changeChecker(this)");
        }
    }
}