<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\User;

class RegisterController extends AbstractController
{

    /**
     * @Route("/", name="register")
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
                               
                // if ((!($pass==$pass2)) || empty($pass))
                // {
                //     $error="Hasła nie są takie same!";
                //     $errorCode=1;
                // }

                // if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                // {
                //     $error="Niepoprawny adres email";
                //     $errorCode=1;
                // }

                // if (empty($username))
                // {
                //     $error="Podaj nick!";
                //     $errorCode=1;
                // }
               
                if ($errorCode==0)
                {
                    $query = $this->getDoctrine()
                    ->getRepository(User::class)->createQueryBuilder('u')
                    ->select('u.username', 'u.email')
                    ->andWhere('u.username = :username OR u.email = :email')
                    ->setParameter('email', $email)
                    ->setParameter('username', $username)
                    ->getQuery();

                    $result = $query->getResult();
                    
                    if ($result) {
                        $errorCode=1;
                        foreach ($result as $row) {

                            if($email==$row['email'])
                            $error="Adres email już istnieje";

                            if($username==$row['username'])
                            $error="Użytkownik już istnieje";
                        }
                    } 
                    else
                    {

                        //salt
                        $token=md5(uniqid());
                        
                        // set user
                        $user = new User();
                        $user->setPassword($pass);
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
  
        return $this->render('register/index.html.twig', array(
            'errorCode' => $errorCode,
            'error' => $error,
            '_username' => $username,
            '_email' => $email,

        ));
    }


     /**
     * @Route("/register-after", name="registerAfter")
     */
    public function afterRegister()
    {      
        //$data1 = $request->get('email');  
        $session = new Session();
        $token=$session->get('token');
        $email=$session->get('email');
        $username=$session->get('username');
       
        $session->set('token', '0');
        $session->set('username', '0');
        $session->set('email', '0');
        
        return $this->render('register/indexAfter.html.twig', array(
           'namePage' => 'After Register',
           'redirect' => $token,
           'email' => $email,
           'username' => $username,
        ));
    }

    /**
    * @Route("/active-account", name="active-account")
    */
    public function activeAccount(ObjectManager $manager, Request $request)
    {      
        $token = $request->get('token');  
        
        $query = $this->getDoctrine()->getRepository(User::class);
        $user = $query->findOneBy(['activeTokenMail' => $token]);
            
        if(!$user)
        {
            return $this->render('register/index.html.twig', [ 
                'namePage' => 'Syssitia App',
            ]);
        } 
        else
        {
            $user->setIsActive(true);
            $manager->flush();
        }
       
        return $this->render('register/indexActive.html.twig', array(
           'namePage' => 'Active Msg',
        ));
    }
}