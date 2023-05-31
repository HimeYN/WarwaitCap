develop_val = ""
function containsUppercase(str) {
    return /^[A-Z]+$/.test(str);
}
const develop = (ev) => {
    ev.preventDefault();
    console.log(ev.target.id);
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
    console.log(elements_json)
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
    row.setAttribute('class','not-searched')
    //console.log(Object.entries(elements_json));
    
    for (let [key,v]of Object.entries(elements_json)) {
        if (isNaN(key)) {
            const cell = document.createElement('td');
            if (key == 'name') {
                cell.setAttribute('class','sticky-col');
            }
            let cellText = document.createTextNode(`${v}`);
            console.log(containsUppercase(key));
            console.log(key);
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

window.onload = function () {
    create_table_matrix();
}