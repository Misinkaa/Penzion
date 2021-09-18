<div class="container">
    <h1>Galerie</h1>
    <?php
        //urcime si cestu ke slozce fotogalerie
        $cesteKeSlozce = "./vendor/filemanager-library/source/fotogalerie";
        $vsechnySoubory = scandir($cesteKeSlozce);

        foreach ($vsechnySoubory as $jmenoSouboru) {
            //is_file() prijima jako parametr cestu k souboru
            //zkontroluje, zda se jedna o soubor nebo slozku
            //pokud je to soubor vrati true jinak vrati false
            if (is_file($cesteKeSlozce."/".$jmenoSouboru)) {
                //echo $jmenoSouboru;
                echo "<a href='{$cesteKeSlozce}/{$jmenoSouboru}'><img src='{$cesteKeSlozce}/{$jmenoSouboru}' alt='Pokoj' /></a>";
            }            
        }
    ?>
        <!--<img src="img/img1-min.jpg" alt="Pokoj" />
        <img src="img/img2-min.jpg" alt="Pokoj" />
        <img src="img/img3-min.jpg" alt="Pokoj" />
        <img src="img/img4-min.jpg" alt="Pokoj" />
        <img src="img/img5-min.jpg" alt="Pokoj" />
        <img src="img/img6-min.jpg" alt="Pokoj" />
        <img src="img/img7-min.jpg" alt="Pokoj" />
        <img src="img/img8-min.jpg" alt="Pokoj" />-->
</div>