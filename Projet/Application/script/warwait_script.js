const get_week_number= () =>{
    currentDate = new Date();
    startDate = new Date(currentDate.getFullYear(), 0, 1);
    const days = Math.floor((currentDate - startDate) /
        (24 * 60 * 60 * 1000));
         
    const weekNumber = Math.ceil(days / 7);
    return weekNumber;
}



const modulo = (number,modulo_v) => {
    if (number >= 0 && number < modulo_v) {
        return number;
    } else {
        if (number < 0) {
            return modulo(modulo_v+number,modulo_v);
        } else {
            return modulo(number-modulo_v,modulo_v);
        }
    }
}
let options_list = Array();
let search_const = '';
var start_week = get_week_number();
const week_6months_before = (modulo(get_week_number()-26,52)==0) ? 52 : modulo(get_week_number()-26,52); 
var nb_week = 5;


const add_option = (select, option_val) => {
    option = document.createElement('option');
    option.value = option_val;
    option.text = option_val;
    select.appendChild(option);
}

const add_option_default = (select) => {
    option = document.createElement('option');
    option.value = 'default';
    option.text = '';
    select.appendChild(option);
}

const generate_cell = (cell,value) => {
    const cellText = document.createTextNode("");
    if (value == 'IP+ indispo') {
        cellText.textContent = 'IP+';
    }
    else if (!(value == 'mission' || value == 'indispo' || value=='NULL' || value == 'true' || value == 'false')) {
        cellText.textContent = value;
    } 
    cell.setAttribute('class',  value);
    cell.appendChild(cellText);
}

const update = async (value,semaine,nom) => {
    let a = await fetch('../api/edit.php',{
        method:'PUT',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            nom : nom,
            semaine : semaine,
            value : value
        })
    })
    const elements_json = await a.json();
    const name_id = elements_json[0];
    const week = elements_json[1];
    const values = elements_json[2];
    const cell_id = week + '-' + name_id;
    const cell = document.getElementById(cell_id);
    const select = cell.firstChild;
    cell.removeChild(select);
    generate_cell(cell,values);
}

const set_value = (ev) => {
    ev.preventDefault();
    const value = ev.target.value;
    const id = ev.target.id;
    const sem = id.split('-')[0];
    const id_name = id.split('-')[1];
    update(value, sem, id_name);
}

const generate_select_table = (cell,id) => {
    const select = document.createElement('select');
    add_option_default(select);
    options_list.forEach(elt => {
        add_option(select,elt);
    })
    select.setAttribute('id', id);
    select.setAttribute('class', 'select');
    select.addEventListener('change', set_value);
    cell.appendChild(select);
}

const fill_cell = (cell,value,id) => {
    switch (value) {
        case 'NULL':
            generate_select_table(cell,id);
            break;
        default:
            generate_cell(cell,value);
            break;
    }
}

const send_to_server = async (collab) => {
    let a = await fetch('../api/skillmatrix.php', {
        method: 'put',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            search_matrix: collab,
        })
    })
    document.location.href='skillmatrix.php';
}

const set_search_matrix = (ev) => {
    ev.preventDefault();
    const collab = ev.target.textContent;
    send_to_server(collab);
}

