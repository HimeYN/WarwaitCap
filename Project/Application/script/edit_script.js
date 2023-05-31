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

let search_const = '';
var start_week = get_week_number();
var nb_week = 10;


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

const generate_select_table_edit = (cell,id,value) => {
    const select = document.createElement('select');
    const options = ['indispo','IP+ indispo', 'IP+','mission'];
    add_option(select,value);
    
    options.forEach(element => {
        if (element != value) {
            add_option(select,element); 
        }
        
    });
    select.setAttribute('id', id);
    select.setAttribute('class', 'select');
    select.addEventListener('change', set_value);
    if (value == 'indispo' || value == 'IP+ indispo') {
        cell.setAttribute('class', 'indispo');
    } else if (value == 'mission') {
        cell.setAttribute('class', 'mission');
    } else {
        cell.setAttribute('class', '');
    }
    cell.appendChild(select);
}

const generate_cell = (cell,value,key) => {
    const cellText = document.createTextNode("");
    if (value == 'IP+ indispo') {
        cellText.textContent = 'IP+';
    }
    else if (!(value == 'mission' || value == 'indispo')) {
        cellText.textContent = value;
    } 
    cell.setAttribute('class',  key);
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
    generate_select_table_edit(cell,cell_id+'-s',values);
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
        case null:
            generate_select_table(cell,id);
            break;
        default:
            generate_select_table_edit(cell,id,value);
            break;
    }
}

update_mission_state = async (id,value,row) => {
    let a = await fetch('../api/edit.php',{
        method:'PUT',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            id : id,
            misison_etat : value
        })
    })
    const elements_json = await a.json();
    const name_id = elements_json[0];
    const values = elements_json[1];
    const cell_id = 'mission-' + name_id;
    const cell = document.getElementById(cell_id);
    const select = cell.firstChild;
    cell.removeChild(select);
    generate_select_table_editor_mission(cell,name_id,values,row);
}

const show_row_update = async (id,value,row) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            'checked-id' : value+'-'+id
        })

    })

    const elements_json = await a.json();
    value = elements_json[0];
    id = elements_json[1];
    const cell_id = 'afficher-'+id.toString();
    const cell = document.getElementById(cell_id);
    const checkbox = cell.firstChild;
    cell.removeChild(checkbox);
    generate_select_box_print(cell,id,value,row);
}

const state_mission = (ev) => {
    ev.preventDefault();
    const value = ev.target.value;
    const id = ev.target.id;
    const row = document.getElementById(id).parentElement.parentElement;
    update_mission_state(id,value,row);
}

const show_row = (ev) => {
    ev.preventDefault();
    const id = ev.target.id;
    const row = document.getElementById(id).parentElement.parentElement;
    show_row_update(id.split('-')[0],ev.target.checked.toString() ,row);
}

const generate_select_table_editor_mission = (cell,id,value,row) => {
    const select = document.createElement('select');
    const options_mission = ['true', 'false'];
    add_option(select,value);
    
    options_mission.forEach(element => {
        if (element != value) {
            add_option(select,element); 
        }
        
    });
    select.setAttribute('id', id);
    if (value == 'true') {
        cell.setAttribute('class', 'true_mission');
        row.setAttribute('class', 'mission_statement_true');        
    }else {
        cell.setAttribute('class', 'false_mission');
        row.setAttribute('class', 'warwait');
        
    }
    select.addEventListener('change', state_mission);
    cell.appendChild(select);

}

const generate_select_box_print = (cell,id,value,row) => {
    const checkbox = document.createElement('input');
    checkbox.setAttribute('type', 'checkbox');
    checkbox.setAttribute('id', id+'-print');
    checkbox.setAttribute('value', value);
    if (value === 'true' ){
        checkbox.setAttribute('checked', '');
        row.setAttribute('class', 'warwait');
    } else {
        if (checkbox.hasAttribute('checked')){
            checkbox.removeAttribute('checked');
        }
        checkbox.setAttribute('value', 'false');
        row.setAttribute('class', 'not-shown');
    }
    checkbox.addEventListener('change', show_row);
    cell.appendChild(checkbox);
}

