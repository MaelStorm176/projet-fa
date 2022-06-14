<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\ProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('profile_picture')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('profile_picture'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setProfilePicture($newFilename);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Your profile picture has been updated!');
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    // nouvelle route profile/menu
    #[Route('/profile/menu', name: 'profile_menu')]
    public function seeMenu(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $menu = $user->getMonMenu();
        if ($menu !== null) {
            $menu2 = $menu->getMenuSave();

            $date_menu = $menu->getDateGenerate();
            $now = new \DateTime();
            $diff = $now->diff($date_menu);

            if ($diff->d > count($menu2[0])) {
                $user->setMonMenu(null);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
            if (isset($menu)) {
                $recipes = [];
                $repository = $this->getDoctrine()->getRepository(Recipe::class);
                $recipes1 = $repository->findBy(['id' => $menu2[0][$diff->d]]);
                $recipes2 = $repository->findBy(['id' => $menu2[1][$diff->d]]);
                $recipes3 = $repository->findBy(['id' => $menu2[2][$diff->d]]);
                $recipes4 = $repository->findBy(['id' => $menu2[3][$diff->d]]);
                $recipes5 = $repository->findBy(['id' => $menu2[4][$diff->d]]);
                $recipes6 = $repository->findBy(['id' => $menu2[5][$diff->d]]);
                array_push($recipes, $recipes1, $recipes2, $recipes3, $recipes4, $recipes5, $recipes6);
            }
            $none = 0;
            return $this->render('profile/menu.html.twig', [
                "recette" => $recipes,
                "test" => $none,
            ]);
        }else{
            $none = 1;
            return $this->render('profile/menu.html.twig', [
                "test" => $none,
            ]);
        }

    }
}
