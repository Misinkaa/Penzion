<?php
    //uzivatel odeslal kontaktni formular
    if (array_key_exists("kontakt-submit", $_POST)) {

    }

?>


<form action="#" method="post">
    <label for="jmeno">Jméno</label>
    <input type="text" name="jmeno" /> 
    <label for="prijmeni">Příjmení</label>
    <input type="text" name="prijmeni" /> 
    <input type="email" name="email" placeholder="E-mail" /> 
    <textarea name="vzkaz" placeholder="Napište nám..."></textarea> 
    <input type="submit" name="kontakt-submit" value="Odeslat" />
</form>