const create_row = (elements_json,ligne,tbl_body,strat_week,nb_week) => {
    const row = document.createElement("tr");
    row.setAttribute("class", "warwait");
    row.setAttribute('id',elements_json[ligne]['id']);
    
    for (const [k,v ]of Object.entries(elements_json[ligne])) {
        if (isNaN(k)) {
            if (isNaN(k.substring(1))) {
                if (k == 'pe' || k == 'afficher' || k == 'id') {
                   
                } else {
                    const cell = document.createElement("td");
                    generate_cell(cell,v);
                    if (k == 'reussite') {
                        cell.textContent = cell.textContent+'%';
                        row.appendChild(cell);
                    } else if (k == 'nom') {
                        //const link = document.createElement("a");
                        cell.setAttribute('class','nom');
                        cell.addEventListener('click', set_search_matrix)
                        //link.setAttribute("href",`skillmatrix.php`);
                        //link.appendChild(cell);
                        row.appendChild(cell);
                    } else if (k != 'nom' && k!= 'grade' && k!= 'site'&& k!= 'reussite'&& k!= 'positionnement'&& k!= 'competences'&& k!= 'cv_code'&& k!= 'en_mission'&& k!='id') { 
                        switch (v) {
                            case 0:
                                cell.setAttribute('class','noskill');
                                break;
                            case 1:
                                cell.setAttribute('class','un');
                                break;

                            case 2:
                                cell.setAttribute('class','deux');
                                break;

                            case 3:
                                cell.setAttribute('class','trois');
                                break;

                            default:
                                cell.setAttribute('class','noskill');
                                break;
                        }
                        row.appendChild(cell);
                    }else {
                        row.appendChild(cell);
                    }
                }

            } else {
                const week_number =parseInt(k.substring(1),10);
                if (strat_week+nb_week > 52 ) {
                    if (week_number == start_week) {
                        let key;
                        for (let i = start_week; i<start_week+nb_week;i++) {
                            if (i <=52) {
                                key = `s${i}`;
                            } else {
                                key = `s${modulo(i,52)}`
                            }
                            const cell = document.createElement('td');
                            fill_cell(cell,elements_json[ligne][key],key +'-' +row.getAttribute('id')+"-s")
                            cell.setAttribute("id",key +'-' +row.getAttribute('id').toString());
                            row.appendChild(cell);
                        }
                    }
                } else  {
                    if (week_number >= strat_week && week_number <strat_week+nb_week) {
                        const cell = document.createElement("td");
                        fill_cell(cell,v,k +'-' +row.getAttribute('id').toString()+"-s");
                        cell.setAttribute("id",k +'-' +row.getAttribute('id').toString());
                        row.appendChild(cell);
                    }
                }
            }



            
        }
    }
    
    tbl_body.appendChild(row);
}

const generate_table_search = async (start_week, nb_week,search) => {
    let a = await fetch(`../api/warwait.php?searched=${search}`, {
        method: 'get',
        headers : {
            'Content-Type': 'application/json',
        },
    })

    const elements_json = await  a.json();
    if (JSON.stringify(elements_json)==='[]') {
        const not_found = document.createElement('h2');
        not_found.setAttribute('id', 'not_found');
        document.body.appendChild(not_found);
        document.getElementById('not_found').innerHTML = 'Nobody was found :(';
    } else {
        const not_found = document.getElementById('not_found');
        if (not_found !== null) {
            document.body.removeChild(not_found);
        }
        const tbl = document.createElement("table");
        tbl.setAttribute("id","table");
        const tblhead = document.createElement("thead");
        const row = document.createElement("tr");
        for (var key in elements_json[0]) {
            if (isNaN(key)) {
                if (isNaN(key.substring(1))) {
                    if (key == 'pe' || key == 'afficher' || key == 'id') {

                    } else {
                        const cell = document.createElement("th");
                        let cellText = document.createTextNode(`${key}`);
                        cell.appendChild(cellText);
                        row.appendChild(cell);
                    
                    }
                    
                } else {
                    week_number = parseInt(key.substring(1), 10);
                    if (start_week+nb_week > 52) {
                        if (week_number == start_week) {
                            let k;
                            for (let i = start_week; i<start_week+nb_week;i++) {
                                if (i <=52) {
                                    k = `s${i}`;
                                } else {
                                    k = `s${modulo(i,52)}`
                                }
                                const cell = document.createElement("th");
                                let cellText = document.createTextNode(`${k}`);
                                cell.appendChild(cellText);
                                row.appendChild(cell);
                            }
                        }

                    }else if (week_number >= start_week && week_number <start_week+nb_week) {
                        const cell = document.createElement("th");
                        let cellText = document.createTextNode(`s${week_number}`);
                        cell.appendChild(cellText);
                        row.appendChild(cell);
                    }
                }
            }
            
        }
        tblhead.appendChild(row);
        tbl.appendChild(tblhead);
        const tbl_body = document.createElement("tbody");

        for (var ligne in elements_json) {
            create_row(elements_json, ligne,tbl_body,start_week,nb_week)
        }
        tbl.appendChild(tbl_body);
        document.body.appendChild(tbl);
        let rows = tbl_body.getElementsByClassName('noskill');

        Array.from(rows).forEach(elt => {
            let row = elt.parentElement;
            if (tbl_body.contains(row)) {
                tbl_body.removeChild(row);

            }
            //elt.parentElement.parentElement.removeChild(elt.parentElement);
    
        });
    }
}

