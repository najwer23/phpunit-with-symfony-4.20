<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


use App\Entity\User;

class TddController extends AbstractController
{

    public function checkUsername($username, $error)
    {
        if (strlen($username)>20)
            $error="Nick musi być krótszy niż 20 znaków!";
    
        if (preg_match("/[AĄBCĆDEĘFGHIJKLŁMNŃOÓPRSŚTUWYZŹŻ]/", $username)===0) 
            $error="Nick musi mieć dużą literę!";
        
        if (ctype_alpha($username))
            $error="Nick musi mieć liczbę!";

        if (strlen($username)<=3)
            $error="Nick musi być dłuższy niż 3 znaki!";
        
        if (empty($username))
            $error="Uzupełnij pole Nick";

        return $error;
    }

    public function checkEmail($email, $error)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $error="Niepoprawny adres email";
        
        return $error;
    }

    public function checkRegulations($regulations, $error)
    {
        if (!$regulations)
            $error="Zaakceptuj regulamin!";
        
        return $error;
    }

    public function checkPass($pass, $pass2, $error)
    {        
        if (preg_match("/[AĄBCĆDEĘFGHIJKLŁMNŃOÓPRSŚTUWYZŹŻ]/", $pass)===0) 
            $error="Hasło musi mieć dużą literę!";
        
        if (ctype_alpha($pass))
            $error="Hasło musi mieć liczbę!";
        
        if (strlen($pass)>20)
            $error="Hasło musi być krótszy niż 20 znaków!";
        
        if (strlen($pass)<=3)
            $error="Hasło musi być dłuższy niż 3 znaki!";
        
        if (!($pass==$pass2) || empty($pass))
            $error = "Hasła nie są takie same!";

        return $error;
    }

    public function errorCode($error, $errorCode)
    {
        if ($error != "ok")
            $errorCode = 1;

        return $errorCode;
    }

    public function checkUsernameAndEmailInEntity($email, $username, $result, $error)
    {
        foreach ($result as $row) {

            if($email==$row['email'])
                $error="Adres email już istnieje";

            if($username==$row['username'])
                $error="Użytkownik już istnieje";
        }

        return $error;
    }

    public function getQueryWithUsernameAndEmailIfExist ($email, $username)
    {
        $query = $this->getDoctrine()
                      ->getRepository(User::class)->createQueryBuilder('u')
                      ->select('u.username', 'u.email')
                      ->andWhere('u.username = :username OR u.email = :email')
                      ->setParameter('email', $email)
                      ->setParameter('username', $username)
                      ->getQuery();

        $result = $query->getResult();
        return $result;
    }

    /**
     * @Route("/tdd", name="registertdd")
     */
    public function register(ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        $username="";
        $email="";
        
        $errorCode=0;
        $error="ok";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['email'])) {
                
                $username=$_POST["username"];
                $email=$_POST["email"];
                $pass=$_POST["pass"];
                $pass2=$_POST["pass2"];
        
                if(!empty($_POST['regulations']))
                    $regulations=$_POST['regulations'];
                else
                    $regulations = 0;

                
                $error = $this->checkRegulations($regulations, $error);
                $errorCode = $this->errorCode($error, $errorCode);
                    
                $error = $this->checkPass($pass, $pass2, $error);
                $errorCode = $this->errorCode($error, $errorCode);

                $error = $this->checkEmail($email, $error);
                $errorCode = $this->errorCode($error, $errorCode);

                $error = $this->checkUsername($username, $error);
                $errorCode = $this->errorCode($error, $errorCode);

                

                if ($errorCode==0)
                {
                    $result = $this->getQueryWithUsernameAndEmailIfExist ($email, $username);
                    
                    if ($result) 
                    {
                        $error = $this->checkUsernameAndEmailInEntity($email, $username, $result, $error);
                        $errorCode = $this->errorCode($error, $errorCode);
                    } 
                    else
                    {

                        //salt
                        $token=md5(uniqid());
                        
                        // set user
                        $user = new User();
                        $encoded = $passwordEncoder->encodePassword($user, $pass);
                        $user->setPassword($encoded);
                        $user->setUsername($username);
                        $user->setEmail($email);
                        $user->setIsActive('0');
                        $user->setActiveTokenMail($token);

                        //set to database
                        $manager->persist($user);
                        $manager->flush();
                        
                        //set session
                        $session = new Session();
                        $session->set('token', '42');
                        $session->set('email', $email);
                        $session->set('username', $username);

                        //send email with active link
                        $body="Wiadomość automatyczna. Proszę na nią nie odpowiadać. Aktywuj konto: "."http://rejestracja/active-account?token=".$token."";
                        $message = (new \Swift_Message("Aktywacja konta w serwisie"))
                          ->setFrom(['syssitiaapp@gmail.com' => 'Rejestracja'])
                          ->setTo($email)
                          ->setBody($body);
                        $mailer->send($message);  
                      
                        return $this->redirectToRoute('registerAfter', array(
                                // 'tokenRegisterAfter' => '42',
                        ));
                    }  
                }
            }
        }
  
        return $this->render('tdd/index.html.twig', array(
            'errorCode' => $errorCode,
            'error' => $error,
            '_username' => $username,
            '_email' => $email,

        ));
    }
}