const delete_row =async (ev) => {
    ev.preventDefault();
    
    const id = ev.target.id.split('-')[1];
    let a = await fetch ('../api/edit.php' , {
        method: 'put',
        headers : {
            'Content-Type': 'application/json',
        },
        body : JSON.stringify({
            'delete' : id
        })

    
    })
    const elements_json = await a.json();
    const name = ev.target.parentElement.firstChild.firstChild.textContent;
    console.log(name);
    tbl = document.getElementById('table');
    if (tbl !== null) {
        document.body.removeChild(tbl);
    }
    generate_table(start_week,nb_week);
    alert(`${name} a été supprimé de la Warwait.`)
}

const update_sucess = async (sucessrate, id) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers: {
            'Content-type' : 'application/json',
        },
        body : JSON.stringify({
            'sucessrate' : sucessrate+'-'+id
        })
    })
    const elements_json = await a.json();
}

const change_sucessrate = (ev) => {
    ev.preventDefault();
    const form_data = new FormData(ev.target);
    
    // 1-sucessrate
    const id = ev.target.firstChild.id.split('-')[0];
    let sucessrate;
    if (form_data.get('reussite')) {
        sucessrate = form_data.get('reussite');
    } else {
        sucessrate = '0';
    }
    update_sucess(sucessrate,id)

}


const generate_sucess_rate_input = (cell,id,value,row) => {
    const form_sucess_rate = document.createElement('form');
    form_sucess_rate.setAttribute('method','get');
    const input_sucess_rate = document.createElement('input');
    input_sucess_rate.setAttribute('type', 'number');
    input_sucess_rate.setAttribute('id', id+'-sucessrate');
    input_sucess_rate.setAttribute('name','reussite');
    input_sucess_rate.setAttribute('min','0');
    input_sucess_rate.setAttribute('max','100');
    input_sucess_rate.value = value;
    form_sucess_rate.appendChild(input_sucess_rate);
    form_sucess_rate.addEventListener('submit',change_sucessrate);
    cell.appendChild(form_sucess_rate);
}

const retrun_to_mainpage = (ev) => {
    ev.preventDefault();
    const div = document.getElementById('edit_displayer');
    if (div !== null) {
        const elements = div.firstChild.children[1].firstChild.children;
        Array.from(elements).forEach(elt => {
            if (elt.firstChild !== null) {
                if (elt.firstChild.tagName=='FORM') {
                    console.log(elt.firstChild.firstChild);
                }

            }
        })
        document.body.removeChild(div);
    }
    generate_table_search(start_week,nb_week,search_const)
}

const update_grade = async (id,value) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers: {
            'Content-type' : 'application/json',
        },
        body : JSON.stringify({
            'grade' : id+'-'+value,
        })
    })
    const elements_json = await a.json();

}

const change_grade = (ev) => {
    ev.preventDefault();
    //grade-1
    const id = ev.target.id.split('-')[1];
    const form_data = new FormData(ev.target);
    const value = form_data.get('grade')
    update_grade(id,value);
}

const create_grade_editor = (cell,id,row) => {
    const value = cell.firstChild.textContent;
    cell.removeChild(cell.firstChild);
    const form = document.createElement('form');
    form.setAttribute('method','get');
    form.setAttribute('name','grade');
    form.setAttribute('id','grade-'+id);
    const input = document.createElement('input');
    input.setAttribute('type','text');
    input.setAttribute('name','grade');
    input.setAttribute('value',value);
    form.appendChild(input);
    form.addEventListener('submit',change_grade);
    cell.appendChild(form);

}

const update_site = async (id,value) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers: {
            'Content-type' : 'application/json',
        },
        body : JSON.stringify({
            'site' : id+'-'+value,
        })
    })
    const elements_json = await a.json();
}

const change_site = (ev) => {
    ev.preventDefault();
    const id = ev.target.id.split('-')[1];
    const form_data = new FormData(ev.target);
    const value = form_data.get('site')
    update_site(id,value);
}

