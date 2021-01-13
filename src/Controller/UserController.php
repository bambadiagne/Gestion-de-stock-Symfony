<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $session = $request->getSession();
        $name = $session->get('name');
       
        return $this->render('user/index.html.twig', [
            'name' => $name,
            'users' => $userRepository->findAll(),
        ]);
    }
    /**
     * @Route("/dash", name="dashboard")
     */
    public function dash(Request $request, CommandeRepository $order, ProduitRepository $product,ClientRepository $client)
    {
        $session = $request->getSession();
        $name = $session->get('name');
        $numberOrder = count($order->findAll());
        $stocknumber = 0;
        $allProducts = $product->findAll();
        $allClients=sizeof($client->findAll());
        for ($i = 0; $i < count($allProducts); $i++) {
            $stocknumber = $stocknumber + $allProducts[$i]->getStock();
        }
        return $this->render('user/dashboard.html.twig', [
            'name' => $name,
            'numberOrder' => $numberOrder,
            'stockNumber' => $stocknumber,
            'allClients' => $allClients,
        ]);
    }
    /**
     * @Route("/dash/show", name="dashboard_show")
     */
    public function dashShow(Request $request, CommandeRepository $orderRepository): Response
    {

        $productStats = array();
        $orderStats = array();
        $em = $this->getDoctrine()->getRepository(Produit::class);
        $allProducts = $em->findAll();
        for ($i = 0; $i < count($allProducts); $i++) {
            $productStats[$allProducts[$i]->getLibelle()] = count($em->findByLibelle($allProducts[$i]->getLibelle()));
        }


        for ($i = 1; $i < 13; $i++) {
            $orderMonth = count($orderRepository->findByDate((int) date('Y'), (int) $i));


            $orderStats[(int) $i] = $orderMonth;
        }
        arsort($productStats);
        $productStats = array_slice($productStats, 0, 5);
        return $this->json(["labelsOrder" => ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"], "dataOrder" => array_values($orderStats), "labelsProducts" => array_keys($productStats), "dataProducts" => array_values($productStats)], 200);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $session = $request->getSession();
        $name = $session->get('name');
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPwd(password_hash($user->getPwd(), PASSWORD_DEFAULT));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user, 'name' => $name,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="user_login", methods={"GET","POST"})
     */
    public function login(Request $request, UserRepository $userRepository, CommandeRepository $order, ProduitRepository $product,ClientRepository $clientRepository): Response
    {
        $session = $request->getSession();
        $session->clear();
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('login', TextType::class, [
                'attr' => [
                    'placeholder' => 'Taper votre login',
                ],

            ])
            ->add('pwd', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Taper votre Password',
                ],

            ])

            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pwd   = $user->getPwd();
            $login = $user->getLogin();
            $user1 = $userRepository->findOneBy(array('login' => $login));
            if (!$user1 || !password_verify($pwd, $user1->getPwd())) {
                $this->get('session')->getFlashBag()->add(
                    'info',
                    'Login Incorrecte Vérifier Votre Login  ....'
                );
            } else {
                if (!$session->has('name')) {
                    $session->set('name', $user1->getUserName());
                    $name = $session->get('name');
                    $numberOrder = count($order->findAll());
                    $stocknumber = 0;
                    $allProducts = $product->findAll();
                    $allClients=$clientRepository->findAll();
                    for ($i = 0; $i < count($allProducts); $i++) {
                        $stocknumber = $stocknumber + $allProducts[$i]->getStock();
                    }

                    return $this->render('user/dashboard.html.twig', [
                        'name' => $name,
                        'allClients'=>count($allClients),
                        'numberOrder' => $numberOrder,
                        'stockNumber' => $stocknumber,

                    ]);
                }
            }
        }

        return $this->render('user/login.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/logout", name="user_logout", methods={"GET"})
     */
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirectToRoute('user_login');
    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPwd(password_hash($user->getPwd(), PASSWORD_DEFAULT));
    
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
