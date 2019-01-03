<?php

namespace App\Controller;
use PHPUnit\Framework\TestCase;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class TddControllerRegisterTest extends KernelTestCase
{

     /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        $this->register = new TddControllerRegister();
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
    public function testTddController()
    {
        $this->assertFileExists('src/Controller/TddControllerRegister.php');
    }



    public function testUserEntity()
    {
        $this->assertFileExists('src/Entity/User.php');
    }

    public function testId()
    {
        $this->assertObjectHasAttribute('id', new User);
        $this->assertEquals($this->user->getId(), 0);
    }

    public function testUsername()
    {
        $this->assertObjectHasAttribute('username', new User);
        $this->assertEquals($this->user->getUsername(), 'mariusz');
    }

    public function testEmail()
    {
        $this->assertObjectHasAttribute('email', new User);
        $this->assertEquals($this->user->getEmail(), 'najwer23@gmail.com');
    }
   
    public function testRole()
    {
        $this->assertEquals($this->user->getRoles(), ['ROLE_USER']);
    }

    public function testPassword()
    {
        $this->assertObjectHasAttribute('password', new User);
        $this->assertEquals($this->user->getPassword(), 'tajnehaslo');
    }

    public function testIsActive()
    {
        $this->assertObjectHasAttribute('isActive', new User);
        $this->assertEquals($this->user->getIsActive(), 1);
    }

    public function testActiveTokenMail()
    {
        $this->assertObjectHasAttribute('activeTokenMail', new User);
        $this->assertEquals($this->user->getActiveTokenMail(), '456ff');
    }




    public function testErrorCode()
    {
        $result = $this->register->errorCode("inny niż ok", 1);
        $this->assertEquals($result, 1);

        $result = $this->register->errorCode('ok', 1);
        $this->assertEquals($result, 1);

        $result = $this->register->errorCode('ok', 0);
        $this->assertEquals($result, 0);
    }

    public function testCheckPass()
    {
        // if all ok
        $result = $this->register->checkPass("poprawneA6", "poprawneA6", 'ok');
        $this->assertEquals($result, 'ok');

        // if they are not the same
        $result = $this->register->checkPass("inneA6", "poprawne", 'ok');
        $this->assertEquals($result, 'Hasła nie są takie same!');

        // length >= 3
        $result = $this->register->checkPass("po", "po", 'ok');
        $this->assertEquals($result, 'Hasło musi być dłuższy niż 3 znaki!');

        // length < 20
        $result = $this->register->checkPass("poppccjdskcjkcjdskcjkcjkcj", "poppccjdskcjkcjdskcjkcjkcj", 'ok');
        $this->assertEquals($result, 'Hasło musi być krótszy niż 20 znaków!');

        // number in pass
        $result = $this->register->checkPass("popp", "popp", 'ok');
        $this->assertEquals($result, 'Hasło musi mieć liczbę!');

        // upercase in pass
        $result = $this->register->checkPass("popp8", "popp8", 'ok');
        $this->assertEquals($result, 'Hasło musi mieć dużą literę!');
    }

    public function testcheckEmail()
    {
        //check no .
        $result = $this->register->checkEmail("najwer@gmailcom", "ok");
        $this->assertEquals($result, 'Niepoprawny adres email');

        //check no @
        $result = $this->register->checkEmail("najwergmail.com", "ok");
        $this->assertEquals($result, 'Niepoprawny adres email');

        //good test
        $result = $this->register->checkEmail("najwer23@gmail.com", "ok");
        $this->assertEquals($result, 'ok');
    }


    public function testCheckUsername()
    {
        //empty Nick
        $result = $this->register->checkUsername("",'ok');
        $this->assertEquals($result, 'Uzupełnij pole Nick');

        // too short nick
        $result = $this->register->checkUsername("ma", 'ok');
        $this->assertEquals($result, 'Nick musi być dłuższy niż 3 znaki!');

        // username should have number
        $result = $this->register->checkUsername("mariuszand", 'ok');
        $this->assertEquals($result, 'Nick musi mieć liczbę!');

        // username should have uppercase
        $result = $this->register->checkUsername("mariuszand23", 'ok');
        $this->assertEquals($result, 'Nick musi mieć dużą literę!');

        // length username should have less than 20 chars, tested Polish letter
        $result = $this->register->checkUsername("mariuszand23Ędddddddddddd", 'ok');
        $this->assertEquals($result, 'Nick musi być krótszy niż 20 znaków!');

    }

    public function testCheckUsernameAndEmailInEntity()
    {
       
        // if all ok
        $items[0] = array();
        $items[0]['username'] = 'marek';
        $items[0]['email'] = 'john@example.com';
        $result = $this->register->checkUsernameAndEmailInEntity("najwer23@gmail.com", "mariusz", $items, 'ok');
        $this->assertEquals($result, 'ok');

        // if user exist
        $items[0] = array();
        $items[0]['username'] = 'mariusz';
        $items[0]['email'] = 'john@example.com';
        $result = $this->register->checkUsernameAndEmailInEntity("najwer23@gmail.com", "mariusz", $items, 'ok');
        $this->assertEquals($result, 'Użytkownik już istnieje');
        
        // if email exist
        $items[0] = array();
        $items[0]['username'] = 'mmmm';
        $items[0]['email'] = 'najwer23@gmail.com';
        $result = $this->register->checkUsernameAndEmailInEntity("najwer23@gmail.com", "mariusz", $items, 'ok');
        $this->assertEquals($result, 'Adres email już istnieje');
    }

    public function testCheckRegulations()
    {
        // if checkbox  regulations clicked
        $result = $this->register->checkRegulations("1", 'ok');
        $this->assertEquals($result, 'ok');

        // if not
        $result = $this->register->checkRegulations(0, 'ok');
        $this->assertEquals($result, 'Zaakceptuj regulamin!');
    }

    // database test
    // public function testgetQueryWithUsernameAndEmailIfExist()
    // {
    //     $query = $this->entityManager
    //                    ->getRepository(User::class)->createQueryBuilder('u')
    //                    ->select('u.username', 'u.email')
    //                    ->andWhere('u.username = :username OR u.email = :email')
    //                    ->setParameter('email', "m")
    //                    ->setParameter('username', "mariusz")
    //                    ->getQuery();

    //     $result = $query->getResult();
    //     // $this->assertEquals("mariusz", $result[0]['username']);
    // }


}