const create_site_editor = (cell, id, row) => {
    const value = cell.firstChild.textContent;
    cell.removeChild(cell.firstChild);
    const form = document.createElement('form');
    form.setAttribute('method','get');
    form.setAttribute('name','site');
    form.setAttribute('id','site-'+id);
    const input = document.createElement('input');
    input.setAttribute('type','text');
    input.setAttribute('name','site');
    input.setAttribute('value',value);
    form.appendChild(input);
    form.addEventListener('submit',change_site);
    cell.appendChild(form);
}

const update_positionnement = async (id,value) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers: {
            'Content-type' : 'application/json',
        },
        body : JSON.stringify({
            'positionnement' : id+'-'+value,
        })
    })
    const elements_json = await a.json();
}

const change_positionnement = (ev) => {
    ev.preventDefault();
    const id = ev.target.id.split('-')[1];
    const form_data = new FormData(ev.target);
    const value = form_data.get('positionnement')
    update_positionnement(id,value);
}

const update_competences = async (id,value) => {
    let a = await fetch('../api/edit.php', {
        method: 'put',
        headers: {
            'Content-type' : 'application/json',
        },
        body : JSON.stringify({
            'competences' : id+'-'+value,
        })
    })
    const elements_json = await a.json();
}

const change_competences = (ev) => {
    ev.preventDefault();
    const id = ev.target.id.split('-')[1];
    const form_data = new FormData(ev.target);
    const value = form_data.get('competences')
    update_competences(id,value);
}

const create_competences_editor = (cell,id,row) => {
    const value = cell.firstChild.textContent;
    cell.removeChild(cell.firstChild);
    const form = document.createElement('form');
    form.setAttribute('method','get');
    form.setAttribute('name','competences');
    form.setAttribute('id','competences-'+id);
    const input = document.createElement('input');
    input.setAttribute('type','text');
    input.setAttribute('name','competences');
    input.setAttribute('value',value);
    form.appendChild(input);
    form.addEventListener('submit',change_competences);
    cell.appendChild(form);
}

const create_positionnement_editor = (cell,id,row) => {
    const value = cell.firstChild.textContent;
    cell.removeChild(cell.firstChild);
    const form = document.createElement('form');
    form.setAttribute('method','get');
    form.setAttribute('name','positionnement');
    form.setAttribute('id','positionnement-'+id);
    const input = document.createElement('input');
    input.setAttribute('type','text');
    input.setAttribute('name','positionnement');
    input.setAttribute('value',value);
    form.appendChild(input);
    form.addEventListener('submit',change_positionnement);
    cell.appendChild(form);
}

const modify_displayer = (cell,row,thead) => {
    const id = row.getElementsByClassName('id')[0].firstChild.textContent;

    const table = document.getElementById('table');
    if (table !== null) {
        document.body.removeChild(table);
    }
    const div_modifier = document.createElement('div');
    div_modifier.setAttribute('id','edit_displayer');
    const tbl = document.createElement('table');
    const tbody = document.createElement('tbody');
    tbody.appendChild(row);
    tbl.appendChild(thead);
    tbl.appendChild(tbody);
    div_modifier.appendChild(tbl);
    const form_exit = document.createElement('form');
    form_exit.setAttribute('method','get');
    const input_btn = document.createElement('input');
    input_btn.setAttribute('type','submit');
    input_btn.setAttribute('name','back_to_edit');
    input_btn.setAttribute('value','OK');
    form_exit.appendChild(input_btn);
    form_exit.addEventListener('submit', retrun_to_mainpage);
    const div_param = document.createElement('div');
    div_param.setAttribute('id', 'div_param');
    div_param.appendChild(form_exit);
    div_modifier.appendChild(div_param);
    document.body.appendChild(div_modifier);
    const modify_cell = document.getElementById(cell);
    const child_modify_cell = modify_cell.firstChild;
    if (child_modify_cell !== null) {
        modify_cell.removeChild(child_modify_cell);
    }
    const grade_cell = row.getElementsByClassName('grade')[0];
    create_grade_editor(grade_cell,id,row);
    const site_cell = row.getElementsByClassName('site')[0];
    create_site_editor(site_cell,id,row);
    const positionnement_cell = row.getElementsByClassName('positionnement')[0];
    create_positionnement_editor(positionnement_cell,id,row);
    const competences_cell = row.getElementsByClassName('competences')[0];
    create_competences_editor(competences_cell,id,row);

}

