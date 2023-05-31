<!DOCTYPE html>
<html>
    <head>
        <title>Edit</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../style/warwait.css">
        <script src="../script/edit_script.js"></script>
    </head>
    <body>
        <div id='mainContainer'>
            <a href="warwait.php" class="homepage"><div class="homepage"><p2><p2></div></a>
            <div id="container">
                <form action="edit.php" method="get" id="week">
                    <input type="number" name="semaine" min="1" max="52"/>
                </form> 
                <div class="plus" id="plus"><p></p></div>
                <div class="moins" id="moins"><p></p></div>
            </div>
            <div id="nb-week-box">
                <h1>Edit</h1> 
                <form action="edit.php" method="get" id="nb_week">
                    <input type="number" id="nbsem" name ="nbsem" placeholder ="Combien de semaines ?" min="5" max ="30" />
                </form>
            </div>

            <div id="search-box">
                <form action = "edit.php" method = "get" id="search">
                <input type = "search" name = "searched" placeholder="    Rechercher">
                <input type = "submit" name = "searched-btn" value = "">
            </div>
            </form>
        </div>
        <form action="edit" method = "get" id="add_collaborator">
            <input type="text" name="add_collaborator"  placeholder="Ajouter collaborateur (Nom, Prénom)">
        </form>
        

    </body>
</html>