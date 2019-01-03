<?php

namespace App\Controller;
use PHPUnit\Framework\TestCase;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class TddControllerLoginTest extends KernelTestCase
{

     /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;


     public function setUp()
    {
        $this->user = new User();
        $this->user->setUsername("mariusz");
        $this->user->setEmail("najwer23@gmail.com");
        $this->user->setPassword("tajnehaslo");
        $this->user->setIsActive(1);
        $this->user->setActiveTokenMail('456ff');


        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /** @test */
    public function testTddControllerLogin()
    {
        $this->assertFileExists('src/Controller/TddControllerLogin.php');
    }

    public function testUserEntity()
    {
        $this->assertFileExists('src/Entity/User.php');
    }

    public function testAtribute()
    {
        $this->assertClassHasAttribute('encoder', TddControllerLogin::class);
    }
   
    

}
