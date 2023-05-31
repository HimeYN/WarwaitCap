const get_week_number= () =>{
    currentDate = new Date();
    startDate = new Date(currentDate.getFullYear(), 0, 1);
    const days = Math.floor((currentDate - startDate) /
        (24 * 60 * 60 * 1000));
         
    const weekNumber = Math.ceil(days / 7);
    return weekNumber;
}

let search_const = '';
var start_week = get_week_number();
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
    else if (!(value == 'mission' || value == 'indispo')) {
        cellText.textContent = value;
    } 
    cell.setAttribute('class',  value);
    cell.appendChild(cellText);
}

const update = async (value,semaine,nom) => {
    let a = await fetch('../api/warwait.php',{
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
    add_option(select, 'indispo');
    add_option(select, 'IP+ indispo');
    add_option(select, 'mission');
    add_option(select, 'IP+');
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

const create_row = (elements_json,ligne,tbl_body,strat_week,nb_week) => {
    const row = document.createElement("tr");
    row.setAttribute("class", "warwait");
    
    for (const [k,v ]of Object.entries(elements_json[ligne])) {
        if (isNaN(k)) {
            if (isNaN(k.substring(1))) {

                const cell = document.createElement("td");
                generate_cell(cell,v);
                if (k == 'nom') {
                    const link = document.createElement("a");
                    link.setAttribute("href",`skillmatrix.php?seached=${v}`);
                    link.appendChild(cell);
                    row.appendChild(link);
                } else {
                    row.appendChild(cell);
                }
            } else {
                const week_number =parseInt(k.substring(1),10);
                if (week_number >= strat_week && week_number <strat_week+nb_week) {
                    const cell = document.createElement("td");
                    fill_cell(cell,v,k +'-' +Object.values(elements_json[ligne])[123].toString()+"-s");
                    cell.setAttribute("id",k +'-' +Object.values(elements_json[ligne])[123].toString());
                    row.appendChild(cell);
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
                    const cell = document.createElement("th");
                    let cellText = document.createTextNode(`${key}`);
                    cell.appendChild(cellText);
                    row.appendChild(cell);
                    
                } else {
                    week_number = parseInt(key.substring(1), 10);
                    if (week_number >= start_week && week_number <start_week+nb_week) {
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
                const cell = document.createElement("th");
                let cellText = document.createTextNode(`${key}`);
                cell.appendChild(cellText);
                row.appendChild(cell);
            
            } else {
                week_number = parseInt(key.substring(1), 10);
                if (week_number >= start_week && week_number <start_week+nb_week) {
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




window.onload = function () { 
    generate_table(start_week,nb_week);
    
    const week = document.getElementById("week");
    const nb_week_form = document.getElementById("nb_week");
    const search = document.getElementById("search");
    const plus = document.getElementById("plus");
    
    const change_week=(ev) => {
        ev.preventDefault();
        const form_data = new FormData(ev.target);
        if (form_data.get('semaine')) {
            start_week = parseInt(form_data.get('semaine'),10);
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
    week.addEventListener('submit', change_week);
}

