<?php 

    $instanceDB = new PDO(
        "mysql:host=localhost;dbname=penzion;charset=utf8",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    class Stranka {
        private $id;
        private $titulek;
        private $menu;
        private $obrazek;
        private $oldId = ""; //tuto promennou jsme si vytvorili, protoze si chceme ukladat stare id, v pripade UPDATE nejake stranky, u ktere chceme zmenit ID

        function __construct($argId, $argTitulek, $argMenu, $argObrazek) {
            $this->id = $argId;
            $this->titulek = $argTitulek;
            $this->menu = $argMenu;
            $this->obrazek = $argObrazek;
        }

        //nevolame to skrze instanci, volame to skrze tridu
        static public function aktualizujPoradiStranek ($argNovePoradi) {

            foreach($argNovePoradi as $index => $idStranky) {
                $query = $GLOBALS["instanceDB"]->prepare("UPDATE stranka SET poradi=? WHERE id=?");
                $query->execute([$index, $idStranky]); 
            }
        }

        public function getTitulek () {
            return $this->titulek;
        }

        public function setTitulek ($argTitulek) {
            $this->titulek = $argTitulek;
        }

        public function getMenu () {
            return $this->menu;
        }

        public function setMenu ($argMenu) {
            $this->menu = $argMenu;
        }

        public function getObrazek () {
            return $this->obrazek;
        }

        public function setObrazek ($argObrazek) {
            $this->obrazek = $argObrazek;
        }

        public function getId () {
            return $this->id;
        }

        public function setId ($argId) {
            //jeste pred tim, nez nahradime vlastnost id za novou hodnotu, si ulozime stare id
            $this->oldId = $this -> id;
            //nahradime stare id za nove
            $this->id = $argId;
        }

        public function getObsah () {
            //metody uvnitr tridy  nevidi promenne definovane venku
            //my kdyz chceme pouzit promennou $instanceDB, tak ji musime zapsat jako $GLOBALS["instanceDB"]
            //tak bychom zapsali jakoukoliv promennou, ktera je definovana venku
            $query = $GLOBALS["instanceDB"]->prepare("SELECT * FROM stranka WHERE id=?");
            $query->execute([$this->id]);
            //protoze vime, ze vysledkem bude jedna stranka, pouzijeme misto fetchAll() jen fetch()
            $row = $query->fetch();

            //zkontrolujeme timto ifem, zda se dana stranka v databazi nasla nebo ne
            if ($row==false) {
                //stranka se nenasla, vrati se prazdny string
                return "";
            }else{
                //stranka se nasla, vratime jeji obsah
                return $row["obsah"];  
            };        
            //return file_get_contents($this->id.".html");
            //return file_get_contents("{$this->id}".html");
            //obe jsou stejne, vybrat is, ktera nam vic vyhovuje
            //file_get_contents prijima jeden parametr, musime ziskat id a pridat k nemu koncovku html, at dostaneme ten obsah 
            

        }
        public function setObsah($argNovyObsah) {
            $query = $GLOBALS["instanceDB"]->prepare("UPDATE stranka SET obsah=? WHERE id=?");
            $query->execute([$argNovyObsah, $this->id]);

        }

        public function vytvoritNeboUpdatovatMetaData () {
            //musime se rozhodnout, zda udelat update nebo insert
            if ($this->oldId == "") { //pokud je oldId prazdny string, vime, ze se jedna o uplne novou stranku, ktera v databazi jeste neexistuje 

                //zjisti nejvyssi hodnotu poradi vsech stranek v databazi
                $query = $GLOBALS["instanceDB"]->prepare("SELECT MAX(poradi) AS nejvyssi_poradi FROM stranka");
                $query->execute();
                //bude pouze jeden radek, takze misto fetchAll pouziju fetch
                $row = $query->fetch();
                $nejvyssiPoradi = $row["nejvyssi_poradi"];

                $query = $GLOBALS["instanceDB"]->prepare("INSERT INTO stranka SET id=?, titulek=?, menu=?, obrazek=?, poradi=?");
                $query->execute([$this->id, $this->titulek, $this->menu, $this->obrazek, $nejvyssiPoradi + 1]);
            }else{
                $query = $GLOBALS["instanceDB"]->prepare("UPDATE stranka SET id=?, titulek=?, menu=?, obrazek=? WHERE id=?");
                $query->execute([$this->id, $this->titulek, $this->menu, $this->obrazek, $this->oldId]);
            }
        }

        public function smazSe() {
            $query = $GLOBALS["instanceDB"]->prepare("DELETE FROM stranka WHERE id=?");
            $query->execute([$this->id]);
        }
    }
    //vytvorime si prazdne pole, ktere budeme pozdeji plnit instancemi z databaze
    $vsechnyStranky = [];
    //sahneme si do databaze pro vsechny radky v tabulce stranka
    $query = $instanceDB->prepare("SELECT * FROM stranka ORDER BY poradi");
    $query->execute();
    
    $rows = $query->fetchAll();

    //v promenne $rows mame nyni vsechny nase stranky a potrebujeme z nich udelat instance a dokrmit do promenne $vsechnyStranky
    foreach($rows as $row) {
        $vsechnyStranky[$row["id"]] = new Stranka ($row["id"], $row["titulek"], $row["menu"], $row["obrazek"]);
    }

    /*$vsechnyStranky = [
        "domu" => new Stranka("domu", "PrimaPenzion", "Domů","primapenzion-main"),
        "galerie" => new Stranka("galerie", "Fotogalerie", "Galerie", "primapenzion-pool-min"),
        "rezervace" => new Stranka("rezervace", "Rezervace", "Rezervace", "primapenzion-room"),
        "kontakt" => new Stranka("kontakt", "Kontakt", "Napište nám", "primapenzion-room2"),
        "404" => new Stranka("404", "Chyba 404", "", "primapenzion-main")
    ];*/


    /*
    // toto je pole vsech nasich dostupnych stranek
        //klicem je ID stranky, hodnota je pole
        //první položka v poli je titulek stránky
        //druhá položka v poli je, jak se stránka zobrazí v menu
    $vsechnyStranky = [
        "domu" => ["PrimaPenzion", "Domů", "primapenzion-main"],
        "galerie" => ["Fotogalerie", "Galerie", "primapenzion-pool-min"],
        "rezervace" => ["Rezervace", "Rezervace", "primapenzion-room"],
        "kontakt" => ["Kontakt", "Napište nám", "primapenzion-room2"],
        "404" => ["Chyba 404", "", "primapenzion-main"]
    ];

    */
?>