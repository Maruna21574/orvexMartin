<?php

/**
 * Typy snehovych retazi VERIGA - zdroj dat pre /snehove-retaze-veriga (prehlad),
 * podstranky /snehove-retaze-veriga/{slug} a typ retazi vo formulari dopytu.
 * Kluc = hodnota "type" vo formulari (musi sediet s api/ajax.php).
 */
function verigaTypes(): array
{
    return [
        'osobne' => [
            'slug' => 'osobne-auta',
            'label' => 'Osobné autá',
            'image' => 'osobne-auta',
            'alt' => 'Snehové reťaze VERIGA na osobnom aute',
            'desc' => 'Reťaze rôznych hrúbok, vhodné aj pre vozidlá s tesnými alebo úzkymi blatníkmi. Na jednoduchšiu montáž v zime odporúčame samonapínacie reťaze.',
            'title' => 'Snehové reťaze na osobné autá VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na osobné autá, aj pre vozidlá s úzkymi blatníkmi. Samonapínacie reťaze pre jednoduchú montáž. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku.',
            'h1' => 'Snehové reťaze na osobné autá',
            'lead' => 'Reťaze VERIGA pre osobné autá nájdete v niekoľkých hrúbkach. Hodia sa aj na vozidlá s tesnými alebo úzkymi blatníkmi, kde je medzera medzi kolesom a karosériou malá. Pri zimných cestách vám dajú lepšiu priľnavosť a bezpečnejšiu jazdu na snehu a ľade.',
            'tips' => [
                'Ak chcete reťaze rýchlo a bez námahy nasadiť aj v mraze, odporúčame samonapínacie reťaze, ktoré si po nasadení samy dopnú.',
                'Pri vozidlách s úzkymi blatníkmi dajte pri výbere pozor na hrúbku reťaze. Poradíme vám model, ktorý sa pod auto zmestí.',
                'Rozmer reťaze určuje rozmer pneumatiky. Nájdete ho na jej boku, napríklad 205/55 R16.',
            ],
        ],
        'suv' => [
            'slug' => 'suv-4x4-dodavky',
            'label' => 'SUV, 4×4 a dodávky',
            'image' => 'suv-dodavky',
            'alt' => 'Snehové reťaze VERIGA na SUV a dodávke',
            'desc' => 'Reťaze pre SUV, terénne autá a dodávky v niekoľkých modeloch podľa spôsobu použitia. Pre náročnejších zákazníkov aj hrubšie, odolnejšie prevedenie.',
            'title' => 'Snehové reťaze na SUV, 4x4 a dodávky VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na SUV, terénne autá 4x4 a dodávky. Viac modelov podľa použitia aj robustné prevedenie. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku.',
            'h1' => 'Snehové reťaze na SUV, 4×4 a dodávky',
            'lead' => 'Pre SUV, terénne autá 4×4 a dodávky ponúkame reťaze VERIGA vo viacerých modeloch podľa toho, ako vozidlo používate. Pre náročnejších zákazníkov je k dispozícii aj hrubšie, robustnejšie prevedenie pre prácu v teréne.',
            'tips' => [
                'Vyberte model podľa toho, či jazdíte prevažne po cestách, alebo aj v náročnejšom teréne. Robustnejšie reťaze sú určené na intenzívnejšie používanie.',
                'Pri dodávkach a vozidlách s vyšším zaťažením nám napíšte aj typ a hmotnosť vozidla.',
                'Rozmer reťaze určuje rozmer pneumatiky. Nájdete ho na jej boku, napríklad 235/65 R17.',
            ],
        ],
        'nakladne' => [
            'slug' => 'nakladne-auta-autobusy',
            'label' => 'Nákladné autá a autobusy',
            'image' => 'nakladne-auta',
            'alt' => 'Snehové reťaze VERIGA na nákladnom aute',
            'desc' => 'Reťaze pre úžitkové vozidlá. Modely sa líšia podľa náročnosti a rozmanitosti použitia, výber vám pomôžeme prispôsobiť vášmu vozidlu.',
            'title' => 'Snehové reťaze na nákladné autá a autobusy VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na nákladné autá a autobusy. Modely podľa náročnosti použitia. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku.',
            'h1' => 'Snehové reťaze na nákladné autá a autobusy',
            'lead' => 'Reťaze pre nákladné autá a autobusy tvoria významnú časť výroby VERIGA. Modely sa líšia podľa náročnosti a rozmanitosti použitia, takže vieme vybrať reťaz pre vozidlá na cestách aj pre stroje nasadené v zimnom teréne.',
            'tips' => [
                'Pri nákladných autách je dôležité, ako vozidlo používate: dlhé trasy po cestách, stavba, lesná doprava alebo komunálne služby.',
                'Napíšte nám typ vozidla, počet náprav a rozmer pneumatík, aby sme vedeli ponúknuť správny model.',
                'Rozmer reťaze určuje rozmer pneumatiky. Nájdete ho na jej boku, napríklad 315/80 R22,5.',
            ],
        ],
        'pracovne' => [
            'slug' => 'traktory-pracovne-stroje',
            'label' => 'Traktory a pracovné stroje',
            'image' => 'pracovne-stroje',
            'alt' => 'Snehové reťaze VERIGA na traktore',
            'desc' => 'Reťaze na traktory a ťažké pracovné stroje. Typ a hrúbka reťaze závisí od modelu, zložitosti terénu a hmotnosti stroja.',
            'title' => 'Snehové reťaze na traktory a pracovné stroje VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na traktory a pracovné stroje: GRIP STUD, TRACTOR, TEMPO. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku podľa rozmeru pneumatík.',
            'h1' => 'Snehové reťaze na traktory a pracovné stroje',
            'lead' => 'Pre traktory a pracovné stroje ponúkame tri základné prevedenia reťazí VERIGA: GRIP STUD, TRACTOR a TEMPO. Ich výber závisí od zložitosti terénu a hmotnosti stroja a hrúbky sú odstupňované podľa modelu.',
            'tips' => [
                'Vyberte typ podľa terénu: ľahšie a stredne ťažké podmienky, alebo stredne ťažké až ťažké.',
                'Rozmer reťaze určuje rozmer pneumatiky, napríklad 16.9-30 alebo 18.4-34. Nájdete ho na jej boku.',
                'Všetky ostatné rozmery vám vieme ponúknuť na vyžiadanie.',
            ],
            'models' => ['grip-stud', 'tractor', 'tempo'],
        ],
        'lesne' => [
            'slug' => 'lesne-stroje',
            'label' => 'Lesné stroje',
            'image' => 'lesne-stroje',
            'alt' => 'Snehové reťaze VERIGA na lesnom stroji',
            'desc' => 'Reťaze pre lesnú techniku do náročného terénu, napríklad hrotová reťaz GRIP STUD pre lesníkov. Poradíme s výberom podľa stroja a pneumatík.',
            'title' => 'Snehové reťaze na lesné stroje VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na lesné stroje a lesnú techniku, napríklad hrotová reťaz GRIP STUD. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku.',
            'h1' => 'Snehové reťaze na lesné stroje',
            'lead' => 'Lesné stroje pracujú v blate, snehu a strmom teréne, kde záber pneumatík rozhoduje o bezpečnosti aj výkone. Pre lesníkov ponúkame hrotovú reťaz GRIP STUD s dobrými jazdnými vlastnosťami a záberom, vyrobenú zo špeciálnej, tepelne upravenej ocele.',
            'models' => ['grip-stud', 'tractor', 'tempo'],
            'tips' => [
                'Reťaz GRIP STUD je dostupná v hrúbkach 8 mm (hroty 9 mm), 9,5 mm (hroty 10 mm) a 11 mm (hroty 12,5 mm).',
                'Hrúbku vyberáme podľa hmotnosti stroja a náročnosti terénu. Napíšte nám typ stroja a pneumatiky.',
                'Rozmer reťaze určuje rozmer pneumatiky, napríklad 16.9-30 alebo 18.4-38. Nájdete ho na jej boku.',
            ],
        ],
        'male' => [
            'slug' => 'atv-voziky-zahradne-traktory',
            'label' => 'ATV, vozíky a záhradné traktory',
            'image' => 'male-stroje',
            'alt' => 'Snehové reťaze VERIGA na záhradnom traktore so snehovou frézou',
            'desc' => 'Reťaze pre menšiu mechanizáciu: vysokozdvižné vozíky, záhradné traktory, snehové frézy a ATV, na bežné aj príležitostné použitie.',
            'title' => 'Snehové reťaze na ATV, vozíky a záhradné traktory VERIGA',
            'metaDesc' => 'Snehové reťaze VERIGA na vysokozdvižné vozíky, záhradné traktory, snehové frézy a ATV. Zástupca VERIGA pre Slovensko, vyžiadajte cenovú ponuku.',
            'h1' => 'Snehové reťaze na ATV, vozíky a záhradné traktory',
            'lead' => 'Pre menšiu mechanizáciu, ako sú vysokozdvižné vozíky, záhradné traktory, snehové frézy a ATV, ponúkame reťaze VERIGA v niekoľkých modeloch. Hodia sa na pravidelné aj príležitostné použitie.',
            'tips' => [
                'Výber modelu závisí od zložitosti použitia a hmotnosti stroja.',
                'Napíšte nám typ stroja a rozmer pneumatík (napríklad 13x5.00-6), aby sme vedeli ponúknuť správnu reťaz.',
                'Rozmer reťaze určuje rozmer pneumatiky. Nájdete ho na jej boku.',
            ],
        ],
    ];
}

