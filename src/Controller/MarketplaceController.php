<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/marketplace')]
class MarketplaceController extends AbstractController
{
    #[Route('/', name: 'app_marketplace')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $category = $request->query->get('category', '');
        
        if ($search || $category) {
            $products = $productRepository->findBySearchAndCategory($search, $category);
        } else {
            $products = $productRepository->findBy([], ['createdAt' => 'DESC']);
        }
        
        return $this->render('marketplace/index.html.twig', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
        ]);
    }

    #[Route('/new', name: 'app_marketplace_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, \App\Service\CloudinaryService $cloudinaryService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    $result = $cloudinaryService->uploadFile($imageFile->getRealPath(), 'bricolage_products');

                    if ($result['success']) {
                        $product->setImage($result['url']);
                    } else {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image : ' . ($result['error'] ?? 'Inconnue'));
                    }
                } catch (\Exception $e) {
                     $this->addFlash('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
                }
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Votre annonce a été publiée !');

            return $this->redirectToRoute('app_marketplace');
        }

        return $this->render('marketplace/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_marketplace_show', requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        return $this->render('marketplace/show.html.twig', [
            'product' => $product,
        ]);
    }
}
