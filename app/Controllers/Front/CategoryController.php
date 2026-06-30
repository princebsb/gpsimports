<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class CategoryController extends BaseController
{
    public function show($slug)
    {
        $categoryModel = model('CategoryModel');
        $category = $categoryModel->getBySlug($slug);

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filters = [
            'category_id' => $category['id'],
            'brand_id' => $this->request->getGet('marca'),
            'min_price' => $this->request->getGet('preco_min'),
            'max_price' => $this->request->getGet('preco_max'),
            'on_sale' => $this->request->getGet('promocao'),
            'sort' => $this->request->getGet('ordenar') ?? 'newest',
        ];

        $productService = service('product');
        $result = $productService->getFilteredProducts($filters, 20);

        $subcategories = $categoryModel->getSubcategories($category['id']);
        $categoryBreadcrumb = $categoryModel->getBreadcrumb($category['id']);
        $brands = model('BrandModel')->getByCategory($category['id']);

        return view('front/categories/show', [
            'title' => $category['name'],
            'category' => $category,
            'subcategories' => $subcategories,
            'categoryBreadcrumb' => $categoryBreadcrumb,
            'products' => $result['products'],
            'pager' => $result['pager'],
            'brands' => $brands,
            'filters' => $filters,
        ]);
    }
}