/** Vyhlada typ podla URL slugu; vrati [kluc, data] alebo null. */
function verigaTypeBySlug(string $slug): ?array
{
    foreach (verigaTypes() as $key => $type) {
        if ($type['slug'] === $slug) {
            return [$key, $type];
        }
    }
    return null;
}

/**
 * Prevedenia retazi VERIGA pre traktory a pracovne stroje (zdroj: letak VERIGA, sep 2022).
 * Kluce: grip-stud, tractor, tempo.
 */
function verigaModels(): array
{
    return [
        'grip-stud' => [
            'name' => 'GRIP STUD',
            'sub' => 'Hrotová reťaz v tvare „H"',
            'image' => 'typ-grip-stud',
            'level' => 'Menej náročný terén',
            'text' => 'Traktorová reťaz pre lesníkov do menej náročného terénu. Vyznačuje sa dobrými jazdnými vlastnosťami a záberom.',
            'bullets' => ['Špeciálna zliatina ocele', 'Kompletne tepelne upravená, čo predlžuje jej životnosť'],
            'thickness' => ['8 mm (hroty 9 mm)', '9,5 mm (hroty 10 mm)', '11 mm (hroty 12,5 mm)'],
        ],
        'tractor' => [
            'name' => 'TRACTOR',
            'sub' => 'Dvojitá traktorová stopa',
            'image' => 'typ-tractor',
            'level' => 'Ľahšie až stredne ťažké podmienky',
            'text' => 'Klasická reťaz v tvare „H" pre poľnohospodárske stroje. Zosilnená záberovými článkami po celej dĺžke, čo zaisťuje lepší záber a bezpečnú priliehavosť na snehu a ľade.',
            'bullets' => ['Cementované, plne pozinkované', 'Obojstranne použiteľné pre dlhšiu životnosť', 'S napínacou reťazou a napínacím hákom'],
            'thickness' => ['6 mm', '7 mm', '8 mm', '9,5 mm'],
        ],
        'tempo' => [
            'name' => 'TEMPO',
            'sub' => 'Vzor „diamant"',
            'image' => 'typ-tempo',
            'level' => 'Stredne ťažké až ťažké podmienky',
            'text' => 'Reťaz so vzorom „diamant" pre poľnohospodárske stroje v stredne ťažkých až ťažkých podmienkach.',
            'bullets' => ['Cementované, plne pozinkované', 'Obojstranne použiteľné pre dlhšiu životnosť', 'S napínacou reťazou a napínacím hákom'],
            'thickness' => ['7 mm', '8 mm', '9,5 mm'],
        ],
    ];
}


