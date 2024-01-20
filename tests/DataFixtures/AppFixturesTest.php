<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\Product;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppFixturesTest extends KernelTestCase
{
    private ?EntityManagerInterface $manager = null;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $this->manager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->executeFixtures([new AppFixtures()]);
    }

    public function testAppFixtures(): void
    {
        if (!is_null($this->manager)) {
            $products = $this->manager->getRepository(Product::class)->findAll();
            $this->assertCount(10, $products);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (!is_null($this->manager)) {
            $this->manager->close();
            $this->manager = null;
        }
    }

    /**
     * @param array<AppFixtures> $fixtures
     */
    private function executeFixtures(array $fixtures): void
    {
        if (!is_null($this->manager)) {
            $purger = new ORMPurger();
            $executor = new ORMExecutor($this->manager, $purger);

            $executor->execute($fixtures);
        }
    }
}
