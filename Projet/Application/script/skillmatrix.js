develop_val = "";
searched_collab = "";
function containsUppercase(str) {
    return /^[A-Z]+([_][A-Z]+)*$/.test(str);
}

const offset_col = (percent,s=0) => {
    let r_max = 215-s;
    let g_max = 245-s;
    let b_max = 225-s;

    let r_min = 249-s;
    let g_min = 255-s;
    let b_min = 252-s;

    let r_offset = (r_max - r_min)*percent;
    let g_offset = (g_max - g_min)*percent;
    let b_offset = (b_max - b_min)*percent;
    if (r_offset<0) {
        r_offset = -r_offset;
    } 
    if (g_offset<0) {
        g_offset = -g_offset;
    }
    if (b_offset<0) {
        b_offset = -b_offset;
    }

    return "rgb("+(r_max-r_offset).toString()+","+(g_max-g_offset).toString()+","+(b_max-b_offset).toString()+")";
}

const develop = (ev) => {
    ev.preventDefault();
    if (develop_val == ev.target.id) {
        develop_val = "";
    } else {
        develop_val = ev.target.id;
    }
    div_main = document.getElementById('table-id');
    if (div_main !== null) {
        document.body.removeChild(div_main);
    }
    create_table_matrix();
}

const create_thead = (thead,elements_json) => {
    const row = document.createElement('tr');
    for (var k in elements_json) {
        if (isNaN(k)) {
            const cell = document.createElement("th");
            if (k == 'name') {
                cell.setAttribute('class','sticky-col_h');
            }
            if (k == 'CLOUD' || k == 'DEVELOPPEMENT'|| k == 'SOFT_SKILLS_ET_MANAGEMENT'|| k == 	'MIDDLEWARES'|| k == 'SUPERVISION'|| k == 'AUTOMATISATION'|| k == 'SYSTEMES'|| k == 'SGBD'|| k == 'VIRTUALISATION'|| k == 'OUTILS'|| k == 'SECURITE_ET_RESEAUX') {
                cell.setAttribute('id',k);
                cell.setAttribute('class','clickable-th')
                cell.addEventListener('click',develop)
            }
            let cellText = document.createTextNode(`${k}`);
            cell.appendChild(cellText);
            row.appendChild(cell);
        }        
    }
    thead.appendChild(row)
} 

const create_row = (tbody,elements_json) => {
    const row = document.createElement('tr');
    //row.setAttribute('class','not-searched')
    //console.log(Object.entries(elements_json));
    
    for (let [key,v]of Object.entries(elements_json)) {
        if (isNaN(key)) {
            const cell = document.createElement('td');
            if (key == 'name') {
                cell.setAttribute('class','sticky-col');
                if (v == searched_collab) {
                    row.setAttribute('class','searched');
                }
            }
            let cellText = document.createTextNode(`${v}`);
            if (!containsUppercase(key)) {
                switch (v) {
                    case 0:
                        cell.setAttribute('class','zero');
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
                }
            } else {
                if (key != "DISPONIBILITE" && key != "ANGLAIS") {
                    let percent = v*100/90;
                    if (row.className=="searched") {
                        percent = v*100/90;
                        cell.style.background=offset_col(percent,15);
                    } else {
                        cell.style.background=offset_col(percent);
                    }
                }
            }
            cell.appendChild(cellText);
            row.appendChild(cell);
        }
    }
    tbody.appendChild(row);
}

const create_tbodys_row = (tbody, elements_json) => {
    for (var ligne in elements_json) {
        create_row(tbody,elements_json[ligne]);
    }
} 
const create_table_matrix = async () => {
    let a;
    if (develop_val == "") {
        a = await fetch('../api/skillmatrix.php', {
            method: 'get',
            headers : {
                'Content-Type': 'application/json',
            },
        })
    } else {
        a = await fetch(`../api/skillmatrix.php?devl=${develop_val}`, {
            method: 'get',
            headers : {
                'Content-Type': 'application/json',
            },
        })
    }
    
    const elements_json = await a.json();
    const div_main = document.createElement('div');
    div_main.setAttribute('class','tscroll');
    div_main.setAttribute('id','table-id');
    const tbl = document.createElement('table');
    const thead = document.createElement('thead');
    create_thead(thead,elements_json[0]);
    tbl.appendChild(thead);
    const tbody = document.createElement('tbody');
    create_tbodys_row(tbody,elements_json);
    tbl.appendChild(tbody);
    div_main.appendChild(tbl);
    document.body.appendChild(div_main);

}

const get_searched_value = async() => {
    let a = await fetch('../api/skillmatrix.php?get_search=',{
        method: 'get',
        headers : {
            'Content-Type': 'application/json',
        },
    })

    let elements_json = await a.json();
    searched_collab = elements_json[0][0];
}

window.onbeforeunload = function () {
    get_searched_value();
}

window.onload = function () {
    get_searched_value();
    create_table_matrix();

    const send_to_server = async (collab) => {
        let a = await fetch('../api/skillmatrix.php', {
            method: 'put',
            headers : {
                'Content-Type': 'application/json',
            },
            body : JSON.stringify({
                search_bar_matrix: collab,
            })
        })
        div_main = document.getElementById('table-id');
        if (div_main !== null) {
            document.body.removeChild(div_main);
        }
        create_table_matrix();
    }

    const set_searched = (ev) => {
        ev.preventDefault();
        const form_data = new FormData(ev.target);
        if (form_data.get('name')) {
            send_to_server(form_data.get('name'));
        }
    }
    const search_bar = document.getElementById('search_bar');
    search_bar.parentElement.addEventListener('submit',set_searched);
}