const modify_event = (ev) => {
    ev.preventDefault();
    const cell = ev.target.parentElement.id;
    const row = ev.target.parentElement.parentElement;
    const thead = ev.target.parentElement.parentElement.parentElement.parentElement.firstChild;
    modify_displayer(cell,row,thead);
}

const generate_edit_button = (cell,id,value,row) => {
    const form_modify = document.createElement('form');
    form_modify.setAttribute('method','get');
    input_butn = document.createElement('input');
    input_butn.setAttribute('type','submit');
    form_modify.addEventListener('submit', modify_event);
    input_butn.setAttribute('value','modifier');
    input_butn.setAttribute('class','modifier_btn');
    form_modify.appendChild(input_butn);
    cell.appendChild(form_modify);
}

const algo_auto_complete = (id,row) => {
    let previous = 'IP+';
    let week_value;
    for (let i=start_week;i<start_week+nb_week;i++) {
        week_value = document.getElementById('s'+i.toString()+'-'+id);
        if (week_value.firstChild.value == 'default') {
            if (i == start_week) {
                const value = 'IP+';
                update(value,'s'+i.toString(),id);
            } else if (document.getElementById('s'+(i-1).toString()+'-'+id).firstChild.value == 'default') {
                const value = previous;
                update(value,'s'+i.toString(),id);
            } else {
                const value = document.getElementById('s'+(i-1).toString()+'-'+id).firstChild.value;
                previous = value;
                update(value,'s'+i.toString(),id);
            }
        }
    }
}

const autocomplete = (ev) => {
    ev.preventDefault();
    const id = ev.target.id.split('-')[1];
    const row = ev.target.parentElement;
    algo_auto_complete(id,row);
}

const generate_auto_complete = (cell,id,value,row) => {
    cell.setAttribute('class','auto_complete');
    cell.addEventListener('click',autocomplete);
}

const create_row = (elements_json,ligne,tbl_body,strat_week,nb_week) => {
    const row = document.createElement("tr");
 //   row.setAttribute("class", "warwait");
    
    for (const [k,v ]of Object.entries(elements_json[ligne])) {
        if (isNaN(k)) {
            if (isNaN(k.substring(1))) {

                const cell = document.createElement("td");
                if (k == 'nom') {
                    generate_cell(cell,v,k);
                    const link = document.createElement("a");
                    link.setAttribute("href",`skillmatrix.php?seached=${v}`);
                    link.appendChild(cell);
                    row.appendChild(link);
                } else if (k == 'en_mission') {
                    cell.setAttribute('id', 'mission-'+Object.values(elements_json[ligne])[125].toString());
                    generate_select_table_editor_mission(cell,Object.values(elements_json[ligne])[125].toString(),v,row);
                    if (v == 'true') {
                        row.removeAttribute('class');
                        row.setAttribute('class', 'mission_statement_true');
                    }
                    row.appendChild(cell);
                }else if (k=='afficher') {
                    cell.setAttribute('id', 'afficher-'+Object.values(elements_json[ligne])[125].toString());
                    generate_select_box_print(cell,Object.values(elements_json[ligne])[125].toString(),v,row);
                    row.appendChild(cell);
                } else if (k == 'reussite') {
                    cell.setAttribute('id', 'reussite-'+Object.values(elements_json[ligne])[125].toString());
                    generate_sucess_rate_input(cell,Object.values(elements_json[ligne])[125].toString(),v,row);
                    row.appendChild(cell);
                } else if (k == 'pe') {
                    cell.setAttribute('id', 'modifier-'+Object.values(elements_json[ligne])[125].toString());
                    generate_edit_button(cell,Object.values(elements_json[ligne])[125].toString(),v,row);
                    row.appendChild(cell);
                } else if (k == 'cv_code') {
                    cell.setAttribute('id','auto_complet-'+Object.values(elements_json[ligne])[125].toString());
                    generate_auto_complete(cell,Object.values(elements_json[ligne])[125].toString(),v,row);
                    row.appendChild(cell);
                }  else {
                    generate_cell(cell,v,k);
                    row.appendChild(cell);
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
                            fill_cell(cell,elements_json[ligne][key],key +'-' +Object.values(elements_json[ligne])[125].toString()+"-s")
                            cell.setAttribute("id",key +'-' +Object.values(elements_json[ligne])[125].toString());
                            row.appendChild(cell);
                        }
                    }
                } else  {
                    if (week_number >= strat_week && week_number <strat_week+nb_week) {
                        const cell = document.createElement("td");
                        fill_cell(cell,v,k +'-' +Object.values(elements_json[ligne])[125].toString()+"-s");
                        cell.setAttribute("id",k +'-' +Object.values(elements_json[ligne])[125].toString());
                        row.appendChild(cell);
                    }
                }
            }



            
        }
    }
    const cell_del = document.createElement('td');
    cell_del.setAttribute('id', 'delete-'+Object.values(elements_json[ligne])[125].toString());
    cell_del.setAttribute('class', 'delete-cell');
    cell_del.addEventListener('click', delete_row);
    row.appendChild(cell_del);
    
    tbl_body.appendChild(row);
}

