<!DOCTYPE html>
<html>
    <head>
        <title>Warwait</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../style/warwait.css">
        <script src="../script/warwait_script.js"></script>
    </head>
    <body>
        <div id='mainContainer'>
        <a href="edit.php" class="editpage"><div class="editpage" title="Edit page"><p2><p2></div></a>
        <a href="edit.php" class="editpagebuton"><div class="editpagebuton"><p2> Edit <p2></div></a>
            <div id="container">
                <form action="warwait.php" method="get" id="week">
                    <input type="number" name="semaine" min="1" max="52" title = "Entrer un numéro de semaine"/>
                </form> 
                <div class="plus" id="plus" title="Semaine d'après"><p></p></div>
                <div class="moins" id="moins" title="Semaine d'avant" ><p></p></div>
            </div>
            <div id="nb-week-box">
                <h1>Warwait</h1> 
                <form action="warwait.php" method="get" id="nb_week">
                    <input type="number" id="nbsem" name ="nbsem" placeholder ="Combien de semaines ?" min="5" max ="13" />
                </form>
            </div>

            <div id="search-box">
                <form action = "warwait.php" method = "get" id="search">
                <input type = "search" name = "searched" placeholder="    Rechercher">
                <input type = "submit" name = "searched-btn" value = "">
            </div>
            </form>
        </div>
        

    </body>
</html>

<!-- let a = await fetch('warwait.php',{
    method:'PUT',
    headers : {
        'Content-Type': 'application/json',
    },
    body : JSON.stringify({
        nom : 'NIYONSABA Victor',
        semaine : 's6',
        value : 'mission'
    })
})

let b = a.json(); -->