/**
 * Rozsirene obsahy podstranok typov retazi (texty podla informacii VERIGA, volne spracovane).
 * Kluce ako verigaTypes(). 'lines' = rady (fotka, text, modely); 'renders' = galeria modelov bez rad.
 * Obrazky: assets/images/veriga/typy/{nazov}.webp
 */
function verigaDetails(): array
{
    $r = static fn(string $file, string $caption = ''): array => [$file, $caption];

    return [
        'osobne' => [
            'about' => [
                'Reťaze VERIGA pre osobné autá ponúkame v niekoľkých hrúbkach. Hodia sa aj na vozidlá s tesnými alebo úzkymi blatníkmi, kde je medzi kolesom a karosériou len malá rezerva.',
                'Na jednoduchšiu montáž v zime odporúčame samonapínacie reťaze. Po nasadení sa samy dopnú, takže ich nemusíte pri mraze opakovane dopínať.',
                'Výrobky sa vyrábajú podľa normy ÖNORM V 5117, ktorá zaručuje kvalitné spracovanie a materiály s dlhou životnosťou.',
            ],
            'photo' => ['p-osobne', 'Osobné autá so snehovými reťazami VERIGA na zasnežených cestách'],
            'renders' => [$r('r-osobne-1'), $r('r-osobne-2'), $r('r-osobne-3')],
            'faq' => [
                ['Čo sú samonapínacie reťaze?', 'Sú to reťaze, ktoré sa po nasadení samy dopnú na pneumatiku, takže je montáž rýchlejšia a jednoduchšia, hlavne v zime.'],
                ['Zmestia sa reťaze na auto s úzkymi blatníkmi?', 'Pre vozidlá s tesnými alebo úzkymi blatníkmi máme reťaze s menšou hrúbkou. Napíšte nám typ auta a rozmer pneumatík a vyberieme vhodný model.'],
            ],
        ],
        'suv' => [
            'about' => [
                'Pre SUV, terénne autá 4×4 a dodávky ponúkame štyri modely reťazí. Každý je prispôsobený inému spôsobu použitia a náročnosti.',
                'Pre SUV sme vyvinuli odolné a zároveň jednoducho nasaditeľné samonapínacie reťaze. Pre náročnejších zákazníkov je k dispozícii aj hrubšie, robustnejšie prevedenie, vďaka ktorému zvládnete aj jazdu v teréne.',
                'Reťaze sa vyrábajú podľa noriem ÖNORM V 5117 alebo V 5119 a vyznačujú sa kvalitným spracovaním a dlhou životnosťou.',
            ],
            'photo' => ['p-suv-van', 'Snehová reťaz VERIGA na kolese dodávky'],
            'renders' => [$r('r-suv-1'), $r('r-suv-2'), $r('r-suv-3'), $r('r-suv-4')],
            'faq' => [
                ['Aký model reťaze vybrať na SUV alebo dodávku?', 'Záleží na tom, či jazdíte hlavne po cestách, alebo aj v teréne. Na bežné použitie stačia samonapínacie reťaze, na náročnejšiu prácu odporúčame hrubšie prevedenie. Napíšte nám typ vozidla a pneumatiky.'],
            ],
        ],
        'nakladne' => [
            'about' => [
                'Reťaze pre nákladné autá a autobusy tvoria významnú časť výroby VERIGA. Modely sa líšia podľa náročnosti a rozmanitosti použitia.',
                'Vyrábajú sa podľa normy ÖNORM V 5119, ktorá je pre tento typ výrobkov zákonom požadovaným štandardom.',
            ],
            'photo' => ['p-kamion-premium', 'Nákladné auto so snehovými reťazami VERIGA'],
            'lines' => [
                ['PREMIUM LINE', 'Najkvalitnejšia rada s osobitne zvýšenou odolnosťou vďaka praktickej konštrukcii, kvalitným materiálom a prepracovanej výrobe. Určená pre profesionálov, ktorí pracujú v najnáročnejších terénoch a podmienkach.',
                    'p-kamion-premium', 'Nákladné auto s reťazami VERIGA Premium Line',
                    [$r('r-kamion-profi-extreme', 'Profi Extreme'), $r('r-kamion-master-truck-mt', 'Master Truck MT'), $r('r-kamion-master-truck-md', 'Master Truck Double MD')]],
                ['STANDARD LINE', 'Klasická rada reťazí pre nákladné autá, autobusy a ľahké úžitkové vozidlá na príležitostné používanie. Podľa použitia je na výber viac modelov: od reťazí na dlhé trasy v preprave až po núdzové reťaze s remienkami na jednoduchší rozjazd.',
                    'p-kamion-standard', 'Montáž snehových reťazí VERIGA na nákladné auto',
                    [$r('r-kamion-arktik-a', 'Arktik A'), $r('r-kamion-s-stop-sst', 'S-Stop SST'), $r('r-kamion-max-bus', 'Max Bus')]],
                ['NORDIC LINE', 'Rada reťazí pre nákladné autá a autobusy vyvinutá pre škandinávsky trh. Vďaka jedinečnému tvaru sa hodí do oblastí s veľkým množstvom snehu a extrémnymi podmienkami, kde je potrebná reťaz s hrotmi (zubatá) na ceste aj mimo nej. Niektoré modely sú dostupné aj s uzamykacím mechanizmom CAM.',
                    'p-kamion-nordic', 'Hrotová snehová reťaz VERIGA Nordic Line',
                    [$r('r-kamion-skandic-cam-sc', 'Skandic Cam SC'), $r('r-kamion-zic-zac', 'Zic Zac'), $r('r-kamion-nordkapp', 'Nordkapp')]],
            ],
            'faq' => [
                ['Aký je rozdiel medzi Premium, Standard a Nordic Line?', 'Premium Line je najodolnejšia rada pre profesionálov v najnáročnejších podmienkach. Standard Line je klasická rada na príležitostné používanie. Nordic Line je rada s hrotmi pre veľa snehu a extrémne podmienky. Poradíme vám, ktorá sa hodí na vaše vozidlo.'],
            ],
        ],
        'pracovne' => [
            'about' => [
                'Reťaze na traktory a ťažké pracovné stroje sa členia podľa modelu a hrúbky článkov. Správny výber určuje zložitosť použitia a hmotnosť traktora alebo stroja.',
                'Vyrábajú sa podľa normy ÖNORM V 5119 z kvalitných materiálov a s prepracovaným výrobným procesom.',
            ],
            'photo' => ['p-traktor-premium', 'Pracovný stroj so snehovými reťazami VERIGA'],
            'lines' => [
                ['PREMIUM LINE', 'Všetky reťaze Premium Line majú praktickú a hrubšiu konštrukciu, kvalitné materiály a prepracovanú výrobu, vďaka čomu sú mimoriadne odolné. Sú určené pre profesionálov v najnáročnejších terénoch a podmienkach.',
                    'p-traktor-premium', 'Pracovný stroj s reťazami VERIGA Premium Line',
                    [$r('r-traktor-maxi-grip-mg', 'Maxi Grip MG'), $r('r-traktor-star-extreme-se', 'Star Extreme SE'), $r('r-traktor-grenland-profi-gp', 'Grenland Profi GP')]],
                ['STANDARD LINE', 'Reťaze Standard Line sú vhodné na každodenné použitie na poľnohospodárskych strojoch v ľahších až stredne ťažkých zimných podmienkach. Hodia sa na zasnežené a zľadovatené povrchy, ako aj do lesných porastov a lesov.',
                    'p-traktor-standard', 'Traktor s reťazami VERIGA Standard Line',
                    [$r('r-traktor-grenland-gr', 'Grenland GR'), $r('r-traktor-tempo-tm', 'Tempo TM'), $r('r-traktor-tractor-tr', 'Tractor TR')]],
            ],
            'faq' => [
                ['Ako vyberiem správny typ reťaze na traktor?', 'Výber závisí od zložitosti terénu, hmotnosti stroja a hrúbky článkov. Na ľahšie až stredne ťažké podmienky je Standard Line, na najnáročnejšie podmienky Premium Line. Napíšte nám typ stroja a pneumatiky.'],
            ],
        ],
        'lesne' => [
            'about' => [
                'Lesné stroje pracujú v blate, snehu a strmom teréne, kde záber pneumatík rozhoduje o bezpečnosti aj výkone.',
                'Pre lesníkov ponúkame hrotovú reťaz GRIP STUD v tvare „H" s dobrými jazdnými vlastnosťami a záberom. Je vyrobená zo špeciálnej zliatiny ocele a kompletne tepelne upravená, čo predlžuje jej životnosť.',
                'Hrúbku reťaze vyberáme podľa hmotnosti stroja a náročnosti terénu, preto nám napíšte typ stroja a pneumatiky.',
            ],
            'photo' => ['p-lesne', 'Pracovník montuje reťaz VERIGA na lesný stroj'],
            'faq' => [
                ['Akú hrúbku reťaze GRIP STUD zvoliť?', 'Reťaz GRIP STUD je dostupná v hrúbkach 8, 9,5 a 11 mm. Hrúbku vyberáme podľa hmotnosti stroja a náročnosti terénu. Napíšte nám typ stroja a pneumatiky a poradíme.'],
            ],
        ],
        'male' => [
            'about' => [
                'Rada reťazí pre menšiu mechanizáciu: vysokozdvižné vozíky, záhradné traktory, snehové frézy a ATV.',
                'Viaceré modely sa hodia pre profesionálov v náročnom teréne, aj pre bežných zákazníkov, ktorí reťaze potrebujú len príležitostne. Správny výber závisí od zložitosti použitia a hmotnosti stroja.',
                'Celá rada je vyrobená z kvalitných materiálov a s precíznym spracovaním.',
            ],
            'photo' => ['p-male', 'Snehová fréza so snehovými reťazami VERIGA'],
            'renders' => [$r('r-male-1'), $r('r-male-2'), $r('r-male-3'), $r('r-male-4')],
            'faq' => [
                ['Hodia sa reťaze aj na občasné použitie?', 'Áno. Rada pre menšiu mechanizáciu je vhodná pre profesionálov aj pre zákazníkov, ktorí reťaze potrebujú len príležitostne, napríklad na odhŕňanie snehu okolo domu.'],
            ],
        ],
    ];
}