const generate_table_search = async (start_week, nb_week,search) => {
    let a = await fetch(`../api/edit.php?searched=${search}`, {
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
                    if (key == 'pe') {
                        const cell = document.createElement("th");
                        let cellText = document.createTextNode(`modifier`);
                        cell.appendChild(cellText);
                        row.appendChild(cell);
                    } else if (key == 'cv_code') {
                        const cell = document.createElement("th");
                        let cellText = document.createTextNode(`auto completer`);
                        cell.appendChild(cellText);
                        row.appendChild(cell);
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
}

const generate_table = async (start_week,nb_week) =>{

    let a = await fetch('../api/edit.php', {
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
                if (key == 'pe') {
                    const cell = document.createElement("th");
                    let cellText = document.createTextNode(`modifier`);
                    cell.appendChild(cellText);
                    row.appendChild(cell);
                } else if (key == 'cv_code') {
                    const cell = document.createElement("th");
                    let cellText = document.createTextNode(`auto completer`);
                    cell.appendChild(cellText);
                    row.appendChild(cell);
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




window.onload = function () { 
    generate_table(start_week,nb_week);
    
    const week = document.getElementById("week");
    const nb_week_form = document.getElementById("nb_week");
    const search = document.getElementById("search");
    const plus = document.getElementById("plus");
    const add_collab = document.getElementById("add_collaborator");

    
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
            nb_week = 10;
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

    const add_collab_f = async (ev) => {
        ev.preventDefault();
        const form_data = new FormData(ev.target);
        const collab = form_data.get('add_collaborator');
        tbl = document.getElementById('table');
        if (collab) {
            let a = await fetch('../api/edit.php', {
                method: 'put',
                headers : {
                    'Content-Type': 'application/json',
                },
                body : JSON.stringify({
                    'collab' : collab
                })
        
            })
            const elements_json = await a.json();
            console.log(elements_json);
            if (elements_json.length == 0) {
                alert(`Le/la collaborateur/trice ${collab} existe déjà`);
            } else {
                if (tbl !== null) {
                    document.body.removeChild(tbl);
                }
                generate_table_search(start_week,nb_week,search_const)
            }
        }
    }

    add_collab.addEventListener('submit', add_collab_f);
    moins.addEventListener("click",moins_week);
    plus.addEventListener("click",plus_week);
    search.addEventListener('submit', search_display);
    nb_week_form.addEventListener('submit', change_nb_week);
    week.addEventListener('submit', change_week);
}

