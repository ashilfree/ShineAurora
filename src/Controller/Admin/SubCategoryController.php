<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SubCategoryController extends AbstractController
{

    /**
     * @Route("/get-sub-category/{id}", name="get.sub.category")
     * @param Category $category
     * @param SubCategoryRepository $subCategoryRepository
     * @return JsonResponse
     */
    public function getSubCategory(Category $category, SubCategoryRepository $subCategoryRepository): JsonResponse
    {
        $subCategories = $subCategoryRepository->findBy(['category' => $category]);
        $categories = [];
        foreach ($subCategories as $subCategory) {
            $categories[] = [
                'id'=> $subCategory->getId(),
                'text' => $subCategory->getName()
            ];
        }
        return new JsonResponse($categories);
    }
}