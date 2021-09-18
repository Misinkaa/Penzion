<?php
    //rikame, ze budeme pouzivat sessiony
    session_start();

    //pripojime si soubor
    require_once "data.php";

    //uzivatel se chce prihlasit
    if (array_key_exists("login-submit", $_POST)) {
        //vytahneme si z postu jeho zadane udaje
        $username = $_POST["username"];
        $heslo = $_POST["heslo"];

        //zkontrolujeme, jestli se ty udaje rovnaji "admin" a "zaba33"
        if ($username == "admin" && $heslo == "zaba33") {
            //pokud se rovnaji, tak muzeme vytvorit sessionu
            $_SESSION["prihlasenyUzivatel"] = $username;
        }
    }

    //uzivatel se chce odhlasit
    if (array_key_exists("logout",$_GET)) {
        //smazeme session
        unset($_SESSION["prihlasenyUzivatel"]);
        //refreshneme stranku
        header("Location: ?");
    }

    //uzivatel chce editovat stranku
    if (array_key_exists("edit", $_GET)) {
        $idStankyKterouChceUzivatelEditovat = $_GET["edit"];
        $aktualniInstance = $vsechnyStranky[$idStankyKterouChceUzivatelEditovat];
    }else if (array_key_exists("add", $_GET)) {
        $aktualniInstance = new Stranka("", "", "", "");
    }
    //uzivatel chce vytvorit novou stranku
        //vyskoci nam chyba, jelikoz stroj hleda obsah v databazi, takze my musime to zakazat
        //volame funkci uvnitr textarey
    /*if (array_key_exists("add", $_GET)) {
        $aktualniInstance = new Stranka("", "", "", "");
    }*/

    //uzivatel chce aktualizovat zmeny
    if (array_key_exists("aktualizace-submit", $_POST)) {
        $novyObsah = $_POST["obsah-stranky"];
        //vytahneme si z postu data, ktera zadal uzivatel do formulare
        $noveId = $_POST["id-stranky"];
        $novyTitulek = $_POST["titulek-stranky"];
        $noveMenu = $_POST["menu-stranky"];
        $novyObrazek = $_POST["obrazek-stranky"];
    

        //kontrola, zda id neni nahodou duplicitni nebo prazdne
        if ($noveId != "") {
            //nasetujeme vlastnosti aktualni instance nasimi daty
            $aktualniInstance->setId($noveId);
            $aktualniInstance->setTitulek($novyTitulek);
            $aktualniInstance->setMenu($noveMenu);
            $aktualniInstance->setObrazek($novyObrazek);

            //updatujeme nebo insertujeme nase metadata
            $aktualniInstance->vytvoritNeboUpdatovatMetaData();

            //potom, co mame uz jistotu, ze v databazi existuje dany radek, tak muzeme udealt update obsahu
            $aktualniInstance->setObsah($novyObsah);

            //refreshneme stranku, aby v url nebylo "?add"
            //presmerujeme na url, kde se mute dal stranka editovat
            header("Location: ?edit={$aktualniInstance->getId()}");
            
        }
    }
    //uzivatel chce smazat stranku
    if (array_key_exists("delete", $_GET)) {
        //zjistim z url, jake ID mame smazat
        $idStrankyKeSmazani = $_GET["delete"];
        //podle ID si vybereme instanci z pole $vsechnyStranky a zavolame metodu smazSe();
        $vsechnyStranky[$idStrankyKeSmazani]->smazSe();
        //stranku refreshneme, aby se aktualizoval seznam
        header("Location: ?");
    }
    
    //prisel nam ajax formular
    if (array_key_exists("novePoradi", $_POST)) {
        //pokud se program dostane do tohoto ifu, znamena to, ze prisel ajax a my oznamime databazi zmenu poradi
        $poleSerazenychStranek = $_POST["novePoradi"];

        Stranka::aktualizujPoradiStranek($poleSerazenychStranek);

        exit; //prikaz exit ukonci php soubor, zde to delame kvuli tomu, ze zpracovavame ajax a neni treba vykreslovat zbytek stranky
    }
 ?>
    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrátorská sekce</title>
</head>
<body>
    <h1>Admin sekce</h1>
    
<?php
    if(array_key_exists("prihlasenyUzivatel",$_SESSION)) {
        echo "Jste přihlášen!";
        echo "<br />";
        //tady je jedno, co je za ?logout=, jelikoz vime, jakeho uzivatele chceme odhlasit
        echo "<a href='?logout=true'>Klikněte sem pro odhlášení</a>";
    ?>
    <?php
        echo "<ul id='seznam-stranek'>";
            foreach ($vsechnyStranky as $instanceJedneStranky) {
                //tady za ?edit= musi byt id stranky, jelikoz my nevime, jakou stranku chce uzivatel editovat
                echo "<li id='{$instanceJedneStranky->getId()}'>
                    <a href='?edit={$instanceJedneStranky->getId()}'>{$instanceJedneStranky->getId()}</a>
                    <a class='delete-tlacitko' href='?delete={$instanceJedneStranky->getId()}'>[Smazat]</a>
                    </li>";
            }
        echo "</ul>";

        echo "<a href=?add=true'>Nová stránka</a>";

        if (isset($aktualniInstance)) {
            ?>
            <form action="" method="post">
                <label for="id">ID</label>
                <!-- value u inputu rika, co se ma predvyplnit, my zde budeme echovat data, ktera patri dane instanci -->
                <input type="text" name="id-stranky" id="id" value="<?php echo $aktualniInstance->getId(); ?>">
                <label for="titul">Titulek</label>
                <input type="text" name="titulek-stranky" id="titul" value="<?php echo $aktualniInstance->getTitulek(); ?>">
                <label for="menu">Menu</label>
                <input type="text" name="menu-stranky" id="menu" value="<?php echo $aktualniInstance->getMenu(); ?>">
                <label for="img">Obrázek</label>
                <input type="text" name="obrazek-stranky" id="img" value="<?php echo $aktualniInstance->getObrazek(); ?>">
            
                <textarea name="obsah-stranky" id="kachna" cols="30" rows="20"><?php echo htmlspecialchars($aktualniInstance->getObsah()); ?></textarea>
                <input type="submit" name="aktualizace-submit" value="Aktualizovat web">
            </form>

            <script src="./vendor/tinymce/tinymce.min.js"></script>
            <script>
                 //selector: #idtextareay
                 tinymce.init({
                            selector: "#kachna",
                            plugins: [
                                    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                                    "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                                    "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                            ],
                            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
                            image_advtab: true ,
                            external_filemanager_path:"./vendor/filemanager-library/filemanager/",
                            external_plugins: { "filemanager" : "plugins/responsivefilemanager/plugin.min.js"},
                            filemanager_title:"Responsive Filemanager",
                            entity_encoding:'raw',
                            verify_html: false,
                            content_css: "./css/style.css"
                        });
            </script>

            <?php
        }
                
    }else{
    ?>   

        <form action="" method="post">
            <label for="username">Uživatelské jméno</label>
            <input type="text" name="username" id="username">
            <label for="heslo">Heslo</label>
            <input type="password" name="heslo" id="heslo">
            <input type="submit" name="login-submit" value="Přihlásit se">
        </form> 

        <?php 
    }
?>
    

    

    <script src="./vendor/jquery/jquery-3.6.0.min.js"></script>
    <script src="./vendor/jquery-ui-1.12.1/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="./js/admin.js"></script>
</body>
</html>