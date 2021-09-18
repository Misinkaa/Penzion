//.sortable promeni nas UL seznam v radici seznam pomoci jQuery UI
$("#seznam-stranek").sortable({
    //tato vlastnost update rika, co se ma stat, pokud se v seznamu neco posune
    update: () => {

        //.sortable("toArray") nam vrati serazene pole s ID jednotlivych <li>
        let poleSerazenychStranek = $("#seznam-stranek").sortable("toArray");
        console.log(poleSerazenychStranek);

        //zde budeme ajaxem posilat na server neviditelny formular s polem serazenych id 
        //toto si muzeme predstavit jako formular
        $.ajax({
            type: "post",
            url: "admin.php",
            data: {
                novePoradi : poleSerazenychStranek 
            },
            dataType: "json",
            success: function (response) {
                
            }
        });
    }
});

//deaktivujeme odkazy SMAZAT
$(".delete-tlacitko").click((e) => {
    e.preventDefault();

    //toto vypise dialogove okno s tlacitky OK nebo Cancel
    let odpoved = confirm("Opravdu chcete stranku smazat?")

    //pokud uzivatel klikne na OK, tak ho presmerujeme dal
    //pokud odpoved je false, tak nedelat nic
    if (odpoved == true) {
        //pomoci metody. attr("href") zjistime, co je napsano v atributu href u elementu, na ktery uzivatel kliknul
        console.log($(e.currentTarget).attr("href"));

        //udelame redirect
        window.location.href = $(e.currentTarget).attr("href");
    }
});

