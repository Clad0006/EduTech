<?php


namespace App\Tests\Controller\Offre;

use App\Factory\EntrepriseFactory;
use App\Factory\OffreFactory;
use App\Factory\TypeFactory;
use App\Tests\Support\ControllerTester;
class IndexCest
{
    public function _before(ControllerTester $I)
    {
        EntrepriseFactory::createMany(10);
        TypeFactory::createOne([
            'libelle' => 'STAGE',
        ]);
        TypeFactory::createOne([
            'libelle' => 'ALTERNANCE',
        ]);
        OffreFactory::createMany(20);
    }

    // tests
    public function testOffrePage(ControllerTester $I): void
    {
        $I->amOnPage('/offre');
        $I->seeResponseCodeIs(200);
        $I->see('Offres', 'title');
        $I->see('LISTE DES OFFRES', '.titre_offre h1');

    }
}