const generate_table = async (start_week,nb_week) =>{

    let a = await fetch('../api/warwait.php', {
        method: 'get',
        headers : {
            'Content-Type': 'application/json',
        },

    })

    const elements_json = await  a.json();
    const tbl = document.createElement("table");
    tbl.setAttribute("id","table");
    const tblhead = document.createElement("thead");
    const row = document.createElement("tr");
    for (var key in elements_json[0]) {
        if (isNaN(key)) {
            if (isNaN(key.substring(1))) {
                if (key == 'pe' || key == 'afficher'|| key == 'id') {

                } else {
                    const cell = document.createElement("th");
                    let cellText = document.createTextNode(`${key}`);
                    cell.appendChild(cellText);
                    row.appendChild(cell);
                }
            
            } else {
                week_number = parseInt(key.substring(1), 10);
                if (start_week+nb_week > 52) {
                    if (week_number == start_week) {
                        let k;
                        for (let i = start_week; i<start_week+nb_week;i++) {
                            if (i <=52) {
                                k = `s${i}`;
                            } else {
                                k = `s${modulo(i,52)}`
                            }
                            const cell = document.createElement("th");
                            let cellText = document.createTextNode(`${k}`);
                            cell.appendChild(cellText);
                            row.appendChild(cell);
                        }
                    }

                }else if (week_number >= start_week && week_number <start_week+nb_week) {
                    const cell = document.createElement("th");
                    let cellText = document.createTextNode(`s${week_number}`);
                    cell.appendChild(cellText);
                    row.appendChild(cell);
                }
            }
        }
        
    }
    tblhead.appendChild(row);
    tbl.appendChild(tblhead);
    const tbl_body = document.createElement("tbody");

    for (var ligne in elements_json) {
        create_row(elements_json, ligne,tbl_body,start_week,nb_week)
    }

    tbl.appendChild(tbl_body);
    document.body.appendChild(tbl);
}



const clear_6_months_before = async () => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            week_clear: week_6months_before,
        })
    })
    const elements_json = await a.json();
}


window.onload = function () { 
    
    clear_6_months_before();

    const get_options= async() => {
        let a = await fetch('../api/edit.php?options=',{
            method: 'get',
            headers : {
                'Content-Type': 'application/json',
            },
        })

        const elements_json = await a.json();
        elements_json.forEach(elt => {
            options_list.push(elt.state)
        })
    }

    get_options();
    generate_table(start_week,nb_week);
    
    const week = document.getElementById("week");
    const nb_week_form = document.getElementById("nb_week");
    const search = document.getElementById("search");
    const plus = document.getElementById("plus");
    
    const change_week=(ev) => {
        ev.preventDefault();
        const value = ev.target.value;
        if (value && value >0 && value <53) {
            start_week = parseInt(value,10);
        } else {
            start_week = get_week_number();
        }
        
        tbl = document.getElementById('table');
        if (tbl !== null) {
            document.body.removeChild(tbl);
        }
        generate_table(start_week,nb_week);
        
    }

    const change_nb_week = (ev) => {
        ev.preventDefault();
        const form_data = new FormData(ev.target);
        if (form_data.get('nbsem')) {
            nb_week = parseInt(form_data.get('nbsem'),10);
            console.log(parseInt(form_data.get('nbsem'),10));
        } else {
            nb_week = 5;
        }
        tbl = document.getElementById('table');
        if (tbl !== null) {
            document.body.removeChild(tbl);
        }
        generate_table(start_week,nb_week);
        
    }

    const search_display = (ev) => {
        ev.preventDefault();
        const form_data = new FormData(ev.target);
        s = form_data.get('searched');
        tbl = document.getElementById('table');
        if (tbl !== null) {
            document.body.removeChild(tbl);
        }
        if (s) {
            search_const =s;
        } else {
            search_const = '';
        }
        generate_table_search(start_week,nb_week,s);
    }

    const plus_week = (ev) =>{
        ev.preventDefault();
            if (start_week == 52) {
                start_week = 1;
            } else {
                start_week = start_week+1;
            }
        
        tbl = document.getElementById('table');
        if (tbl !== null) {
            document.body.removeChild(tbl);
        }
        generate_table_search(start_week,nb_week,search_const);
    }

    const moins_week = (ev) =>{
        ev.preventDefault();
        if (start_week ==1) {
            start_week = 52;
        } else {
            start_week = start_week-1;
        }
        
        tbl = document.getElementById('table');
        if (tbl !== null) {
            document.body.removeChild(tbl);
        }
        generate_table_search(start_week,nb_week,search_const)
    }

    moins.addEventListener("click",moins_week);
    plus.addEventListener("click",plus_week);
    search.addEventListener('submit', search_display);
    nb_week_form.addEventListener('submit', change_nb_week);
    week.addEventListener('input', change_week);
    week.addEventListener('submit', ev=> {
        ev.preventDefault();
    });
}

