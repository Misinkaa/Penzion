<?php
    require_once "data.php";

    //zjistime, kam chce uživatel jít
    if (array_key_exists("stranka", $_GET)) {
        $idAktualniStranky = $_GET["stranka"];

        //chceme zjistit, jestli id, ktere jsme vytahli z url, existuje v nasem poli $vsechnyStranky, pokud neexistuje, tak to znamena, ze stranku administrator odstranil
        if (array_key_exists($idAktualniStranky, $vsechnyStranky)) {
            //stranka existuje a nemusime nic delat
        }else{
            //stranka v poli jiz neni
            //musime tedy vypsat 404
            $idAktualniStranky = "404";
        }
    }else{
        //v url neni zadny parametr stranka a proto nastavime aktualni stranku "domu"
        //$idAktualniStranky = "domu";

        //tato funcke nam vaati pole vsech klicu, v tomto pripade se jedna o IDcka
        $idAktualniStranky = array_keys($vsechnyStranky)[0];
    }
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- pole v poli, chceme vypsat index 0 (primapenzion, fotogalerie atd.) -->
    <title><?php echo $vsechnyStranky[$idAktualniStranky]->getTitulek(); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet"> <!--aby se to zobrazilo všem, i těm, kteří nemají font nainstalován v pc-->
</head>
<body>
    <header>
       <div class="container">

            <div class="headerTop">
                <a href="tel:+420775885995">(+420) 775 885 995</a>
                <div class="ikony">
                    <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <a href="?stranka=domu" class="logo">Prima<br />Penzion</a>

            <div class="menu">
                <ul>
                    <?php
                    //vytvorime dynamicke menu podle naseho pole $vsechnyStranky
                        foreach ($vsechnyStranky as $klic => $jednaStranka) {
                            //tento if kontroluje, jestli daná stránka má uvedený nějaky string v menu
                            if ($jednaStranka->getMenu() != "") {
                            echo "<li><a href='?stranka={$klic}'>{$jednaStranka->getMenu()}</a></li>";
                            }
                        }
                    ?>
                    <!--li><a href="index.html">Domů</a></li>
                    <li><a href="kontakt.html">Kontakt</a></li>
                    <li><a href="galerie.html">Galerie</a></li>
                    <li><a href="rezervace.html">Rezervace</a></li-->
                </ul>
            </div>
       </div>
       
       <?php
            echo"<img src='img/{$vsechnyStranky[$idAktualniStranky]->getObrazek()}.jpg' alt='Fotka penzionu' />";
       ?>
    </header>

    <!-- sem budeme pripojovat obsah -->
    <?php
        //pripojime init soubor, ktery pripoji zdrojaky knihovny Shortcode
        require_once "./vendor/shortcode-init.php";
        //pripojime html soubor podle toho, jake je ID
        //vytahneme si z databaze obsah stranky, ve kterem mohou byt shortcode znacky
        $surovyHtmlText = $vsechnyStranky[$idAktualniStranky]->getObsah();

        //surovy text prozeneme metodou process, aby se na misto znacek pripojily dane php soubory
        $zprocesovaneHtml = ShortcodeProcessor::process($surovyHtmlText);

        //vyechujeme zprocesovany text
        echo $zprocesovaneHtml;
        /*
        //pripojime html soubor podle toho jake je id
        require_once "{$idAktualniStranky}.html";
        */
    ?>

    <footer>
        <div class="pata">
            <div class="container">
                <div class="menu">
                    <ul>
                    <?php
                    //vytvorime dynamicke menu podle naseho pole $vsechnyStranky
                        foreach ($vsechnyStranky as $klic => $jednaStranka) {
                            //tento if kontroluje, jestli daná stránka má uvedený nějaky string v menu
                            if ($jednaStranka->getMenu() != "") {
                            echo "<li><a href='?stranka={$klic}'>{$jednaStranka->getMenu()}</a></li>";
                            }
                        }
                    ?>
                    </ul>
                </div>

                <a href="?stranka=domu" class="logo">Prima<br />Penzion</a>

                <p>
                    <i class="fas fa-map-marker-alt fa-spin"></i>
                    <a href="#" target="_blank">PrimaPenzion, Jablonského 2, Praha 7</a>
                </p>
                <p>
                    <i class="fas fa-phone-alt fa-rotate-180"></i>
                    <a href="tel:+420775885995">775885995</a>
                </p>
                <p>
                    <i class="far fa-envelope"></i>
                    <b>info@primapenzion.cz</b>
                </p>

                <div class="ikony">
                    <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copy">
            &copy;<b>PrimaPenzion</b> 2021
        </div>

    </footer>


    <script src="./vendor/jquery/jquery-3.6.0.min.js"></script>
    <?php
        require_once "./vendor/photoswipe-init.php";
    ?>
</body>
</html>