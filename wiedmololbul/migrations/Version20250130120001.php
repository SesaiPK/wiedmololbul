<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250130120001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migracja dodająca wpisy do tabeli article';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO article (name, title, content) VALUES 
            ('geraltofrivia', 'Geralt of Rivia',  'Geralt of Rivia was a legendary witcher of the School of the Wolf active throughout the 13th century.
                    He loved the sorceress Yennefer, considered the love of his life despite their tumultuous
                    relationship, and became Ciri''s adoptive father.

                    During the Trial of the Grasses, Geralt exhibited unusual tolerance for the mutagens that grant
                    witchers their abilities. Accordingly, Geralt was subjected to further experimental mutagens which
                    rendered his hair white and may have given him greater speed, strength, and stamina than his fellow
                    witchers.

                    Despite his title, Geralt did not hail from the city of Rivia. After being left with the witchers by
                    his mother, Visenna, he grew up in their keep of Kaer Morhen in the realm of Kaedwen. In the
                    interest of appearing more trustworthy to potential clients, young witchers were encouraged to make
                    up surnames for themselves by master Vesemir. As his first choice, Geralt chose \"Geralt Roger Eric
                    du Haute-Bellegarde\", but this choice was dismissed by Vesemir as silly and pretentious, so \"Geralt\"
                    was all that remained of his chosen name. \"Of Rivia\" was a more practical alternative and Geralt
                    even went so far as to adopt a Rivian accent to appear more authentic. Later, Queen Meve of Lyria
                    knighted him for his valor in the Battle for the Bridge on the Yaruga conferring on him the formal
                    title \"of Rivia\", which amused him. He, therefore, became a true knight.' ),
            ('yenneferofvengerberg', 'Yennefer of Vengerberg', 'Yennefer of Vengerberg, born as Janka on Belleteyn in 1173, was a sorceress who lived in Vengerberg,
                    the capital city of Aedirn. She was Geralt of Rivia''s true love and a mother figure to Ciri, whom
                    she viewed like a daughter to the point that she did everything she could to rescue the girl and
                    keep her from harm.

                    She helped advise King Demavend of Aedirn (though was never a formal royal advisor), was a close
                    friend of Triss Merigold, and the youngest member of the Council of Wizards within the Brotherhood
                    of Sorcerers. After its fall, the Lodge of Sorceresses attempted to recruit her, but they didn''t see
                    eye to eye as the Lodge wanted to advance their own political agenda by using Ciri.'),
            ('emhyrvaremreis', 'Emhyr var Emreis', 'Emhyr var Emreis, Deithwen Addan yn Carn aep Morvudd (Nilfgaardian language: The White Flame Dancing
                    on the Barrows of his Enemies), also known to a few under his alias as Duny, the Urcheon of
                    Erlenwald (Polish: Jeż z Erlenwaldu) was Emperor of the Nilfgaardian Empire, Lord of Metinna,
                    Ebbing, Gemmera, and Sovereign of Nazair and Vicovaro from 1257 until his death sometime in the late
                    13th century. He also became the King of Cintra after marrying a false Cirilla in 1268.[2]

                    His rule of Nilfgaard was highly aggressive, often pursuing expansionist policies similar to those
                    of his predecessors. This led to the outbreak of two wars against the Northern Kingdoms, both of
                    which he lost. Emhyr var Emreis was an intelligent and brilliant ruler. He chose his people well and
                    crushed many plots against him. He was ruthless toward traitors and moved towards his goals with
                    great determination.

                    He was publicly favorable to the Elder Races, in stark contrast to monarchs of the North.
    '),
            ('gaunterodimm', 'Gaunter O''Dimm', 'Gaunter O''Dimm, sometimes called Master Mirror or Man of Glass, was a powerful individual, creating pacts with people in exchange for their souls and being able to control time with a mere clap of his hands.')
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM article WHERE name IN ('geraltofrivia', 'yenneferofvengerberg', 'emhyrvaremreis', 'gaunterodimm')");

    }
}
